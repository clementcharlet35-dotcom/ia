<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\UserManager;

final class LeaderboardController extends AbstractController
{
    public function index(): void
    {
        $userManager = new UserManager();

        $this->render('leaderboard', [
            'users' => $userManager->findRanking(),
        ]);
    }

    public function deleteUser(): void
    {
        $this->requireAdmin();
        $this->requireValidCsrfToken('/leaderboard');

        $userId = (int) ($_POST['user_id'] ?? 0);
        $currentUserId = (int) ($_SESSION['user']['user_id'] ?? 0);

        if ($userId <= 0) {
            $this->setFlash('error', 'Utilisateur invalide.');
            $this->redirect('/leaderboard');
        }

        if ($userId === $currentUserId) {
            $this->setFlash('error', 'Impossible de retirer votre propre compte.');
            $this->redirect('/leaderboard');
        }

        $userManager = new UserManager();
        $user = $userManager->findById($userId);

        if (!$user) {
            $this->setFlash('error', 'Utilisateur introuvable.');
            $this->redirect('/leaderboard');
        }

        if (($user['role'] ?? 'user') === 'admin') {
            $this->setFlash('error', 'Impossible de retirer un administrateur.');
            $this->redirect('/leaderboard');
        }

        $userManager->delete($userId);
        $this->setFlash('success', 'Utilisateur retiré du classement.');
        $this->redirect('/leaderboard');
    }
}
