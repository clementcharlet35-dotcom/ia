DELIMITER //

CREATE PROCEDURE add_match_sync_column_if_missing(
    IN column_name VARCHAR(64),
    IN column_definition TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'matches'
          AND COLUMN_NAME = column_name
    ) THEN
        SET @sql = CONCAT('ALTER TABLE matches ADD COLUMN ', column_name, ' ', column_definition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END//

DELIMITER ;

CALL add_match_sync_column_if_missing('api_provider', 'VARCHAR(50) NULL DEFAULT ''football-data''');
CALL add_match_sync_column_if_missing('api_match_id', 'VARCHAR(100) NULL');
CALL add_match_sync_column_if_missing('result_check_120_at', 'DATETIME NULL');
CALL add_match_sync_column_if_missing('result_check_150_at', 'DATETIME NULL');
CALL add_match_sync_column_if_missing('result_check_180_at', 'DATETIME NULL');
CALL add_match_sync_column_if_missing('result_sync_error', 'VARCHAR(255) NULL');

DROP PROCEDURE add_match_sync_column_if_missing;
