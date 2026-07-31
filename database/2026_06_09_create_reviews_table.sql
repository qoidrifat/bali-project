-- Manual migration for destination reviews and ratings.
-- Review and run manually after auth users table exists.

CREATE TABLE IF NOT EXISTS reviews (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  destination_id INT NOT NULL,
  user_id INT UNSIGNED NOT NULL,
  rating TINYINT UNSIGNED NOT NULL,
  review_text TEXT NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY reviews_destination_user_unique (destination_id, user_id),
  KEY reviews_destination_id_index (destination_id),
  KEY reviews_user_id_index (user_id),
  KEY reviews_rating_index (rating),
  CONSTRAINT reviews_destination_id_foreign
    FOREIGN KEY (destination_id) REFERENCES destination (id_des)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT reviews_user_id_foreign
    FOREIGN KEY (user_id) REFERENCES users (id)
    ON UPDATE CASCADE
    ON DELETE CASCADE,
  CONSTRAINT reviews_rating_range
    CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
