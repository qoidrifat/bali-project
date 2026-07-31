-- Manual SQL for promoting an existing user account to admin.
-- 1. Register a normal user from register.php first.
-- 2. Replace admin@example.com with the real email address.
-- 3. Review before running. Do not run this blindly on production.

UPDATE users
SET role_id = (SELECT id FROM roles WHERE name = 'admin' LIMIT 1)
WHERE email = 'admin@example.com';
