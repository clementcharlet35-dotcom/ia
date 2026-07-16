<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Managers\MatchManager;

final class HomeController extends AbstractController
{
    public function index(): void
    {
        $matchManager = new MatchManager();

        $this->render('home', [
            'nextMatches' => $matchManager->findUpcoming(5),
        ]);
    }

    public function legal(): void
    {
        $this->render('legal');
    }
}
