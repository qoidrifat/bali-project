<?php
require_once __DIR__ . '/_helpers.php';

auth_start_session();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$old = [
    'service_type' => trim($_POST['service_type'] ?? ''),
    'origin_label' => trim($_POST['origin_label'] ?? ''),
    'destination_label' => trim($_POST['destination_label'] ?? ''),
    'start_date' => trim($_POST['start_date'] ?? ''),
    'end_date' => trim($_POST['end_date'] ?? ''),
    'quantity' => trim($_POST['quantity'] ?? ''),
    'customer_name' => trim($_POST['customer_name'] ?? ''),
    'customer_email' => trim($_POST['customer_email'] ?? ''),
    'customer_phone' => trim($_POST['customer_phone'] ?? ''),
    'notes' => trim($_POST['notes'] ?? ''),
];

$errors = [];

if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
    $errors[] = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
}

$validation = booking_validate_request($old);
$errors = array_merge($errors, $validation['errors']);

$connection = db_connect();

if (!$connection) {
    $errors[] = 'Sistem booking belum tersedia. Silakan coba lagi nanti.';
} elseif (!booking_schema_ready($connection)) {
    $errors[] = 'Tabel booking internal belum tersedia. Jalankan migration manual booking terlebih dahulu.';
}

if ($errors) {
    $_SESSION['booking_errors'] = array_values(array_unique($errors));
    $_SESSION['booking_old'] = $old;
    header('Location: index.php');
    exit;
}

$booking = booking_create($connection, $validation['data']);

if (!$booking) {
    $_SESSION['booking_errors'] = ['Booking belum bisa dibuat. Silakan coba lagi nanti.'];
    $_SESSION['booking_old'] = $old;
    header('Location: index.php');
    exit;
}

header('Location: confirmation.php?id=' . (int) $booking['id'] . '&token=' . urlencode($booking['public_token']));
exit;
