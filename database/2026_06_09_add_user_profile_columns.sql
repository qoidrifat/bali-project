-- Manual migration for Bali Project user profile settings.
-- Safe for the current PHP native project. Adds optional profile columns only.

SET @current_database = DATABASE();

SET @add_phone = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE users ADD COLUMN phone VARCHAR(30) NULL AFTER email',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @current_database
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'phone'
);
PREPARE stmt FROM @add_phone;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_city = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE users ADD COLUMN city VARCHAR(120) NULL AFTER phone',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @current_database
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'city'
);
PREPARE stmt FROM @add_city;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_country = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE users ADD COLUMN country VARCHAR(120) NULL AFTER city',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @current_database
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'country'
);
PREPARE stmt FROM @add_country;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_birth_date = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE users ADD COLUMN birth_date DATE NULL AFTER country',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @current_database
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'birth_date'
);
PREPARE stmt FROM @add_birth_date;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_preferred_contact = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE users ADD COLUMN preferred_contact VARCHAR(30) NULL AFTER birth_date',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @current_database
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'preferred_contact'
);
PREPARE stmt FROM @add_preferred_contact;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_travel_style = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE users ADD COLUMN travel_style VARCHAR(80) NULL AFTER preferred_contact',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @current_database
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'travel_style'
);
PREPARE stmt FROM @add_travel_style;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @add_bio = (
  SELECT IF(
    COUNT(*) = 0,
    'ALTER TABLE users ADD COLUMN bio TEXT NULL AFTER travel_style',
    'SELECT 1'
  )
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = @current_database
    AND TABLE_NAME = 'users'
    AND COLUMN_NAME = 'bio'
);
PREPARE stmt FROM @add_bio;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
