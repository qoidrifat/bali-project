-- Manual migration for Bali Project basic authentication.
-- Run this manually after reviewing it. Do not run on production without backup.
-- This script is intentionally idempotent for the current PHP native project.
-- It also upgrades the older legacy users table when it only has:
-- id, name, email, password.

CREATE TABLE IF NOT EXISTS roles (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(50) NOT NULL,
  label VARCHAR(100) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY roles_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  role_id INT UNSIGNED NOT NULL DEFAULT 2,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY users_email_unique (email),
  KEY users_role_id_index (role_id),
  CONSTRAINT users_role_id_foreign
    FOREIGN KEY (role_id) REFERENCES roles (id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (id, name, label) VALUES
  (1, 'admin', 'Administrator'),
  (2, 'user', 'User')
ON DUPLICATE KEY UPDATE
  label = VALUES(label);

SET @current_database = DATABASE();

SET @add_role_id = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE users ADD COLUMN role_id INT UNSIGNED NOT NULL DEFAULT 2 AFTER id',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @current_database
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'role_id'
);
PREPARE stmt FROM @add_role_id;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_password_hash = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE users ADD COLUMN password_hash VARCHAR(255) NULL AFTER email',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @current_database
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'password_hash'
);
PREPARE stmt FROM @add_password_hash;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE users
SET role_id = 2
WHERE role_id IS NULL OR role_id = 0;
