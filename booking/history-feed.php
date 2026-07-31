<?php
require_once __DIR__ . '/_helpers.php';

auth_start_session();

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!auth_check()) {
    http_response_code(401);
    ?>
    <div class="booking-alert" role="alert">
      Sesi akun sudah berakhir. Silakan login ulang untuk melihat riwayat invoice.
    </div>
    <?php
    exit;
}

$connection = db_connect();

if (!$connection) {
    http_response_code(503);
    ?>
    <div class="booking-alert" role="alert">
      Riwayat invoice belum bisa dimuat. Silakan coba lagi nanti.
    </div>
    <?php
    exit;
}

if (!booking_schema_ready($connection)) {
    http_response_code(503);
    ?>
    <div class="booking-alert" role="alert">
      Tabel booking internal belum lengkap. Jalankan SQL booking manual terlebih dahulu.
    </div>
    <?php
    exit;
}

$user = auth_user();
$bookings = booking_find_for_user($connection, $user['id'] ?? 0);

booking_render_history_list($bookings);
