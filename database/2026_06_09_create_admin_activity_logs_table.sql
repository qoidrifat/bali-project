-- Optional additive migration for admin activity history.
-- Safe to run after backup. It does not drop, truncate, or overwrite data.

CREATE TABLE IF NOT EXISTS admin_activity_logs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_user_id INT UNSIGNED NULL,
  action VARCHAR(120) NOT NULL,
  entity_type VARCHAR(80) NULL,
  entity_id VARCHAR(80) NULL,
  description TEXT NULL,
  ip_address VARCHAR(45) NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY admin_activity_logs_user_index (admin_user_id),
  KEY admin_activity_logs_action_index (action),
  KEY admin_activity_logs_entity_index (entity_type, entity_id),
  KEY admin_activity_logs_created_at_index (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
