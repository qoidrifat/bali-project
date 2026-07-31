<?php
/**
 * Local admin seeder for Bali Project PHP native.
 *
 * Optional environment variables:
 * - BALI_ADMIN_NAME
 * - BALI_ADMIN_EMAIL
 * - BALI_ADMIN_PASSWORD
 *
 * If BALI_ADMIN_PASSWORD is not provided, this script generates a new password
 * and prints it once in the terminal.
 */

require_once __DIR__ . '/../includes/auth.php';

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo 'Seeder hanya boleh dijalankan dari CLI.';
    exit(1);
}

$connection = db_connect();

if (!$connection) {
    fwrite(STDERR, "Database tidak tersedia.\n");
    exit(1);
}

if (!auth_schema_ready($connection)) {
    fwrite(STDERR, "Schema auth belum siap. Jalankan database/2026_06_09_create_auth_tables.sql terlebih dahulu.\n");
    exit(1);
}

$name = getenv('BALI_ADMIN_NAME') ?: 'Bali Project Admin';
$email = getenv('BALI_ADMIN_EMAIL') ?: 'admin@baliparadise.local';
$plainPassword = getenv('BALI_ADMIN_PASSWORD') ?: ('Admin-' . strtoupper(bin2hex(random_bytes(4))) . '!9');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fwrite(STDERR, "Email admin tidak valid.\n");
    exit(1);
}

if (strlen($plainPassword) < 8) {
    fwrite(STDERR, "Password admin minimal 8 karakter.\n");
    exit(1);
}

$roleStmt = mysqli_prepare($connection, "SELECT id FROM roles WHERE name = 'admin' LIMIT 1");

if (!$roleStmt || !mysqli_stmt_execute($roleStmt)) {
    fwrite(STDERR, "Role admin tidak bisa dibaca.\n");
    exit(1);
}

$roleResult = mysqli_stmt_get_result($roleStmt);
$role = $roleResult ? mysqli_fetch_assoc($roleResult) : null;
mysqli_stmt_close($roleStmt);

if (!$role) {
    fwrite(STDERR, "Role admin belum tersedia.\n");
    exit(1);
}

$roleId = (int) $role['id'];
$passwordHash = password_hash($plainPassword, PASSWORD_DEFAULT);

$stmt = mysqli_prepare(
    $connection,
    "INSERT INTO users (role_id, name, email, password_hash)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE
       role_id = VALUES(role_id),
       name = VALUES(name),
       password_hash = VALUES(password_hash)"
);

if (!$stmt) {
    fwrite(STDERR, "Prepare seeder admin gagal.\n");
    exit(1);
}

mysqli_stmt_bind_param($stmt, 'isss', $roleId, $name, $email, $passwordHash);
$ok = mysqli_stmt_execute($stmt);

if (!$ok) {
    fwrite(STDERR, "Seeder admin gagal dijalankan.\n");
    mysqli_stmt_close($stmt);
    exit(1);
}

mysqli_stmt_close($stmt);

echo "Admin seeder berhasil.\n";
echo "Name: {$name}\n";
echo "Email: {$email}\n";
echo "Password: {$plainPassword}\n";
echo "Role: admin\n";
