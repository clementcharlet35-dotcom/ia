<?php

declare(strict_types=1);

namespace App\Models;

final class Prediction
{
    public function __construct(
        public int $predictionId,
        public int $userId,
        public int $matchId,
        public string $predictionResult,
        public int $validated,
        public ?int $correct,
        public int $pointsEarned
    ) {
    }
}
