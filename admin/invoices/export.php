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
    "SELECT b.booking_code, b.customer_name, b.customer_email, b.payment_status, b.booking_status, b.created_at,
            d.service_name, d.start_date, d.subtotal
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
        $row['service_name'],
        $row['start_date'],
        $row['subtotal'],
        $row['booking_status'],
        $row['payment_status'],
        $row['created_at'],
    ];
}

if ($result) {
    mysqli_free_result($result);
}

admin_log_activity($connection, 'invoice.exported', 'bookings', null, 'CSV export');
admin_output_csv(
    'bali-invoices-' . date('Ymd-His') . '.csv',
    ['invoice_number', 'customer_name', 'customer_email', 'service_name', 'start_date', 'total', 'booking_status', 'payment_status', 'created_at'],
    $rows
);
