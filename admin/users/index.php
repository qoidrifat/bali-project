<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$search = trim($_GET['q'] ?? '');
$users = [];
$roles = [];

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia. Data user belum bisa dimuat.';
} elseif (!auth_schema_ready($connection)) {
    http_response_code(503);
    $errorMessage = 'Schema auth belum lengkap. Review dan jalankan SQL auth manual terlebih dahulu.';
} else {
    $roles = admin_fetch_all(mysqli_query($connection, 'SELECT id, name, label FROM roles ORDER BY id ASC'));

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $targetUserId = $_POST['user_id'] ?? '';
        $roleId = $_POST['role_id'] ?? '';

        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
        } elseif (!is_valid_positive_id($targetUserId) || !is_valid_positive_id($roleId)) {
            $errorMessage = 'Data user atau role tidak valid.';
        } elseif ((int) $targetUserId === (int) ($adminUser['id'] ?? 0)) {
            $errorMessage = 'Role akun admin yang sedang login tidak boleh diubah dari halaman ini.';
        } else {
            $roleExists = false;
            foreach ($roles as $role) {
                if ((int) $role['id'] === (int) $roleId) {
                    $roleExists = true;
                    break;
                }
            }

            if (!$roleExists) {
                $errorMessage = 'Role yang dipilih tidak tersedia.';
            } else {
                $stmt = mysqli_prepare($connection, 'UPDATE users SET role_id = ? WHERE id = ? LIMIT 1');

                if (!$stmt) {
                    error_log('Admin user role update prepare failed: ' . mysqli_error($connection));
                    $errorMessage = 'Role user belum bisa diperbarui.';
                } else {
                    $newRoleId = (int) $roleId;
                    $targetId = (int) $targetUserId;
                    mysqli_stmt_bind_param($stmt, 'ii', $newRoleId, $targetId);

                    if (!mysqli_stmt_execute($stmt)) {
                        error_log('Admin user role update failed: ' . mysqli_stmt_error($stmt));
                        $errorMessage = 'Role user belum bisa diperbarui.';
                    } else {
                        admin_log_activity($connection, 'user.role_updated', 'users', $targetId, 'role_id=' . $newRoleId);
                        header('Location: index.php?notice=role-updated');
                        exit;
                    }

                    mysqli_stmt_close($stmt);
                }
            }
        }
    }

    if ($search !== '') {
        $like = '%' . $search . '%';
        $stmt = mysqli_prepare(
            $connection,
            "SELECT users.id, users.name, users.email, users.phone, users.city, users.role_id, roles.name AS role_name, roles.label AS role_label
             FROM users
             LEFT JOIN roles ON roles.id = users.role_id
             WHERE users.name LIKE ? OR users.email LIKE ?
             ORDER BY users.id DESC
             LIMIT 100"
        );

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $like, $like);
            mysqli_stmt_execute($stmt);
            $users = admin_fetch_all(mysqli_stmt_get_result($stmt));
            mysqli_stmt_close($stmt);
        }
    } else {
        $result = mysqli_query(
            $connection,
            "SELECT users.id, users.name, users.email, users.phone, users.city, users.role_id, roles.name AS role_name, roles.label AS role_label
             FROM users
             LEFT JOIN roles ON roles.id = users.role_id
             ORDER BY users.id DESC
             LIMIT 100"
        );
        $users = $result ? admin_fetch_all($result) : [];
    }
}

$page_title = 'Manajemen User';
$page_desc = 'Kelola akun dan role user Bali Paradise.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'users';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-users-title">
    <?php
    $admin_eyebrow = 'Access Control';
    $admin_title = 'Manajemen User';
    $admin_title_id = 'admin-users-title';
    $admin_subtitle = 'Lihat akun, cari user, dan ubah role secara aman tanpa mengubah password.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($notice === 'role-updated'): ?>
      <div class="admin-alert admin-alert--success" role="status">Role user berhasil diperbarui.</div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Daftar User</h2>
            <p><?= e(admin_format_number(count($users))) ?> akun ditampilkan.</p>
          </div>
        </div>

        <form class="admin-filterbar" action="admin/users/index.php" method="get">
          <input type="search" name="q" value="<?= e($search) ?>" placeholder="Cari nama atau email">
          <button class="btn btn--primary" type="submit">Cari</button>
          <?php if ($search !== ''): ?>
            <a class="btn btn--outline" href="admin/users/index.php">Reset</a>
          <?php endif; ?>
        </form>

        <?php if (!$users): ?>
          <div class="admin-empty">Belum ada user yang cocok.</div>
        <?php else: ?>
          <div class="admin-data-table admin-data-table--actions admin-data-table--users">
            <table>
              <thead>
                <tr>
                  <th>User</th>
                  <th>Kontak</th>
                  <th>Role</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($users as $user): ?>
                  <tr>
                    <td>
                      <strong><?= e($user['name'] ?: 'Tanpa nama') ?></strong>
                      <small>ID #<?= (int) $user['id'] ?></small>
                    </td>
                    <td>
                      <strong><?= e($user['email'] ?: '-') ?></strong>
                      <small><?= e(trim(($user['phone'] ?? '') . ' ' . ($user['city'] ?? '')) ?: 'Profil belum lengkap') ?></small>
                    </td>
                    <td>
                      <span class="admin-badge <?= e(admin_badge_class($user['role_name'] ?? 'user')) ?>">
                        <?= e($user['role_label'] ?? $user['role_name'] ?? 'User') ?>
                      </span>
                    </td>
                    <td>
                      <?php if ((int) $user['id'] === (int) ($adminUser['id'] ?? 0)): ?>
                        <span class="admin-badge admin-badge--muted">Current Admin</span>
                      <?php else: ?>
                        <form class="admin-inline-form" action="admin/users/index.php" method="post">
                          <?= csrf_field() ?>
                          <input type="hidden" name="user_id" value="<?= (int) $user['id'] ?>">
                          <select name="role_id" aria-label="Pilih role">
                            <?php foreach ($roles as $role): ?>
                              <option value="<?= (int) $role['id'] ?>" <?= (int) $user['role_id'] === (int) $role['id'] ? 'selected' : '' ?>>
                                <?= e($role['label']) ?>
                              </option>
                            <?php endforeach; ?>
                          </select>
                          <button type="submit">Update</button>
                        </form>
                      <?php endif; ?>
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
