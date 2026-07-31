-- Optional manual migration for saving contact form messages.
-- Review and run manually only after deciding to connect contact.php to the database.

CREATE TABLE IF NOT EXISTS contact_messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  topic VARCHAR(60) NOT NULL,
  message TEXT NOT NULL,
  status ENUM('new', 'read', 'archived') NOT NULL DEFAULT 'new',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY contact_messages_status_index (status),
  KEY contact_messages_created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
