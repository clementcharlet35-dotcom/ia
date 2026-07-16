CREATE DATABASE IF NOT EXISTS pronostic_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE pronostic_db;

DROP TABLE IF EXISTS predictions;
DROP TABLE IF EXISTS matches;
DROP TABLE IF EXISTS teams;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') NOT NULL DEFAULT 'user',
    points INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE teams (
    team_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    flag VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE matches (
    match_id INT AUTO_INCREMENT PRIMARY KEY,
    team1_id INT NOT NULL,
    team2_id INT NOT NULL,
    match_date DATETIME NOT NULL,
    result_team1 INT NULL,
    result_team2 INT NULL,
    description VARCHAR(255) NULL,
    result ENUM('1', 'N', '2') NULL,
    status ENUM('scheduled', 'finished', 'cancelled') NOT NULL DEFAULT 'scheduled',
    api_provider VARCHAR(50) NULL DEFAULT 'football-data',
    api_match_id VARCHAR(100) NULL,
    result_check_120_at DATETIME NULL,
    result_check_150_at DATETIME NULL,
    result_check_180_at DATETIME NULL,
    result_sync_error VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_matches_team1 FOREIGN KEY (team1_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_matches_team2 FOREIGN KEY (team2_id) REFERENCES teams(team_id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT chk_different_teams CHECK (team1_id <> team2_id)
);

CREATE TABLE predictions (
    prediction_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    match_id INT NOT NULL,
    prediction_result ENUM('1', 'N', '2') NOT NULL,
    validated TINYINT(1) NOT NULL DEFAULT 0,
    correct TINYINT(1) NULL,
    points_earned INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_predictions_user FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_predictions_match FOREIGN KEY (match_id) REFERENCES matches(match_id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT uq_user_match UNIQUE (user_id, match_id)
);

INSERT INTO users (username, email, password_hash, role, points) VALUES
('admin', 'admin@pronostics.test', '$2y$10$Alce4nkVJv4fM5EwJmGMeu1aGkU0wbZdS1YB0Sx6x6mUnrJpS1L5u', 'admin', 0),
('john', 'john@pronostics.test', '$2y$10$M0P.ojU2mB6z6v1qjP0I6uuX4M/5n6TqBv7vPCv2SxQdQY.6Q0E2m', 'user', 0);

INSERT INTO teams (name, flag) VALUES
('France', 'france.png'),
('Brésil', 'brazil.png'),
('Argentine', 'argentina.png'),
('Espagne', 'spain.png');

INSERT INTO matches (team1_id, team2_id, match_date, description, status) VALUES
(1, 2, '2026-06-15 20:00:00', 'Phase de groupes', 'scheduled'),
(3, 4, '2026-06-16 18:00:00', 'Phase de groupes', 'scheduled');
