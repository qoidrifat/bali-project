<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$roles = [];

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia. Data role belum bisa dimuat.';
} elseif (!auth_schema_ready($connection)) {
    http_response_code(503);
    $errorMessage = 'Schema auth belum lengkap. Review dan jalankan SQL auth manual terlebih dahulu.';
} else {
    $result = mysqli_query(
        $connection,
        "SELECT roles.id, roles.name, roles.label, COUNT(users.id) AS total_users
         FROM roles
         LEFT JOIN users ON users.role_id = roles.id
         GROUP BY roles.id, roles.name, roles.label
         ORDER BY roles.id ASC"
    );
    $roles = $result ? admin_fetch_all($result) : [];
}

$page_title = 'Manajemen Role';
$page_desc = 'Review role dan akses admin Bali Paradise.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'roles';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-roles-title">
    <?php
    $admin_eyebrow = 'Role & Access';
    $admin_title = 'Manajemen Role';
    $admin_title_id = 'admin-roles-title';
    $admin_subtitle = 'Role dibuat dari schema auth existing. Edit role langsung dibatasi agar akses admin tidak rusak.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Role Tersedia</h2>
            <p>Gunakan halaman Users untuk menetapkan role akun.</p>
          </div>
          <a class="admin-link" href="admin/users/index.php">Kelola User</a>
        </div>

        <div class="admin-data-table admin-data-table--compact">
          <table>
            <thead>
              <tr>
                <th>Role</th>
                <th>Label</th>
                <th>Total User</th>
                <th>Catatan Akses</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($roles as $role): ?>
                <tr>
                  <td><strong><?= e($role['name']) ?></strong></td>
                  <td><?= e($role['label']) ?></td>
                  <td><?= e(admin_format_number($role['total_users'])) ?></td>
                  <td>
                    <?php if ($role['name'] === 'admin'): ?>
                      <span class="admin-badge admin-badge--active">Admin Area</span>
                    <?php else: ?>
                      <span class="admin-badge admin-badge--neutral">User Area</span>
                    <?php endif; ?>
                  </td>
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
