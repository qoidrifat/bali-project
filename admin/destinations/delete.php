<?php
require_once __DIR__ . '/_helpers.php';

$adminUser = admin_destination_require();
$connection = db_connect();
$errors = [];
$destination = null;
$id = null;

if (!admin_destination_is_valid_id($_GET['id'] ?? null)) {
    http_response_code(400);
    $errors[] = 'ID destinasi tidak valid.';
} else {
    $id = (int) $_GET['id'];
}

if (!$connection) {
    http_response_code(500);
    $errors[] = 'Koneksi database belum tersedia.';
} elseif ($id) {
    $destination = admin_destination_fetch($connection, $id);

    if (!$destination) {
        http_response_code(404);
        $errors[] = 'Destinasi tidak ditemukan.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $destination) {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
    }

    $schema = $connection ? admin_destination_schema($connection) : [];

    if (empty($schema['has_is_active']) || empty($schema['has_deleted_at'])) {
        $errors[] = 'Soft-delete belum aktif. Jalankan migration manual destination admin terlebih dahulu.';
    }

    if (!$errors) {
        $stmt = mysqli_prepare($connection, 'UPDATE destination SET is_active = 0, deleted_at = NOW() WHERE id_des = ?');

        if (!$stmt) {
            error_log('Destination disable prepare failed: ' . mysqli_error($connection));
            $errors[] = 'Destinasi belum bisa dinonaktifkan.';
        } else {
            mysqli_stmt_bind_param($stmt, 'i', $id);

            if (!mysqli_stmt_execute($stmt)) {
                error_log('Destination disable failed: ' . mysqli_stmt_error($stmt));
                $errors[] = 'Destinasi belum bisa dinonaktifkan.';
            }

            mysqli_stmt_close($stmt);
        }
    }

    if (!$errors) {
        header('Location: index.php?notice=disabled');
        exit;
    }
}

$page_title = 'Disable Destinasi';
$page_desc = 'Konfirmasi nonaktif destinasi.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'destinations';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="delete-title">
    <?php
    $admin_eyebrow = 'Soft Delete';
    $admin_title = 'Nonaktifkan Destinasi';
    $admin_title_id = 'delete-title';
    $admin_subtitle = 'Aksi ini tidak menghapus row database dan tidak menghapus file gambar.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($errors): ?>
      <div class="admin-alert" role="alert">
        <strong>Aksi belum bisa diproses.</strong>
        <ul>
          <?php foreach (array_unique($errors) as $error): ?>
            <li><?= e($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($destination): ?>
      <section class="admin-panel admin-confirm">
        <img src="images/<?= e($destination['gambar']) ?>" alt="<?= e($destination['nama_des']) ?>">
        <div>
          <h2><?= e($destination['nama_des']) ?></h2>
          <p>Konfirmasi untuk menonaktifkan destinasi ini dari daftar publik setelah migration soft-delete aktif.</p>
          <form class="admin-form__actions" action="admin/destinations/delete.php?id=<?= (int) $id ?>" method="post">
            <?= csrf_field() ?>
            <button class="btn btn--primary admin-danger-button" type="submit">Ya, Nonaktifkan</button>
            <a class="btn btn--outline" href="admin/destinations/index.php">Batal</a>
          </form>
        </div>
      </section>
    <?php else: ?>
      <a class="btn btn--outline" href="admin/destinations/index.php">Kembali</a>
    <?php endif; ?>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
