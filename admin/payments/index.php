<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';
require_once __DIR__ . '/../../booking/_helpers.php';
require_once __DIR__ . '/../../includes/notifications.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$payments = [];
$bookings = [];
$paymentTableReady = $connection && admin_table_exists($connection, 'payments');
$paymentStatuses = ['pending', 'manual_review', 'paid', 'rejected'];

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia. Pembayaran belum bisa dimuat.';
} elseif (!booking_schema_ready($connection)) {
    http_response_code(503);
    $errorMessage = 'Tabel booking internal belum lengkap.';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
        } elseif (!$paymentTableReady) {
            $errorMessage = 'Tabel payments belum tersedia.';
        } elseif ($action === 'create') {
            $bookingId = $_POST['booking_id'] ?? '';
            $paymentMethod = trim((string) ($_POST['payment_method'] ?? 'manual_transfer'));
            $amount = filter_var($_POST['amount'] ?? 0, FILTER_VALIDATE_FLOAT);
            $paymentStatus = admin_normalize_status($_POST['payment_status'] ?? 'pending', $paymentStatuses, 'pending');
            $paidAt = trim((string) ($_POST['paid_at'] ?? ''));
            $notes = trim((string) ($_POST['notes'] ?? ''));

            if (!is_valid_positive_id($bookingId)) {
                $errorMessage = 'Booking tidak valid.';
            } elseif ($amount === false || $amount < 0) {
                $errorMessage = 'Nominal pembayaran tidak valid.';
            } else {
                $stmt = mysqli_prepare(
                    $connection,
                    'INSERT INTO payments (booking_id, payment_method, amount, payment_status, paid_at, notes) VALUES (?, ?, ?, ?, ?, ?)'
                );

                if ($stmt) {
                    $bookingIdValue = (int) $bookingId;
                    $paidAtValue = $paidAt !== '' ? str_replace('T', ' ', $paidAt) . ':00' : null;
                    $notesValue = $notes !== '' ? $notes : null;
                    mysqli_stmt_bind_param($stmt, 'isdsss', $bookingIdValue, $paymentMethod, $amount, $paymentStatus, $paidAtValue, $notesValue);

                    if (mysqli_stmt_execute($stmt)) {
                        admin_log_activity($connection, 'payment.created', 'payments', mysqli_insert_id($connection), 'Booking #' . $bookingIdValue);
                        admin_redirect('index.php?notice=created');
                    }

                    error_log('Admin payment create failed: ' . mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);
                }

                $errorMessage = 'Pembayaran belum bisa dibuat.';
            }
        } elseif ($action === 'status') {
            $paymentId = $_POST['payment_id'] ?? '';
            $paymentStatus = admin_normalize_status($_POST['payment_status'] ?? 'pending', $paymentStatuses, 'pending');

            if (!is_valid_positive_id($paymentId)) {
                $errorMessage = 'Pembayaran tidak valid.';
            } else {
                $stmt = mysqli_prepare(
                    $connection,
                    "UPDATE payments
                     SET payment_status = ?, paid_at = CASE WHEN ? = 'paid' AND paid_at IS NULL THEN NOW() ELSE paid_at END
                     WHERE id = ?
                     LIMIT 1"
                );

                if ($stmt) {
                    $id = (int) $paymentId;
                    mysqli_stmt_bind_param($stmt, 'ssi', $paymentStatus, $paymentStatus, $id);

                    if (mysqli_stmt_execute($stmt)) {
                        admin_log_activity($connection, 'payment.status_updated', 'payments', $id, $paymentStatus);
                        admin_redirect('index.php?notice=status-updated');
                    }

                    error_log('Admin payment status update failed: ' . mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);
                }

                $errorMessage = 'Status pembayaran belum bisa diperbarui.';
            }
        }
    }

    if ($paymentTableReady) {
        $result = mysqli_query(
            $connection,
            "SELECT p.*, b.booking_code, b.public_token, b.customer_name, b.customer_email, b.payment_status AS booking_payment_status,
                    d.service_name, d.subtotal
             FROM payments p
             LEFT JOIN bookings b ON b.id = p.booking_id
             LEFT JOIN booking_details d ON d.booking_id = b.id
             ORDER BY p.id DESC
             LIMIT 100"
        );
        $payments = $result ? admin_fetch_all($result) : [];
    }

    $bookingResult = mysqli_query(
        $connection,
        "SELECT b.id, b.booking_code, b.public_token, b.customer_name, b.customer_email, b.payment_status, b.created_at,
                d.service_name, d.subtotal
         FROM bookings b
         JOIN booking_details d ON d.booking_id = b.id
         ORDER BY FIELD(b.payment_status, 'manual_review', 'unpaid', 'paid'), b.id DESC
         LIMIT 100"
    );
    $bookings = $bookingResult ? admin_fetch_all($bookingResult) : [];
}

