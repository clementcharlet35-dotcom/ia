<?php

declare(strict_types=1);

namespace App\Models;

final class FootballMatch
{
    public function __construct(
        public int $matchId,
        public int $team1Id,
        public int $team2Id,
        public string $matchDate,
        public ?int $resultTeam1,
        public ?int $resultTeam2,
        public ?string $description,
        public ?string $result,
        public string $status
    ) {
    }
}
