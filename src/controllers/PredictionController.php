<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\MatchManager;
use App\Managers\PredictionManager;

final class PredictionController extends AbstractController
{
    public function index(): void
    {
        $this->requireAuth();

        $matchManager = new MatchManager();
        $predictionManager = new PredictionManager();

        $userId = (int) ($_SESSION['user']['user_id'] ?? 0);

        $this->render('predictions', [
            'matches' => $matchManager->findUpcoming(),
            'userPredictions' => $predictionManager->findByUser($userId),
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->requireValidCsrfToken('/predictions');

        $userId = (int) ($_SESSION['user']['user_id'] ?? 0);
        $matchId = (int) ($_POST['match_id'] ?? 0);
        $predictionResult = $_POST['prediction_result'] ?? '';

        if ($matchId <= 0 || !in_array($predictionResult, ['1', 'N', '2'], true)) {
            $this->setFlash('error', 'Pronostic invalide.');
            $this->redirect('/predictions');
        }

        $predictionManager = new PredictionManager();

        try {
            $predictionManager->saveOrUpdate($userId, $matchId, $predictionResult);
            $this->setFlash('success', 'Pronostic enregistré.');
        } catch (\Throwable $e) {
            $this->setFlash('error', $e->getMessage());
        }

        $this->redirect('/predictions');
    }
}
