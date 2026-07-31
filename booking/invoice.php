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
        $errorMessage = 'Invoice tidak ditemukan atau token tidak valid.';
    }
}

$page_title = 'Invoice Booking';
$page_desc = 'Invoice booking internal Bali Paradise.';
$page_css = 'styles/booking.internal.css';
$active = auth_check() ? 'invoice' : 'tiket';
$base_href = '../';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/navbar.php';
?>

<main class="booking-page booking-page--compact">
  <?php if ($errorMessage): ?>
    <section class="booking-confirm">
      <div class="booking-alert" role="alert"><?= e($errorMessage) ?></div>
      <a class="btn btn--outline" href="booking/index.php">Kembali ke Booking</a>
    </section>
  <?php else: ?>
    <section class="invoice-card" aria-labelledby="invoice-title">
      <div class="invoice-head">
        <div>
          <span class="booking-eyebrow">Invoice</span>
          <h1 id="invoice-title"><?= e($booking['booking_code']) ?></h1>
          <p>Invoice internal. Pembayaran manual dan belum terhubung payment gateway.</p>
        </div>
        <button class="btn btn--outline" type="button" onclick="window.print()">Print</button>
      </div>

      <div class="invoice-meta">
        <article>
          <span>Pemesan</span>
          <strong><?= e($booking['customer_name']) ?></strong>
          <p><?= e($booking['customer_email']) ?><br><?= e($booking['customer_phone']) ?></p>
        </article>
        <article>
          <span>Status</span>
          <strong><?= e($booking['booking_status']) ?></strong>
          <p>Pembayaran: <?= e($booking['payment_status']) ?></p>
        </article>
      </div>

      <div class="invoice-table">
        <div class="invoice-row invoice-row--head">
          <span>Layanan</span>
          <span>Tanggal</span>
          <span>Jumlah</span>
          <span>Subtotal</span>
        </div>
        <div class="invoice-row">
          <span>
            <strong><?= e($booking['service_name']) ?></strong>
            <small><?= $booking['origin_label'] ? e($booking['origin_label']) . ' ke ' : '' ?><?= e($booking['destination_label']) ?></small>
          </span>
          <span><?= e($booking['start_date']) ?><?= $booking['end_date'] ? '<br>' . e($booking['end_date']) : '' ?></span>
          <span><?= e($booking['quantity']) ?> <?= e($booking['unit_label']) ?></span>
          <span><?= e(booking_format_money($booking['subtotal'])) ?></span>
        </div>
      </div>

      <div class="invoice-total">
        <span>Total Estimasi</span>
        <strong><?= e(booking_format_money($booking['subtotal'])) ?></strong>
      </div>

      <?php if (!empty($booking['notes'])): ?>
        <div class="invoice-note">
          <strong>Catatan</strong>
          <p><?= e($booking['notes']) ?></p>
        </div>
      <?php endif; ?>
    </section>
  <?php endif; ?>
</main>

<?php include __DIR__ . '/../partials/footer.php'; ?>
