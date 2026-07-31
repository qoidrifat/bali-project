<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$categories = [];
$tableReady = $connection && admin_table_exists($connection, 'destination_categories');

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia.';
} elseif ($tableReady) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
        } elseif ($action === 'create') {
            $name = trim((string) ($_POST['name'] ?? ''));
            $slug = admin_slugify($_POST['slug'] ?? $name);
            $description = trim((string) ($_POST['description'] ?? ''));
            $status = admin_normalize_status($_POST['status'] ?? 'active', ['active', 'inactive'], 'active');

            if ($name === '' || strlen($name) > 120) {
                $errorMessage = 'Nama kategori wajib diisi maksimal 120 karakter.';
            } else {
                $stmt = mysqli_prepare($connection, 'INSERT INTO destination_categories (name, slug, description, status) VALUES (?, ?, ?, ?)');

                if ($stmt) {
                    $descriptionValue = $description !== '' ? $description : null;
                    mysqli_stmt_bind_param($stmt, 'ssss', $name, $slug, $descriptionValue, $status);

                    if (mysqli_stmt_execute($stmt)) {
                        admin_log_activity($connection, 'category.created', 'destination_categories', mysqli_insert_id($connection), $name);
                        admin_redirect('index.php?notice=created');
                    }

                    error_log('Admin category create failed: ' . mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);
                }

                $errorMessage = 'Kategori belum bisa dibuat. Pastikan slug belum digunakan.';
            }
        } elseif ($action === 'status') {
            $categoryId = $_POST['category_id'] ?? '';
            $status = admin_normalize_status($_POST['status'] ?? 'active', ['active', 'inactive'], 'active');

            if (!is_valid_positive_id($categoryId)) {
                $errorMessage = 'Kategori tidak valid.';
            } else {
                $stmt = mysqli_prepare($connection, 'UPDATE destination_categories SET status = ? WHERE id = ? LIMIT 1');

                if ($stmt) {
                    $id = (int) $categoryId;
                    mysqli_stmt_bind_param($stmt, 'si', $status, $id);

                    if (mysqli_stmt_execute($stmt)) {
                        admin_log_activity($connection, 'category.status_updated', 'destination_categories', $id, $status);
                        admin_redirect('index.php?notice=status-updated');
                    }

                    error_log('Admin category status update failed: ' . mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);
                }

                $errorMessage = 'Status kategori belum bisa diperbarui.';
            }
        }
    }

    $categories = admin_fetch_all(mysqli_query($connection, 'SELECT id, name, slug, description, status FROM destination_categories ORDER BY id DESC LIMIT 100'));
}

$page_title = 'Kategori Destinasi';
$page_desc = 'Kelola kategori destinasi wisata.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'categories';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-categories-title">
    <?php
    $admin_eyebrow = 'Destination Taxonomy';
    $admin_title = 'Kategori Destinasi';
    $admin_title_id = 'admin-categories-title';
    $admin_subtitle = 'Siapkan pengelompokan destinasi untuk filtering dan struktur konten yang lebih profesional.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if (!$tableReady): ?>
      <div class="admin-alert admin-alert--warning" role="status">
        Tabel <code>destination_categories</code> belum tersedia. Review manual SQL <code>database/2026_06_09_create_admin_content_tables.sql</code> sebelum mengaktifkan CRUD kategori.
      </div>
    <?php endif; ?>

    <?php $flashMessage = admin_flash_message($notice); ?>
    <?php if ($flashMessage): ?>
      <div class="admin-alert admin-alert--success" role="status"><?= e($flashMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <?php if ($tableReady): ?>
        <section class="admin-panel admin-panel--form">
          <div class="admin-panel__head">
            <div>
              <h2>Tambah Kategori</h2>
              <p>Gunakan nama singkat, slug stabil, dan status publikasi yang jelas.</p>
            </div>
          </div>
          <form class="admin-form" action="admin/categories/index.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create">
            <div class="admin-form__grid">
              <div class="admin-field">
                <label for="name">Nama kategori</label>
                <input id="name" name="name" type="text" maxlength="120" required>
              </div>
              <div class="admin-field">
                <label for="slug">Slug</label>
                <input id="slug" name="slug" type="text" maxlength="140" placeholder="auto jika kosong">
              </div>
              <div class="admin-field">
                <label for="status">Status</label>
                <select id="status" name="status">
                  <option value="active">active</option>
                  <option value="inactive">inactive</option>
                </select>
              </div>
              <div class="admin-field admin-field--wide">
                <label for="description">Deskripsi</label>
                <textarea id="description" name="description" rows="3" maxlength="1000"></textarea>
              </div>
            </div>
            <div class="admin-form__actions">
              <button class="btn btn--primary" type="submit">Simpan Kategori</button>
            </div>
          </form>
        </section>
      <?php endif; ?>

      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Daftar Kategori</h2>
            <p><?= e(admin_format_number(count($categories))) ?> kategori tersedia.</p>
          </div>
        </div>

        <?php if (!$tableReady): ?>
          <div class="admin-empty">Kategori belum aktif karena tabel belum dibuat.</div>
        <?php elseif (!$categories): ?>
          <div class="admin-empty">Belum ada kategori destinasi.</div>
        <?php else: ?>
          <div class="admin-data-table admin-data-table--actions admin-data-table--compact">
            <table>
              <thead><tr><th>Nama</th><th>Slug</th><th>Deskripsi</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php foreach ($categories as $category): ?>
                  <tr>
                    <td><strong><?= e($category['name']) ?></strong></td>
                    <td><?= e($category['slug']) ?></td>
                    <td><?= e($category['description'] ?? '-') ?></td>
                    <td><span class="admin-badge <?= e(admin_badge_class($category['status'] ?? 'active')) ?>"><?= e($category['status'] ?? 'active') ?></span></td>
                    <td>
                      <form class="admin-inline-form" action="admin/categories/index.php" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="status">
                        <input type="hidden" name="category_id" value="<?= (int) $category['id'] ?>">
                        <select name="status" aria-label="Status kategori">
                          <?php foreach (['active', 'inactive'] as $status): ?>
                            <option value="<?= e($status) ?>" <?= ($category['status'] ?? 'active') === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                          <?php endforeach; ?>
                        </select>
                        <button type="submit">Update</button>
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
