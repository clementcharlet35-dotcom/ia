<?php

declare(strict_types=1);

namespace App\Models;

final class User
{
    public function __construct(
        public int $userId,
        public string $username,
        public string $email,
        public string $role,
        public int $points,
        public string $createdAt
    ) {
    }
}
