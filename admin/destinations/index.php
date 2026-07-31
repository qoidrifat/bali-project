<?php
require_once __DIR__ . '/_helpers.php';

$adminUser = admin_destination_require();
$connection = db_connect();
$errorMessage = null;
$destinations = [];
$schema = [
    'has_is_active' => false,
    'has_deleted_at' => false,
];

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia. Data destinasi belum bisa dimuat.';
} else {
    $schema = admin_destination_schema($connection);
    $select = 'id_des, nama_des, gambar';
    $select .= $schema['has_is_active'] ? ', is_active' : ', 1 AS is_active';
    $select .= $schema['has_deleted_at'] ? ', deleted_at' : ', NULL AS deleted_at';
    $result = mysqli_query($connection, "SELECT {$select} FROM destination ORDER BY id_des DESC");

    if (!$result) {
        http_response_code(500);
        error_log('Admin destination list failed: ' . mysqli_error($connection));
        $errorMessage = 'Data destinasi belum bisa dimuat.';
    } else {
        while ($row = mysqli_fetch_assoc($result)) {
            $destinations[] = $row;
        }
        mysqli_free_result($result);
    }
}

$notice = $_GET['notice'] ?? '';
$noticeMessages = [
    'created' => 'Destinasi baru berhasil dibuat.',
    'updated' => 'Destinasi berhasil diperbarui.',
    'disabled' => 'Destinasi berhasil dinonaktifkan.',
];

$page_title = 'Kelola Destinasi';
$page_desc = 'CRUD destinasi wisata Bali Paradise.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'destinations';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="destinations-title">
    <?php
    $admin_eyebrow = 'Destination CRUD';
    $admin_title = 'Kelola Destinasi';
    $admin_title_id = 'destinations-title';
    $admin_subtitle = 'Buat, edit, dan nonaktifkan destinasi tanpa hard delete data lama.';
    $admin_action_html = '<a class="btn btn--primary" href="admin/destinations/create.php">Tambah Destinasi</a>';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($notice && isset($noticeMessages[$notice])): ?>
      <div class="admin-alert admin-alert--success" role="status"><?= e($noticeMessages[$notice]) ?></div>
    <?php endif; ?>

    <?php if (!$schema['has_is_active'] || !$schema['has_deleted_at']): ?>
      <div class="admin-alert admin-alert--warning" role="status">
        Soft-delete belum aktif. Review dan jalankan manual <code>database/2026_06_09_add_destination_admin_columns.sql</code>.
      </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Daftar Destinasi</h2>
            <p><?= e(admin_format_number(count($destinations))) ?> destinasi terdaftar.</p>
          </div>
        </div>

        <div class="admin-destination-table">
          <?php foreach ($destinations as $destination): ?>
            <?php $isDisabled = !empty($destination['deleted_at']) || (isset($destination['is_active']) && (int) $destination['is_active'] === 0); ?>
            <article class="admin-destination-row <?= $isDisabled ? 'is-disabled' : '' ?>">
              <img src="images/<?= e($destination['gambar']) ?>" alt="<?= e($destination['nama_des']) ?>">
              <div>
                <strong><?= e($destination['nama_des']) ?></strong>
                <span>ID #<?= (int) $destination['id_des'] ?> · <?= e($destination['gambar']) ?></span>
              </div>
              <span class="admin-badge <?= $isDisabled ? 'admin-badge--muted' : 'admin-badge--active' ?>">
                <?= $isDisabled ? 'Disabled' : 'Active' ?>
              </span>
              <div class="admin-row-actions">
                <a href="detail.php?id=<?= (int) $destination['id_des'] ?>">Preview</a>
                <a href="admin/destinations/edit.php?id=<?= (int) $destination['id_des'] ?>">Edit</a>
                <a class="is-danger" href="admin/destinations/delete.php?id=<?= (int) $destination['id_des'] ?>">Disable</a>
              </div>
            </article>
          <?php endforeach; ?>

          <?php if (!$destinations): ?>
            <div class="admin-empty">Belum ada destinasi.</div>
          <?php endif; ?>
        </div>
      </section>
    <?php endif; ?>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
