<?php

declare(strict_types=1);

namespace App\Managers;

use App\Config\Database;
use PDO;

abstract class AbstractManager
{
    protected PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }
}
