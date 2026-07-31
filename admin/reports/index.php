<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$reportCards = [];
$tableStatus = [];

$trackedTables = [
    'destination' => 'Destinasi publik',
    'detail_image' => 'Galeri legacy',
    'users' => 'Akun user',
    'roles' => 'Role akses',
    'bookings' => 'Booking internal',
    'booking_details' => 'Detail booking',
    'destination_categories' => 'Kategori destinasi',
    'tickets' => 'Tiket dan paket',
    'payments' => 'Pembayaran manual',
    'galleries' => 'Galeri baru',
    'contact_messages' => 'Pesan contact',
    'articles' => 'Artikel',
    'site_settings' => 'Settings',
    'admin_activity_logs' => 'Activity log',
];

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia. Laporan belum bisa dimuat.';
} else {
    foreach ($trackedTables as $table => $label) {
        $count = admin_count_table($connection, $table, array_keys($trackedTables));
        $tableStatus[] = [
            'table' => $table,
            'label' => $label,
            'count' => $count,
            'available' => $count !== null,
        ];
    }

    $reportCards = [
        ['label' => 'Destinasi', 'value' => admin_count_table($connection, 'destination', array_keys($trackedTables))],
        ['label' => 'Users', 'value' => admin_count_table($connection, 'users', array_keys($trackedTables))],
        ['label' => 'Booking', 'value' => admin_count_table($connection, 'bookings', array_keys($trackedTables))],
        [
            'label' => 'Unpaid',
            'value' => admin_table_exists($connection, 'bookings')
                ? (int) admin_query_value($connection, "SELECT COUNT(*) FROM bookings WHERE payment_status IN ('unpaid', 'manual_review')")
                : null,
        ],
        ['label' => 'Payments', 'value' => admin_count_table($connection, 'payments', array_keys($trackedTables))],
        ['label' => 'Articles', 'value' => admin_count_table($connection, 'articles', array_keys($trackedTables))],
    ];
}

$page_title = 'Laporan Ringkas';
$page_desc = 'Ringkasan data admin Bali Paradise.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'reports';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-reports-title">
    <?php
    $admin_eyebrow = 'Operational Report';
    $admin_title = 'Laporan Ringkas';
    $admin_title_id = 'admin-reports-title';
    $admin_subtitle = 'Pantau jumlah data penting dan kesiapan tabel admin tanpa query berat.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-kpi-row">
        <?php foreach ($reportCards as $card): ?>
          <article class="admin-kpi-card">
            <span><?= e($card['label']) ?></span>
            <strong><?= $card['value'] === null ? '-' : e(admin_format_number($card['value'])) ?></strong>
          </article>
        <?php endforeach; ?>
      </section>

      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Status Tabel</h2>
            <p>Gunakan ini untuk menentukan fitur admin yang sudah siap dipakai.</p>
          </div>
        </div>
        <div class="admin-data-table admin-data-table--compact">
          <table>
            <thead><tr><th>Tabel</th><th>Fungsi</th><th>Status</th><th>Jumlah</th></tr></thead>
            <tbody>
              <?php foreach ($tableStatus as $row): ?>
                <tr>
                  <td><strong><?= e($row['table']) ?></strong></td>
                  <td><?= e($row['label']) ?></td>
                  <td>
                    <span class="admin-badge <?= $row['available'] ? 'admin-badge--active' : 'admin-badge--pending' ?>">
                      <?= $row['available'] ? 'Available' : 'Missing' ?>
                    </span>
                  </td>
                  <td><?= $row['count'] === null ? '-' : e(admin_format_number($row['count'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
