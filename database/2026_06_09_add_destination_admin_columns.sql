-- Optional manual migration for admin destination CRUD.
-- Review and run manually before using the admin disable/soft-delete feature.
-- Existing public destination data is preserved.

ALTER TABLE destination
  ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1 AFTER gambar,
  ADD COLUMN IF NOT EXISTS deleted_at DATETIME NULL DEFAULT NULL AFTER is_active,
  ADD COLUMN IF NOT EXISTS created_at TIMESTAMP NULL DEFAULT NULL AFTER deleted_at,
  ADD COLUMN IF NOT EXISTS updated_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER created_at;

UPDATE destination
SET is_active = 1
WHERE is_active IS NULL;
