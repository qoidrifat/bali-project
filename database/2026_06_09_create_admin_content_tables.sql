-- Optional manual migration for Bali Project admin content modules.
-- Review before running. Do not run on production without backup.
-- This file is intentionally additive: it creates missing admin-support tables
-- and does not drop, truncate, or overwrite existing project data.

CREATE TABLE IF NOT EXISTS destination_categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  slug VARCHAR(140) NOT NULL,
  description TEXT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY destination_categories_slug_unique (slug),
  KEY destination_categories_status_index (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS tickets (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  destination_id INT UNSIGNED NULL,
  name VARCHAR(160) NOT NULL,
  type VARCHAR(80) NULL,
  price DECIMAL(12,2) NOT NULL DEFAULT 0,
  quota INT UNSIGNED NULL,
  description TEXT NULL,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY tickets_destination_id_index (destination_id),
  KEY tickets_status_index (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS payments (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id INT UNSIGNED NULL,
  payment_method VARCHAR(80) NULL,
  amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  payment_status ENUM('pending', 'manual_review', 'paid', 'rejected') NOT NULL DEFAULT 'pending',
  payment_proof VARCHAR(255) NULL,
  paid_at DATETIME NULL,
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY payments_booking_id_index (booking_id),
  KEY payments_status_index (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS galleries (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  destination_id INT UNSIGNED NULL,
  title VARCHAR(160) NULL,
  image VARCHAR(255) NOT NULL,
  alt_text VARCHAR(190) NULL,
  sort_order INT NOT NULL DEFAULT 0,
  status ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY galleries_destination_id_index (destination_id),
  KEY galleries_status_sort_index (status, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS articles (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(190) NOT NULL,
  slug VARCHAR(210) NOT NULL,
  excerpt TEXT NULL,
  content LONGTEXT NULL,
  cover_image VARCHAR(255) NULL,
  status ENUM('draft', 'published', 'archived') NOT NULL DEFAULT 'draft',
  published_at DATETIME NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY articles_slug_unique (slug),
  KEY articles_status_published_index (status, published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS site_settings (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `key` VARCHAR(120) NOT NULL,
  value LONGTEXT NULL,
  type VARCHAR(40) NULL DEFAULT 'text',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY site_settings_key_unique (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
