<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$logs = [];
$tableReady = $connection && admin_table_exists($connection, 'admin_activity_logs');

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia. Activity log belum bisa dimuat.';
} elseif ($tableReady) {
    $result = mysqli_query(
        $connection,
        "SELECT l.*, u.name AS admin_name, u.email AS admin_email
         FROM admin_activity_logs l
         LEFT JOIN users u ON u.id = l.admin_user_id
         ORDER BY l.id DESC
         LIMIT 150"
    );
    $logs = $result ? admin_fetch_all($result) : [];
}

$page_title = 'Activity Log';
$page_desc = 'Riwayat aktivitas admin Bali Paradise.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'activity';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-activity-title">
    <?php
    $admin_eyebrow = 'Audit Trail';
    $admin_title = 'Activity Log';
    $admin_title_id = 'admin-activity-title';
    $admin_subtitle = 'Pantau perubahan penting yang dilakukan admin tanpa menampilkan credential atau data sensitif.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if (!$tableReady): ?>
      <div class="admin-alert admin-alert--warning" role="status">
        Tabel <code>admin_activity_logs</code> belum tersedia.
      </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Riwayat Aktivitas</h2>
            <p><?= e(admin_format_number(count($logs))) ?> aktivitas terbaru ditampilkan.</p>
          </div>
        </div>

        <?php if (!$logs): ?>
          <div class="admin-empty">Belum ada aktivitas admin yang tercatat.</div>
        <?php else: ?>
          <div class="admin-data-table admin-data-table--activity">
            <table>
              <thead>
                <tr>
                  <th>Waktu</th>
                  <th>Admin</th>
                  <th>Aksi</th>
                  <th>Entity</th>
                  <th>Deskripsi</th>
                  <th>IP</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($logs as $log): ?>
                  <tr>
                    <td><strong><?= e($log['created_at']) ?></strong></td>
                    <td>
                      <strong><?= e($log['admin_name'] ?? 'System') ?></strong>
                      <small><?= e($log['admin_email'] ?? '-') ?></small>
                    </td>
                    <td><span class="admin-badge admin-badge--neutral"><?= e($log['action']) ?></span></td>
                    <td>
                      <strong><?= e($log['entity_type'] ?? '-') ?></strong>
                      <small><?= e($log['entity_id'] ?? '-') ?></small>
                    </td>
                    <td><?= e(admin_excerpt($log['description'] ?? '-', 140)) ?></td>
                    <td><?= e($log['ip_address'] ?? '-') ?></td>
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
