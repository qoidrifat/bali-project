<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/_helpers.php';

$adminUser = require_admin();
$connection = db_connect();
$dashboardError = null;

$allowedTables = [
    'articles',
    'admin_activity_logs',
    'bookings',
    'booking_details',
    'bookings_hotel',
    'bookings_mobil',
    'bookings_pesawat',
    'buses',
    'car',
    'contact_messages',
    'destination',
    'destination_categories',
    'destinations',
    'from_city',
    'galleries',
    'hotel',
    'payments',
    'pesawat',
    'roles',
    'routes_bus',
    'site_settings',
    'tickets',
    'to_city',
    'users',
];

$stats = [
    'destinations' => null,
    'bookings' => ['total' => 0, 'available' => 0, 'missing' => []],
    'internal_bookings' => null,
    'pending_bookings' => null,
    'unpaid_invoices' => null,
    'messages' => null,
    'payments' => null,
    'articles' => null,
    'users' => null,
];
$recentDestinations = [];
$recentBookings = [];
$recentActivities = [];
$bookingTrend = [];

if (!$connection) {
    http_response_code(500);
    $dashboardError = 'Koneksi database belum tersedia. Statistik admin belum bisa dimuat.';
} else {
    $stats['destinations'] = admin_count_table($connection, 'destination', $allowedTables);
    $stats['messages'] = admin_count_table($connection, 'contact_messages', $allowedTables);
    $stats['payments'] = admin_count_table($connection, 'payments', $allowedTables);
    $stats['articles'] = admin_count_table($connection, 'articles', $allowedTables);
    $stats['users'] = admin_count_table($connection, 'users', $allowedTables);
    $stats['bookings'] = admin_sum_tables($connection, ['bookings_hotel', 'bookings_mobil', 'bookings_pesawat', 'routes_bus'], $allowedTables);
    $stats['internal_bookings'] = admin_count_table($connection, 'bookings', $allowedTables);
    $stats['pending_bookings'] = admin_table_exists($connection, 'bookings')
        ? (int) admin_query_value($connection, "SELECT COUNT(*) FROM bookings WHERE booking_status = 'pending'")
        : null;
    $stats['unpaid_invoices'] = admin_table_exists($connection, 'bookings')
        ? (int) admin_query_value($connection, "SELECT COUNT(*) FROM bookings WHERE payment_status IN ('unpaid', 'manual_review')")
        : null;

    if (admin_table_exists($connection, 'destination')) {
        $result = mysqli_query($connection, 'SELECT id_des, nama_des, gambar FROM destination ORDER BY id_des DESC LIMIT 5');
        $recentDestinations = $result ? admin_fetch_all($result) : [];
    }

    if (admin_table_exists($connection, 'bookings') && admin_table_exists($connection, 'booking_details')) {
        $result = mysqli_query(
            $connection,
            "SELECT b.id, b.booking_code, b.public_token, b.customer_name, b.booking_status, b.payment_status, b.created_at,
                    d.service_name, d.subtotal
             FROM bookings b
             JOIN booking_details d ON d.booking_id = b.id
             ORDER BY b.id DESC
             LIMIT 6"
        );
        $recentBookings = $result ? admin_fetch_all($result) : [];

        $trendResult = mysqli_query(
            $connection,
            "SELECT DATE(created_at) AS booking_date, COUNT(*) AS total
             FROM bookings
             WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
             GROUP BY DATE(created_at)
             ORDER BY booking_date ASC"
        );
        $trendRows = $trendResult ? admin_fetch_all($trendResult) : [];
        $trendMap = [];
        foreach ($trendRows as $row) {
            $trendMap[$row['booking_date']] = (int) $row['total'];
        }
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime('-' . $i . ' days'));
            $bookingTrend[] = [
                'label' => date('d M', strtotime($date)),
                'value' => $trendMap[$date] ?? 0,
            ];
        }
    }

    if (admin_table_exists($connection, 'admin_activity_logs')) {
        $activityResult = mysqli_query(
            $connection,
            "SELECT l.*, u.name AS admin_name
             FROM admin_activity_logs l
             LEFT JOIN users u ON u.id = l.admin_user_id
             ORDER BY l.id DESC
             LIMIT 5"
        );
        $recentActivities = $activityResult ? admin_fetch_all($activityResult) : [];
    }
}

