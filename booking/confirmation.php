<?php
require_once __DIR__ . '/_helpers.php';

auth_start_session();

$connection = db_connect();
$booking = null;
$errorMessage = null;

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Sistem booking belum tersedia.';
} elseif (!booking_schema_ready($connection)) {
    http_response_code(500);
    $errorMessage = 'Tabel booking internal belum tersedia.';
} else {
    $booking = booking_find_by_token($connection, $_GET['id'] ?? '', $_GET['token'] ?? '');

    if (!$booking) {
        http_response_code(404);
        $errorMessage = 'Booking tidak ditemukan atau token tidak valid.';
    }
}

$page_title = 'Konfirmasi Booking';
$page_desc = 'Status booking internal Bali Paradise.';
$page_css = 'styles/booking.internal.css';
$active = 'tiket';
$base_href = '../';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/navbar.php';
?>

<main class="booking-page booking-page--compact">
  <section class="booking-confirm">
    <?php if ($errorMessage): ?>
      <div class="booking-alert" role="alert"><?= e($errorMessage) ?></div>
      <a class="btn btn--outline" href="booking/index.php">Kembali ke Booking</a>
    <?php else: ?>
      <span class="booking-eyebrow">Booking Created</span>
      <h1>Booking Anda berhasil dibuat.</h1>
      <p>Tim admin akan melakukan pengecekan manual. Pembayaran belum aktif otomatis.</p>

      <div class="booking-status-grid">
        <article>
          <span>Kode Booking</span>
          <strong><?= e($booking['booking_code']) ?></strong>
        </article>
        <article>
          <span>Status Booking</span>
          <strong><?= e($booking['booking_status']) ?></strong>
        </article>
        <article>
          <span>Status Pembayaran</span>
          <strong><?= e($booking['payment_status']) ?></strong>
        </article>
      </div>

      <div class="booking-summary-list">
        <p><strong>Layanan:</strong> <?= e($booking['service_name']) ?></p>
        <p><strong>Rute/Tujuan:</strong>
          <?= $booking['origin_label'] ? e($booking['origin_label']) . ' ke ' : '' ?><?= e($booking['destination_label']) ?>
        </p>
        <p><strong>Tanggal:</strong> <?= e($booking['start_date']) ?><?= $booking['end_date'] ? ' - ' . e($booking['end_date']) : '' ?></p>
        <p><strong>Jumlah:</strong> <?= e($booking['quantity']) ?> <?= e($booking['unit_label']) ?></p>
        <p><strong>Total Estimasi:</strong> <?= e(booking_format_money($booking['subtotal'])) ?></p>
      </div>

      <div class="booking-actions">
        <a class="btn btn--primary" href="booking/invoice.php?id=<?= (int) $booking['id'] ?>&token=<?= e($booking['public_token']) ?>">Lihat Invoice</a>
        <?php if (auth_check()): ?>
          <a class="btn btn--outline" href="booking/history.php">Riwayat Invoice</a>
        <?php endif; ?>
        <a class="btn btn--outline" href="booking/index.php">Buat Booking Baru</a>
      </div>
    <?php endif; ?>
  </section>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
