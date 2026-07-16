<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\MatchManager;
use App\Managers\ResultSyncSchemaManager;
use App\Services\ResultSyncService;

final class AdminController extends AbstractController
{
    public function index(): void
    {
        $this->requireAdmin();

        (new ResultSyncSchemaManager())->ensureColumns();
        $matchManager = new MatchManager();

        $this->render('admin', [
            'teams' => $matchManager->findTeams(),
            'matches' => $matchManager->findAll(),
        ]);
    }

    public function storeMatch(): void
    {
        $this->requireAdmin();
        $this->requireValidCsrfToken('/admin');

        $team1Id = (int) ($_POST['team1_id'] ?? 0);
        $team2Id = (int) ($_POST['team2_id'] ?? 0);
        $matchDate = trim($_POST['match_date'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $apiMatchId = trim($_POST['api_match_id'] ?? '');

        if ($team1Id <= 0 || $team2Id <= 0 || $team1Id === $team2Id || $matchDate === '') {
            $this->setFlash('error', 'Donnees du match invalides.');
            $this->redirect('/admin');
        }

        (new ResultSyncSchemaManager())->ensureColumns();
        $matchManager = new MatchManager();
        $matchManager->create($team1Id, $team2Id, $matchDate, $description, $apiMatchId);

        $this->setFlash('success', 'Match ajoute.');
        $this->redirect('/admin');
    }

    public function deleteMatch(): void
    {
        $this->requireAdmin();
        $this->requireValidCsrfToken('/admin');

        $matchId = (int) ($_POST['match_id'] ?? 0);

        if ($matchId <= 0) {
            $this->setFlash('error', 'Match invalide.');
            $this->redirect('/admin');
        }

        $matchManager = new MatchManager();
        $matchManager->delete($matchId);

        $this->setFlash('success', 'Match supprime.');
        $this->redirect('/admin');
    }

    public function syncResults(): void
    {
        $this->requireAdmin();
        $this->requireValidCsrfToken('/admin');

        $summary = (new ResultSyncService())->syncDueMatches(true);
        $checked = (int) ($summary['checked'] ?? 0);
        $updated = (int) ($summary['updated'] ?? 0);

        if (!($summary['success'] ?? false)) {
            $this->setFlash('error', (string) ($summary['message'] ?? 'Synchronisation impossible.'));
            $this->redirect('/admin');
        }

        if (!empty($summary['errors'])) {
            $this->setFlash('error', "Synchronisation terminee avec erreurs : {$checked} match(s) verifie(s), {$updated} resultat(s) mis a jour.");
            $this->redirect('/admin');
        }

        $this->setFlash('success', "Synchronisation terminee : {$checked} match(s) verifie(s), {$updated} resultat(s) mis a jour.");
        $this->redirect('/admin');
    }
}
