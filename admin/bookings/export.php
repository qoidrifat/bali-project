<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';
require_once __DIR__ . '/../../booking/_helpers.php';

require_admin('../../login.php', '../../');
$connection = db_connect();

if (!$connection || !booking_schema_ready($connection)) {
    http_response_code(503);
    echo 'Export belum tersedia.';
    exit;
}

$result = mysqli_query(
    $connection,
    "SELECT b.booking_code, b.customer_name, b.customer_email, b.customer_phone,
            b.booking_status, b.payment_status, b.created_at,
            d.service_type, d.service_name, d.origin_label, d.destination_label,
            d.start_date, d.end_date, d.quantity, d.unit_label, d.unit_price, d.subtotal
     FROM bookings b
     JOIN booking_details d ON d.booking_id = b.id
     ORDER BY b.id DESC
     LIMIT 1000"
);

$rows = [];
while ($result && ($row = mysqli_fetch_assoc($result))) {
    $rows[] = [
        $row['booking_code'],
        $row['customer_name'],
        $row['customer_email'],
        $row['customer_phone'],
        $row['service_type'],
        $row['service_name'],
        $row['origin_label'],
        $row['destination_label'],
        $row['start_date'],
        $row['end_date'],
        $row['quantity'],
        $row['unit_label'],
        $row['unit_price'],
        $row['subtotal'],
        $row['booking_status'],
        $row['payment_status'],
        $row['created_at'],
    ];
}

if ($result) {
    mysqli_free_result($result);
}

admin_log_activity($connection, 'booking.exported', 'bookings', null, 'CSV export');
admin_output_csv(
    'bali-bookings-' . date('Ymd-His') . '.csv',
    ['booking_code', 'customer_name', 'customer_email', 'customer_phone', 'service_type', 'service_name', 'origin', 'destination', 'start_date', 'end_date', 'quantity', 'unit_label', 'unit_price', 'subtotal', 'booking_status', 'payment_status', 'created_at'],
    $rows
);
