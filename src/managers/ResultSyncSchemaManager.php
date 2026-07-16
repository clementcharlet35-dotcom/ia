<?php

declare(strict_types=1);

namespace App\Managers;

final class ResultSyncSchemaManager extends AbstractManager
{
    public function ensureColumns(): void
    {
        $columns = $this->getMatchColumns();
        $definitions = [
            'api_provider' => 'ADD COLUMN api_provider VARCHAR(50) NULL DEFAULT "football-data"',
            'api_match_id' => 'ADD COLUMN api_match_id VARCHAR(100) NULL',
            'result_check_120_at' => 'ADD COLUMN result_check_120_at DATETIME NULL',
            'result_check_150_at' => 'ADD COLUMN result_check_150_at DATETIME NULL',
            'result_check_180_at' => 'ADD COLUMN result_check_180_at DATETIME NULL',
            'result_sync_error' => 'ADD COLUMN result_sync_error VARCHAR(255) NULL',
        ];

        foreach ($definitions as $column => $definition) {
            if (!in_array($column, $columns, true)) {
                $this->pdo->exec('ALTER TABLE matches ' . $definition);
            }
        }
    }

    private function getMatchColumns(): array
    {
        $stmt = $this->pdo->query('SHOW COLUMNS FROM matches');

        return array_map(
            static fn (array $column): string => (string) $column['Field'],
            $stmt->fetchAll()
        );
    }
}
