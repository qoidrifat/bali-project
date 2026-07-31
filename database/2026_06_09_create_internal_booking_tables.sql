-- Manual migration for Bali Project internal booking system.
-- Review and run manually. This does not modify legacy search/result tables.
-- user_id is nullable and intentionally not constrained here so guest booking
-- keeps working even when the optional auth schema is not fully installed.

CREATE TABLE IF NOT EXISTS bookings (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_code VARCHAR(32) NOT NULL,
  public_token CHAR(64) NOT NULL,
  user_id INT UNSIGNED NULL,
  customer_name VARCHAR(120) NOT NULL,
  customer_email VARCHAR(190) NOT NULL,
  customer_phone VARCHAR(30) NOT NULL,
  booking_status ENUM('pending', 'confirmed', 'cancelled') NOT NULL DEFAULT 'pending',
  payment_status ENUM('unpaid', 'manual_review', 'paid') NOT NULL DEFAULT 'unpaid',
  notes TEXT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY bookings_code_unique (booking_code),
  UNIQUE KEY bookings_public_token_unique (public_token),
  KEY bookings_user_id_index (user_id),
  KEY bookings_status_index (booking_status, payment_status),
  KEY bookings_created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS booking_details (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  booking_id INT UNSIGNED NOT NULL,
  service_type ENUM('bus', 'flight', 'hotel', 'car') NOT NULL,
  service_name VARCHAR(160) NOT NULL,
  origin_label VARCHAR(120) NULL,
  destination_label VARCHAR(120) NOT NULL,
  start_date DATE NOT NULL,
  end_date DATE NULL,
  quantity INT UNSIGNED NOT NULL,
  unit_label VARCHAR(40) NOT NULL,
  unit_price DECIMAL(12,2) NOT NULL DEFAULT 0,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY booking_details_booking_id_index (booking_id),
  KEY booking_details_service_type_index (service_type),
  CONSTRAINT booking_details_booking_id_foreign
    FOREIGN KEY (booking_id) REFERENCES bookings (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
