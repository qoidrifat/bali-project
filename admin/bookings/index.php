<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';
require_once __DIR__ . '/../../booking/_helpers.php';
require_once __DIR__ . '/../../includes/notifications.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$filter = admin_normalize_status($_GET['status'] ?? 'all', ['all', 'pending', 'confirmed', 'cancelled'], 'all');
$bookings = [];
$bookingStatuses = ['pending', 'confirmed', 'cancelled'];
$paymentStatuses = ['unpaid', 'manual_review', 'paid'];

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia. Data booking belum bisa dimuat.';
} elseif (!booking_schema_ready($connection)) {
    http_response_code(503);
    $errorMessage = 'Tabel booking internal belum lengkap. Review dan jalankan SQL booking manual terlebih dahulu.';
} else {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $bookingId = $_POST['booking_id'] ?? '';
        $bookingStatus = admin_normalize_status($_POST['booking_status'] ?? '', $bookingStatuses, 'pending');
        $paymentStatus = admin_normalize_status($_POST['payment_status'] ?? '', $paymentStatuses, 'unpaid');

        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
        } elseif (!is_valid_positive_id($bookingId)) {
            $errorMessage = 'Booking tidak valid.';
        } else {
            $stmt = mysqli_prepare($connection, 'UPDATE bookings SET booking_status = ?, payment_status = ? WHERE id = ? LIMIT 1');

            if (!$stmt) {
                error_log('Admin booking status prepare failed: ' . mysqli_error($connection));
                $errorMessage = 'Status booking belum bisa diperbarui.';
            } else {
                $id = (int) $bookingId;
                mysqli_stmt_bind_param($stmt, 'ssi', $bookingStatus, $paymentStatus, $id);

                if (!mysqli_stmt_execute($stmt)) {
                    error_log('Admin booking status update failed: ' . mysqli_stmt_error($stmt));
                    $errorMessage = 'Status booking belum bisa diperbarui.';
                } else {
                    admin_log_activity($connection, 'booking.status_updated', 'bookings', $id, $bookingStatus . ' / ' . $paymentStatus);
                    $notifyResult = mysqli_query($connection, 'SELECT booking_code, customer_name, customer_email, booking_status, payment_status FROM bookings WHERE id = ' . $id . ' LIMIT 1');
                    $notifyBooking = $notifyResult ? mysqli_fetch_assoc($notifyResult) : null;
                    if ($notifyResult) {
                        mysqli_free_result($notifyResult);
                    }
                    if ($notifyBooking) {
                        notification_send_booking_status($notifyBooking, 'booking.status_updated');
                    }
                    header('Location: index.php?notice=status-updated');
                    exit;
                }

                mysqli_stmt_close($stmt);
            }
        }
    }

    if (!$errorMessage) {
        if ($filter === 'all') {
            $result = mysqli_query(
                $connection,
                "SELECT b.*, d.service_type, d.service_name, d.origin_label, d.destination_label, d.start_date, d.end_date,
                        d.quantity, d.unit_label, d.subtotal
                 FROM bookings b
                 JOIN booking_details d ON d.booking_id = b.id
                 ORDER BY b.id DESC
                 LIMIT 100"
            );
            $bookings = $result ? admin_fetch_all($result) : [];
        } else {
            $stmt = mysqli_prepare(
                $connection,
                "SELECT b.*, d.service_type, d.service_name, d.origin_label, d.destination_label, d.start_date, d.end_date,
                        d.quantity, d.unit_label, d.subtotal
                 FROM bookings b
                 JOIN booking_details d ON d.booking_id = b.id
                 WHERE b.booking_status = ?
                 ORDER BY b.id DESC
                 LIMIT 100"
            );

            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $filter);
                mysqli_stmt_execute($stmt);
                $bookings = admin_fetch_all(mysqli_stmt_get_result($stmt));
                mysqli_stmt_close($stmt);
            }
        }
    }
}

$page_title = 'Manajemen Booking';
$page_desc = 'Kelola booking internal Bali Paradise.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'bookings';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-bookings-title">
    <?php
    $admin_eyebrow = 'Booking Operations';
    $admin_title = 'Manajemen Booking';
    $admin_title_id = 'admin-bookings-title';
    $admin_subtitle = 'Pantau booking internal, layanan yang dipilih, dan ubah status operasional.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($notice === 'status-updated'): ?>
      <div class="admin-alert admin-alert--success" role="status">Status booking berhasil diperbarui.</div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Daftar Booking</h2>
            <p><?= e(admin_format_number(count($bookings))) ?> booking ditampilkan.</p>
          </div>
          <div class="admin-row-actions">
            <a href="admin/bookings/export.php">Export CSV</a>
            <a href="booking/index.php">Buat Booking</a>
          </div>
        </div>

        <form class="admin-filterbar" action="admin/bookings/index.php" method="get">
          <select name="status" aria-label="Filter status booking">
            <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>Semua status</option>
            <?php foreach ($bookingStatuses as $status): ?>
              <option value="<?= e($status) ?>" <?= $filter === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn--primary" type="submit">Filter</button>
        </form>

        <?php if (!$bookings): ?>
          <div class="admin-empty">Belum ada booking internal.</div>
        <?php else: ?>
          <div class="admin-data-table admin-data-table--actions admin-data-table--booking">
            <table>
              <thead>
                <tr>
                  <th>Booking</th>
                  <th>Layanan</th>
                  <th>Customer</th>
                  <th>Total</th>
                  <th>Status</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($bookings as $booking): ?>
                  <tr>
                    <td>
                      <strong><?= e($booking['booking_code']) ?></strong>
                      <small><?= e($booking['created_at']) ?></small>
                    </td>
                    <td>
                      <strong><?= e($booking['service_name']) ?></strong>
                      <small><?= e(($booking['origin_label'] ? $booking['origin_label'] . ' ke ' : '') . $booking['destination_label']) ?> · <?= e($booking['start_date']) ?></small>
                    </td>
                    <td>
                      <strong><?= e($booking['customer_name']) ?></strong>
                      <small><?= e($booking['customer_email']) ?> · <?= e($booking['customer_phone']) ?></small>
                    </td>
                    <td><?= e(admin_format_money($booking['subtotal'])) ?></td>
                    <td>
                      <span class="admin-badge <?= e(admin_badge_class($booking['booking_status'])) ?>"><?= e($booking['booking_status']) ?></span>
                      <span class="admin-badge <?= e(admin_badge_class($booking['payment_status'])) ?>"><?= e($booking['payment_status']) ?></span>
                    </td>
                    <td>
                      <form class="admin-inline-form" action="admin/bookings/index.php" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="booking_id" value="<?= (int) $booking['id'] ?>">
                        <select name="booking_status" aria-label="Status booking">
                          <?php foreach ($bookingStatuses as $status): ?>
                            <option value="<?= e($status) ?>" <?= $booking['booking_status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <select name="payment_status" aria-label="Status pembayaran">
                          <?php foreach ($paymentStatuses as $status): ?>
                            <option value="<?= e($status) ?>" <?= $booking['payment_status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <button type="submit">Update</button>
                        <a class="admin-small-action" href="booking/invoice.php?id=<?= (int) $booking['id'] ?>&token=<?= e(urlencode($booking['public_token'])) ?>">Invoice</a>
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
