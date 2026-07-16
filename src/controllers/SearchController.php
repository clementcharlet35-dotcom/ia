<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\MatchManager;

final class SearchController extends AbstractController
{
    public function index(): void
    {
        $query = trim((string) ($_GET['q'] ?? ''));
        $results = [];

        if ($query !== '') {
            $matchManager = new MatchManager();
            $results = $matchManager->searchTeamsWithMatches($query);
        }

        $this->render('search', [
            'query' => $query,
            'results' => $results,
        ]);
    }
}
