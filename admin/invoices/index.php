<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';
require_once __DIR__ . '/../../booking/_helpers.php';
require_once __DIR__ . '/../../includes/notifications.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$filter = admin_normalize_status($_GET['status'] ?? 'all', ['all', 'unpaid', 'manual_review', 'paid'], 'all');
$invoices = [];
$paymentStatuses = ['unpaid', 'manual_review', 'paid'];

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia. Invoice belum bisa dimuat.';
} elseif (!booking_schema_ready($connection)) {
    http_response_code(503);
    $errorMessage = 'Tabel booking internal belum lengkap. Invoice internal memakai data bookings dan booking_details.';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bookingId = $_POST['booking_id'] ?? '';
        $paymentStatus = admin_normalize_status($_POST['payment_status'] ?? '', $paymentStatuses, 'unpaid');

        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
        } elseif (!is_valid_positive_id($bookingId)) {
            $errorMessage = 'Invoice tidak valid.';
        } else {
            $stmt = mysqli_prepare($connection, 'UPDATE bookings SET payment_status = ? WHERE id = ? LIMIT 1');

            if ($stmt) {
                $id = (int) $bookingId;
                mysqli_stmt_bind_param($stmt, 'si', $paymentStatus, $id);

                if (mysqli_stmt_execute($stmt)) {
                    admin_log_activity($connection, 'invoice.payment_updated', 'bookings', $id, $paymentStatus);
                    $notifyResult = mysqli_query($connection, 'SELECT booking_code, customer_name, customer_email, booking_status, payment_status FROM bookings WHERE id = ' . $id . ' LIMIT 1');
                    $notifyBooking = $notifyResult ? mysqli_fetch_assoc($notifyResult) : null;
                    if ($notifyResult) {
                        mysqli_free_result($notifyResult);
                    }
                    if ($notifyBooking) {
                        notification_send_booking_status($notifyBooking, 'invoice.payment_updated');
                    }
                    header('Location: index.php?notice=payment-updated');
                    exit;
                }

                error_log('Admin invoice payment update failed: ' . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
            }

            $errorMessage = 'Status invoice belum bisa diperbarui.';
        }
    }

    if (!$errorMessage) {
        $where = '';
        if ($filter !== 'all') {
            $safe = mysqli_real_escape_string($connection, $filter);
            $where = "WHERE b.payment_status = '{$safe}'";
        }

        $result = mysqli_query(
            $connection,
            "SELECT b.*, d.service_type, d.service_name, d.subtotal, d.start_date
             FROM bookings b
             JOIN booking_details d ON d.booking_id = b.id
             {$where}
             ORDER BY b.id DESC
             LIMIT 100"
        );
        $invoices = $result ? admin_fetch_all($result) : [];
    }
}

$page_title = 'Manajemen Invoice';
$page_desc = 'Kelola invoice internal Bali Paradise.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'invoices';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-invoices-title">
    <?php
    $admin_eyebrow = 'Invoice Control';
    $admin_title = 'Manajemen Invoice';
    $admin_title_id = 'admin-invoices-title';
    $admin_subtitle = 'Invoice internal dibuat dari booking. Status pembayaran dapat dikontrol manual.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($notice === 'payment-updated'): ?>
      <div class="admin-alert admin-alert--success" role="status">Status invoice berhasil diperbarui.</div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Daftar Invoice</h2>
            <p><?= e(admin_format_number(count($invoices))) ?> invoice ditampilkan.</p>
          </div>
          <div class="admin-row-actions">
            <a href="admin/invoices/export.php">Export CSV</a>
          </div>
        </div>

        <form class="admin-filterbar" action="admin/invoices/index.php" method="get">
          <select name="status" aria-label="Filter status pembayaran">
            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua pembayaran</option>
            <?php foreach ($paymentStatuses as $status): ?>
              <option value="<?= e($status) ?>" <?= $filter === $status ? 'selected' : '' ?>><?= e($status) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn--primary" type="submit">Filter</button>
        </form>

        <?php if (!$invoices): ?>
          <div class="admin-empty">Belum ada invoice internal.</div>
        <?php else: ?>
          <div class="admin-data-table admin-data-table--actions admin-data-table--booking">
            <table>
              <thead>
                <tr>
                  <th>Invoice</th>
                  <th>Customer</th>
                  <th>Layanan</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($invoices as $invoice): ?>
                  <tr>
                    <td>
                      <strong><?= e($invoice['booking_code']) ?></strong>
                      <small><?= e($invoice['created_at']) ?></small>
                    </td>
                    <td>
                      <strong><?= e($invoice['customer_name']) ?></strong>
                      <small><?= e($invoice['customer_email']) ?></small>
                    </td>
                    <td>
                      <strong><?= e($invoice['service_name']) ?></strong>
                      <small><?= e($invoice['start_date']) ?></small>
                    </td>
                    <td><?= e(admin_format_money($invoice['subtotal'])) ?></td>
                    <td><span class="admin-badge <?= e(admin_badge_class($invoice['payment_status'])) ?>"><?= e($invoice['payment_status']) ?></span></td>
                    <td>
                      <form class="admin-inline-form" action="admin/invoices/index.php" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= (int) $invoice['id'] ?>">
                        <select name="payment_status" aria-label="Status invoice">
                          <?php foreach ($paymentStatuses as $status): ?>
                            <option value="<?= e($status) ?>" <?= $invoice['payment_status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <button type="submit">Update</button>
                        <a class="admin-small-action" href="booking/invoice.php?id=<?= (int) $invoice['id'] ?>&token=<?= e(urlencode($invoice['public_token'])) ?>">Lihat</a>
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
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