$page_title = 'Manajemen Pembayaran';
$page_desc = 'Pantau status pembayaran manual.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'payments';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-payments-title">
    <?php
    $admin_eyebrow = 'Payment Review';
    $admin_title = 'Manajemen Pembayaran';
    $admin_title_id = 'admin-payments-title';
    $admin_subtitle = 'Saat ini pembayaran masih manual dan status disimpan pada tabel bookings.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($connection && !$paymentTableReady): ?>
      <div class="admin-alert admin-alert--warning" role="status">
        Tabel <code>payments</code> khusus belum tersedia. Halaman ini memakai <code>bookings.payment_status</code> sebagai sumber status pembayaran.
      </div>
    <?php endif; ?>

    <?php $flashMessage = admin_flash_message($notice); ?>
    <?php if ($flashMessage): ?>
      <div class="admin-alert admin-alert--success" role="status"><?= e($flashMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <?php if ($paymentTableReady): ?>
        <section class="admin-panel admin-panel--form">
          <div class="admin-panel__head">
            <div>
              <h2>Tambah Pembayaran Manual</h2>
              <p>Catat transfer manual atau hasil review bukti bayar tanpa integrasi payment gateway.</p>
            </div>
          </div>
          <form class="admin-form" action="admin/payments/index.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create">
            <div class="admin-form__grid">
              <div class="admin-field">
                <label for="booking-id">Booking</label>
                <select id="booking-id" name="booking_id" required>
                  <option value="">Pilih booking</option>
                  <?php foreach ($bookings as $booking): ?>
                    <option value="<?= (int) $booking['id'] ?>"><?= e($booking['booking_code'] . ' - ' . $booking['customer_name']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="admin-field">
                <label for="payment-method">Metode</label>
                <input id="payment-method" name="payment_method" type="text" maxlength="80" value="manual_transfer">
              </div>
              <div class="admin-field">
                <label for="amount">Nominal</label>
                <input id="amount" name="amount" type="number" min="0" step="1000" value="0" required>
              </div>
              <div class="admin-field">
                <label for="payment-status">Status</label>
                <select id="payment-status" name="payment_status">
                  <?php foreach ($paymentStatuses as $status): ?>
                    <option value="<?= e($status) ?>"><?= e($status) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="admin-field">
                <label for="paid-at">Paid at</label>
                <input id="paid-at" name="paid_at" type="datetime-local">
              </div>
              <div class="admin-field admin-field--wide">
                <label for="notes">Catatan</label>
                <textarea id="notes" name="notes" rows="3" maxlength="1000"></textarea>
              </div>
            </div>
            <div class="admin-form__actions">
              <button class="btn btn--primary" type="submit">Simpan Pembayaran</button>
            </div>
          </form>
        </section>

        <section class="admin-panel">
          <div class="admin-panel__head">
            <div>
              <h2>Payment Records</h2>
              <p><?= e(admin_format_number(count($payments))) ?> catatan pembayaran.</p>
            </div>
          </div>

          <?php if (!$payments): ?>
            <div class="admin-empty">Belum ada catatan pembayaran manual.</div>
          <?php else: ?>
            <div class="admin-data-table admin-data-table--actions admin-data-table--payment">
              <table>
                <thead><tr><th>Booking</th><th>Customer</th><th>Metode</th><th>Nominal</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                  <?php foreach ($payments as $payment): ?>
                    <tr>
                      <td><strong><?= e($payment['booking_code'] ?? '-') ?></strong><small><?= e($payment['service_name'] ?? '-') ?></small></td>
                      <td><strong><?= e($payment['customer_name'] ?? '-') ?></strong><small><?= e($payment['customer_email'] ?? '-') ?></small></td>
                      <td><?= e($payment['payment_method'] ?? '-') ?></td>
                      <td><?= e(admin_format_money($payment['amount'])) ?></td>
                      <td><span class="admin-badge <?= e(admin_badge_class($payment['payment_status'])) ?>"><?= e($payment['payment_status']) ?></span></td>
                      <td>
                        <form class="admin-inline-form" action="admin/payments/index.php" method="post">
                          <?= csrf_field() ?>
                          <input type="hidden" name="action" value="status">
                          <input type="hidden" name="payment_id" value="<?= (int) $payment['id'] ?>">
                          <select name="payment_status" aria-label="Status pembayaran">
                            <?php foreach ($paymentStatuses as $status): ?>
                              <option value="<?= e($status) ?>" <?= $payment['payment_status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                            <?php endforeach; ?>
                          </select>
                          <button type="submit">Update</button>
                          <?php if (!empty($payment['payment_proof'])): ?>
                            <a class="admin-small-action" href="<?= e($payment['payment_proof']) ?>" target="_blank" rel="noopener">Bukti</a>
                          <?php endif; ?>
                        </form>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </section>
      <?php endif; ?>

      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Status Booking</h2>
            <p>Ubah status detail melalui halaman Invoice.</p>
          </div>
          <a class="admin-link" href="admin/invoices/index.php">Kelola Invoice</a>
        </div>

        <?php if (!$bookings): ?>
          <div class="admin-empty">Belum ada pembayaran.</div>
        <?php else: ?>
          <div class="admin-data-table admin-data-table--actions admin-data-table--payment">
            <table>
              <thead>
                <tr>
                  <th>Booking</th>
                  <th>Customer</th>
                  <th>Layanan</th>
                  <th>Nominal</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($bookings as $payment): ?>
                  <tr>
                    <td><strong><?= e($payment['booking_code']) ?></strong><small><?= e($payment['created_at']) ?></small></td>
                    <td><strong><?= e($payment['customer_name']) ?></strong><small><?= e($payment['customer_email']) ?></small></td>
                    <td><?= e($payment['service_name']) ?></td>
                    <td><?= e(admin_format_money($payment['subtotal'])) ?></td>
                    <td><span class="admin-badge <?= e(admin_badge_class($payment['payment_status'])) ?>"><?= e($payment['payment_status']) ?></span></td>
                    <td><a class="admin-small-action" href="admin/invoices/index.php">Review</a></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
      </section>
    <?php endif; ?>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