$statCards = [
    ['label' => 'Destinasi', 'value' => $stats['destinations'], 'note' => 'Konten wisata publik', 'tone' => 'teal'],
    ['label' => 'Booking Internal', 'value' => $stats['internal_bookings'], 'note' => 'Transaksi dari akun', 'tone' => 'sunset'],
    ['label' => 'Pending', 'value' => $stats['pending_bookings'], 'note' => 'Perlu follow-up', 'tone' => 'sky'],
    ['label' => 'Invoice Unpaid', 'value' => $stats['unpaid_invoices'], 'note' => 'Unpaid / review', 'tone' => 'sunset'],
    ['label' => 'Payments', 'value' => $stats['payments'], 'note' => 'Catatan pembayaran', 'tone' => 'teal'],
    ['label' => 'Users', 'value' => $stats['users'], 'note' => 'Akun terdaftar', 'tone' => 'slate'],
    ['label' => 'Pesan', 'value' => $stats['messages'], 'note' => 'Contact inbox', 'tone' => 'sky'],
    ['label' => 'Artikel', 'value' => $stats['articles'], 'note' => 'Konten blog', 'tone' => 'slate'],
];

$masterTables = [
    'destination_categories' => 'Kategori destinasi',
    'tickets' => 'Tiket dan paket',
    'payments' => 'Pembayaran manual',
    'galleries' => 'Galeri baru',
    'articles' => 'Artikel wisata',
    'site_settings' => 'Pengaturan website',
    'contact_messages' => 'Pesan contact',
    'admin_activity_logs' => 'Audit trail',
];

$maxTrend = 1;
foreach ($bookingTrend as $point) {
    $maxTrend = max($maxTrend, (int) $point['value']);
}

