<?php

declare(strict_types=1);

namespace App\Managers;

final class UserManager extends AbstractManager
{
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function emailExists(string $email): bool
    {
        return $this->findByEmail($email) !== null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function findById(int $userId): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE user_id = :user_id LIMIT 1');
        $stmt->execute(['user_id' => $userId]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function usernameExists(string $username): bool
    {
        return $this->findByUsername($username) !== null;
    }

    public function create(string $username, string $email, string $password): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)'
        );

        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_BCRYPT),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updatePoints(int $userId, int $pointsToAdd): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET points = points + :points WHERE user_id = :user_id');
        $stmt->execute([
            'points' => $pointsToAdd,
            'user_id' => $userId,
        ]);
    }

    public function recalculateAllPoints(): void
    {
        $this->pdo->exec('UPDATE users SET points = 0');

        $sql = 'UPDATE users u
                JOIN (
                    SELECT user_id, COALESCE(SUM(points_earned), 0) AS total_points
                    FROM predictions
                    WHERE validated = 1
                    GROUP BY user_id
                ) p ON p.user_id = u.user_id
                SET u.points = p.total_points';
        $this->pdo->exec($sql);
    }

    public function findRanking(): array
    {
        $stmt = $this->pdo->query('SELECT user_id, username, email, role, points FROM users ORDER BY points DESC, username ASC');
        return $stmt->fetchAll();
    }

    public function delete(int $userId): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM users WHERE user_id = :user_id');
        $stmt->execute(['user_id' => $userId]);
    }
}
