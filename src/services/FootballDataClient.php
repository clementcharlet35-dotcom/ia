<?php

declare(strict_types=1);

namespace App\Services;

final class FootballDataClient
{
    private string $token;
    private string $baseUrl;
    private string $competitionCode;
    private string $season;

    public function __construct()
    {
        $this->token = trim((string) ($_ENV['FOOTBALL_DATA_TOKEN'] ?? ''));
        $this->baseUrl = rtrim((string) ($_ENV['FOOTBALL_DATA_BASE_URL'] ?? 'https://api.football-data.org/v4'), '/');
        $this->competitionCode = trim((string) ($_ENV['FOOTBALL_DATA_COMPETITION'] ?? 'WC'));
        $this->season = trim((string) ($_ENV['FOOTBALL_DATA_SEASON'] ?? '2026'));
    }

    public function isConfigured(): bool
    {
        return $this->token !== '';
    }

    public function findFinishedScoreForMatch(array $localMatch): ?array
    {
        $remoteMatch = $this->fetchRemoteMatch($localMatch);

        if ($remoteMatch === null || ($remoteMatch['status'] ?? '') !== 'FINISHED') {
            return null;
        }

        $fullTime = $remoteMatch['score']['fullTime'] ?? [];
        $homeScore = $fullTime['home'] ?? null;
        $awayScore = $fullTime['away'] ?? null;

        if ($homeScore === null || $awayScore === null) {
            return null;
        }

        $homeName = $this->normalizeTeamName((string) ($remoteMatch['homeTeam']['name'] ?? ''));
        $awayName = $this->normalizeTeamName((string) ($remoteMatch['awayTeam']['name'] ?? ''));
        $team1Name = $this->normalizeTeamName((string) $localMatch['team1_name']);
        $team2Name = $this->normalizeTeamName((string) $localMatch['team2_name']);

        if ($this->namesMatch($homeName, $team1Name) && $this->namesMatch($awayName, $team2Name)) {
            return [
                'result_team1' => (int) $homeScore,
                'result_team2' => (int) $awayScore,
                'api_match_id' => (string) ($remoteMatch['id'] ?? ($localMatch['api_match_id'] ?? '')),
            ];
        }

        if ($this->namesMatch($homeName, $team2Name) && $this->namesMatch($awayName, $team1Name)) {
            return [
                'result_team1' => (int) $awayScore,
                'result_team2' => (int) $homeScore,
                'api_match_id' => (string) ($remoteMatch['id'] ?? ($localMatch['api_match_id'] ?? '')),
            ];
        }

        throw new \RuntimeException('Match API trouve, mais les equipes ne correspondent pas.');
    }

    private function fetchRemoteMatch(array $localMatch): ?array
    {
        $apiMatchId = trim((string) ($localMatch['api_match_id'] ?? ''));

        if ($apiMatchId !== '') {
            $payload = $this->request('/matches/' . rawurlencode($apiMatchId));

            return $payload['match'] ?? $payload;
        }

        $matchDate = new \DateTimeImmutable((string) $localMatch['match_date']);
        $query = [
            'dateFrom' => $matchDate->modify('-1 day')->format('Y-m-d'),
            'dateTo' => $matchDate->modify('+1 day')->format('Y-m-d'),
            'status' => 'FINISHED',
        ];

        if ($this->season !== '') {
            $query['season'] = $this->season;
        }

        $path = $this->competitionCode !== ''
            ? '/competitions/' . rawurlencode($this->competitionCode) . '/matches?' . http_build_query($query)
            : '/matches?' . http_build_query($query);

        $payload = $this->request($path);
        $matches = $payload['matches'] ?? [];

        foreach ($matches as $match) {
            if ($this->remoteMatchLooksLikeLocalMatch($match, $localMatch)) {
                return $match;
            }
        }

        return null;
    }

    private function remoteMatchLooksLikeLocalMatch(array $remoteMatch, array $localMatch): bool
    {
        $homeName = $this->normalizeTeamName((string) ($remoteMatch['homeTeam']['name'] ?? ''));
        $awayName = $this->normalizeTeamName((string) ($remoteMatch['awayTeam']['name'] ?? ''));
        $team1Name = $this->normalizeTeamName((string) $localMatch['team1_name']);
        $team2Name = $this->normalizeTeamName((string) $localMatch['team2_name']);

        return ($this->namesMatch($homeName, $team1Name) && $this->namesMatch($awayName, $team2Name))
            || ($this->namesMatch($homeName, $team2Name) && $this->namesMatch($awayName, $team1Name));
    }