$page_title = 'Admin Dashboard';
$page_desc = 'Dashboard admin dasar Bali Paradise.';
$page_css = 'styles/admin.css';
$active = '';
$admin_active = 'dashboard';
$base_href = '../';
include __DIR__ . '/../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-dashboard-title">
    <?php
    $admin_eyebrow = 'Bali Paradise Control';
    $admin_title = 'Dashboard Admin';
    $admin_title_id = 'admin-dashboard-title';
    $admin_subtitle = 'Ringkasan operasional untuk destinasi, booking, invoice, konten, dan aktivitas admin.';
    include __DIR__ . '/partials/topbar.php';
    ?>

    <?php if ($dashboardError): ?>
      <div class="admin-alert" role="alert"><?= e($dashboardError) ?></div>
    <?php endif; ?>

    <section class="admin-stats" aria-label="Statistik utama">
      <?php foreach ($statCards as $card): ?>
        <article class="admin-stat admin-stat--<?= e($card['tone']) ?>">
          <div class="admin-stat__head">
            <span class="admin-stat__label"><?= e($card['label']) ?></span>
          </div>
          <strong><?= $card['value'] === null ? '-' : e(admin_format_number($card['value'])) ?></strong>
          <p><?= e($card['note']) ?></p>
        </article>
      <?php endforeach; ?>
    </section>

    <section class="admin-grid admin-grid--balanced">
      <article class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Trend booking 7 hari</h2>
            <p>Visual ringan dari tabel <code>bookings</code>.</p>
          </div>
          <a class="admin-link" href="admin/reports/index.php">Laporan</a>
        </div>
        <div class="admin-chart" aria-label="Trend booking 7 hari">
          <?php foreach ($bookingTrend as $point): ?>
            <?php $height = max(8, (int) round(((int) $point['value'] / $maxTrend) * 100)); ?>
            <div class="admin-chart__bar">
              <span style="height: <?= $height ?>%"></span>
              <small><?= e($point['label']) ?></small>
              <strong><?= e(admin_format_number($point['value'])) ?></strong>
            </div>
          <?php endforeach; ?>
        </div>
      </article>

      <article class="admin-panel admin-panel--note">
        <div class="admin-panel__head">
          <div>
            <h2>Quick actions</h2>
            <p>Akses cepat modul yang sering dipakai.</p>
          </div>
        </div>
        <div class="admin-action-grid">
          <a href="admin/bookings/index.php">
            <span><strong>Booking</strong><small>Review booking masuk</small></span>
          </a>
          <a href="admin/invoices/index.php">
            <span><strong>Invoice</strong><small>Update pembayaran</small></span>
          </a>
          <a href="admin/tickets/index.php">
            <span><strong>Paket</strong><small>Kelola tiket</small></span>
          </a>
          <a href="admin/messages/index.php">
            <span><strong>Pesan</strong><small>Follow-up contact</small></span>
          </a>
        </div>
      </article>
    </section>

    <section class="admin-grid">
      <article class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Booking terbaru</h2>
            <p>Data dari booking internal.</p>
          </div>
          <a class="admin-link" href="admin/bookings/index.php">Kelola</a>
        </div>

        <?php if (!$recentBookings): ?>
          <div class="admin-empty">Belum ada booking internal yang bisa ditampilkan.</div>
        <?php else: ?>
          <div class="admin-table" role="table" aria-label="Booking terbaru">
            <?php foreach ($recentBookings as $booking): ?>
              <div class="admin-table__row" role="row">
                <span role="cell">
                  <strong><?= e($booking['booking_code']) ?> &middot; <?= e($booking['customer_name']) ?></strong>
                  <small><?= e($booking['service_name']) ?> &middot; <?= e(admin_format_money($booking['subtotal'])) ?></small>
                </span>
                <b role="cell"><?= e($booking['booking_status']) ?> / <?= e($booking['payment_status']) ?></b>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </article>

      <article class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Destinasi terbaru</h2>
            <p>Data dari tabel <code>destination</code>.</p>
          </div>
          <a class="admin-link" href="admin/destinations/index.php">Kelola</a>
        </div>

        <?php if (!$recentDestinations): ?>
          <div class="admin-empty">Belum ada data destinasi yang bisa ditampilkan.</div>
        <?php else: ?>
          <div class="admin-list">
            <?php foreach ($recentDestinations as $destination): ?>
              <?php $imagePath = public_image_path($destination['gambar'] ?? ''); ?>
              <a class="admin-list__item" href="detail.php?id=<?= (int) $destination['id_des'] ?>">
                <img src="<?= e($imagePath) ?>" alt="<?= e($destination['nama_des']) ?>" loading="lazy">
                <div>
                  <strong><?= e($destination['nama_des']) ?></strong>
                  <span>ID #<?= (int) $destination['id_des'] ?></span>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </article>
    </section>

    <section class="admin-grid admin-grid--support">
      <article class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Status tabel admin</h2>
            <p>Kesiapan modul tambahan setelah migration additive.</p>
          </div>
        </div>
        <div class="admin-table">
          <?php foreach ($masterTables as $table => $label): ?>
            <?php $count = $connection ? admin_count_table($connection, $table, $allowedTables) : null; ?>
            <div class="admin-table__row">
              <span><strong><?= e($table) ?></strong><small><?= e($label) ?></small></span>
              <b><?= $count === null ? 'Missing' : e(admin_format_number($count)) ?></b>
            </div>
          <?php endforeach; ?>
        </div>
      </article>

      <article class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Aktivitas terbaru</h2>
            <p>Audit trail dari aksi admin.</p>
          </div>
          <a class="admin-link" href="admin/activity/index.php">Lihat</a>
        </div>
        <?php if (!$recentActivities): ?>
          <div class="admin-empty">Belum ada activity log.</div>
        <?php else: ?>
          <div class="admin-table">
            <?php foreach ($recentActivities as $activity): ?>
              <div class="admin-table__row">
                <span>
                  <strong><?= e($activity['action']) ?></strong>
                  <small><?= e(($activity['admin_name'] ?? 'System') . ' - ' . $activity['created_at']) ?></small>
                </span>
                <b><?= e($activity['entity_type'] ?? '-') ?></b>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </article>
    </section>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
