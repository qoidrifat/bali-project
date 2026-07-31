<?php
require_once __DIR__ . '/_helpers.php';

auth_start_session();

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!auth_check()) {
    http_response_code(401);
    echo json_encode([
        'ok' => false,
        'count' => 0,
        'message' => 'Unauthenticated',
    ]);
    exit;
}

$connection = db_connect();

if (!$connection || !booking_schema_ready($connection)) {
    http_response_code(503);
    echo json_encode([
        'ok' => false,
        'count' => 0,
        'message' => 'Booking schema unavailable',
    ]);
    exit;
}

$user = auth_user();
$count = booking_count_for_user($connection, $user['id'] ?? 0);

echo json_encode([
    'ok' => true,
    'count' => $count,
    'synced_at' => date(DATE_ATOM),
]);