    private function request(string $path): array
    {
        if (!$this->isConfigured()) {
            throw new \RuntimeException('FOOTBALL_DATA_TOKEN est vide.');
        }

        $url = $this->baseUrl . $path;

        if (function_exists('curl_init')) {
            $curl = curl_init($url);
            $curlOptions = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_HTTPHEADER => [
                    'X-Auth-Token: ' . $this->token,
                    'Accept: application/json',
                ],
            ];
            $caFile = dirname(__DIR__, 2) . '/config/cacert.pem';

            if (is_file($caFile)) {
                $curlOptions[CURLOPT_CAINFO] = $caFile;
            }

            curl_setopt_array($curl, $curlOptions);
            $body = curl_exec($curl);
            $statusCode = (int) curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $error = curl_error($curl);

            if ($body === false) {
                throw new \RuntimeException('Erreur API football-data.org: ' . $error);
            }
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'timeout' => 15,
                    'ignore_errors' => true,
                    'header' => "X-Auth-Token: {$this->token}\r\nAccept: application/json\r\n",
                ],
            ]);
            $body = file_get_contents($url, false, $context);
            $statusLine = $http_response_header[0] ?? 'HTTP/1.1 500';
            preg_match('/\s(\d{3})\s/', $statusLine, $matches);
            $statusCode = (int) ($matches[1] ?? 500);

            if ($body === false) {
                throw new \RuntimeException('Erreur API football-data.org.');
            }
        }

        $payload = json_decode((string) $body, true);

        if ($statusCode >= 400) {
            $message = is_array($payload) ? (string) ($payload['message'] ?? 'Erreur API') : 'Erreur API';
            throw new \RuntimeException("football-data.org HTTP {$statusCode}: {$message}");
        }

        if (!is_array($payload)) {
            throw new \RuntimeException('Reponse API invalide.');
        }

        return $payload;
    }

    private function normalizeTeamName(string $name): string
    {
        $name = mb_strtolower(trim($name), 'UTF-8');
        if (function_exists('transliterator_transliterate')) {
            $ascii = transliterator_transliterate('Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove', $name);
        } else {
            $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $name);
        }
        $name = $ascii !== false && $ascii !== null ? $ascii : $name;
        $name = preg_replace('/\b(fc|cf|national team|team)\b/', '', $name) ?? $name;
        $name = preg_replace('/[^a-z0-9]+/', ' ', $name) ?? $name;
        $name = trim((string) preg_replace('/\s+/', ' ', $name));
        $aliases = [
            'allemagne' => 'germany',
            'algerie' => 'algeria',
            'angleterre' => 'england',
            'argentine' => 'argentina',
            'australie' => 'australia',
            'belgique' => 'belgium',
            'bosnia and herzegovina' => 'bosnia herzegovina',
            'bosnia herzegovina' => 'bosnia herzegovina',
            'bresil' => 'brazil',
            'cabo verde' => 'cape verde',
            'cap vert' => 'cape verde',
            'cape verde islands' => 'cape verde',
            'colombie' => 'colombia',
            'congo dr' => 'dr congo',
            'coree du sud' => 'korea republic',
            'croatie' => 'croatia',
            'cote d ivoire' => 'ivory coast',
            'curacao' => 'curacao',
            'czech republic' => 'czechia',
            'danemark' => 'denmark',
            'dr congo' => 'dr congo',
            'ecosse' => 'scotland',
            'egypte' => 'egypt',
            'espagne' => 'spain',
            'etats unis' => 'united states',
            'italie' => 'italy',
            'japon' => 'japan',
            'maroc' => 'morocco',
            'mexique' => 'mexico',
            'pologne' => 'poland',
            'south korea' => 'korea republic',
            'pays bas' => 'netherlands',
            'republique tcheque' => 'czechia',
            'senegal' => 'senegal',
            'serbie' => 'serbia',
            'suisse' => 'switzerland',
            'tunisie' => 'tunisia',
            'turkey' => 'turkiye',
            'turquie' => 'turkiye',
        ];

        return $aliases[$name] ?? $name;
    }

    private function namesMatch(string $apiName, string $localName): bool
    {
        if ($apiName === '' || $localName === '') {
            return false;
        }

        return $apiName === $localName
            || str_contains($apiName, $localName)
            || str_contains($localName, $apiName);
    }
}
