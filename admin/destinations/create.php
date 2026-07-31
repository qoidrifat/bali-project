<?php
require_once __DIR__ . '/_helpers.php';

$adminUser = admin_destination_require();
$connection = db_connect();
$errors = [];
$values = [
    'nama_des' => '',
    'detail_text' => '',
    'gambar' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $values['nama_des'] = trim($_POST['nama_des'] ?? '');
    $values['detail_text'] = trim($_POST['detail_text'] ?? '');

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
    }

    if ($values['nama_des'] === '' || strlen($values['nama_des']) > 100) {
        $errors[] = 'Nama destinasi wajib diisi maksimal 100 karakter.';
    }

    if ($values['detail_text'] === '' || strlen($values['detail_text']) < 20) {
        $errors[] = 'Deskripsi wajib diisi minimal 20 karakter.';
    }

    $mainImageError = admin_destination_validate_upload($_FILES['main_image'] ?? null, true);
    if ($mainImageError) {
        $errors[] = $mainImageError;
    }

    $galleryFiles = admin_destination_uploaded_files($_FILES['gallery_images'] ?? null);
    $galleryFiles = array_values(array_filter($galleryFiles, function ($file) {
        return ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }));

    if (count($galleryFiles) > 4) {
        $errors[] = 'Gambar galeri maksimal 4 file per submit.';
    }

    foreach ($galleryFiles as $file) {
        $galleryError = admin_destination_validate_upload($file, false);
        if ($galleryError) {
            $errors[] = $galleryError;
        }
    }

    if (!$connection) {
        $errors[] = 'Koneksi database belum tersedia.';
    }

    if (!$errors) {
        $mainImage = admin_destination_store_upload($_FILES['main_image'], $errors);
    }

    if (!$errors && $mainImage) {
        mysqli_begin_transaction($connection);

        $stmt = mysqli_prepare($connection, 'INSERT INTO destination (nama_des, gambar) VALUES (?, ?)');

        if (!$stmt) {
            error_log('Destination create prepare failed: ' . mysqli_error($connection));
            $errors[] = 'Destinasi belum bisa dibuat.';
        } else {
            mysqli_stmt_bind_param($stmt, 'ss', $values['nama_des'], $mainImage);

            if (!mysqli_stmt_execute($stmt)) {
                error_log('Destination create failed: ' . mysqli_stmt_error($stmt));
                $errors[] = 'Destinasi belum bisa dibuat.';
            }

            mysqli_stmt_close($stmt);
        }

        $destinationId = (int) mysqli_insert_id($connection);
        $detailId = 0;

        if (!$errors) {
            $detailHtml = admin_destination_detail_html($values['detail_text']);

            if (!admin_destination_insert_detail($connection, $destinationId, $detailHtml)) {
                $errors[] = 'Detail destinasi belum bisa disimpan.';
            } else {
                $detailId = (int) mysqli_insert_id($connection);
            }
        }

        if (!$errors && $detailId && $galleryFiles) {
            foreach ($galleryFiles as $file) {
                $galleryImage = admin_destination_store_upload($file, $errors);

                if ($galleryImage && !admin_destination_insert_gallery_image($connection, $detailId, $galleryImage)) {
                    $errors[] = 'Salah satu gambar galeri belum bisa disimpan.';
                    break;
                }
            }
        }

        if ($errors) {
            mysqli_rollback($connection);
        } else {
            mysqli_commit($connection);
            header('Location: index.php?notice=created');
            exit;
        }
    }
}

$page_title = 'Tambah Destinasi';
$page_desc = 'Tambah destinasi wisata baru.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'destinations';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="create-title">
    <?php
    $admin_eyebrow = 'Create Destination';
    $admin_title = 'Tambah Destinasi';
    $admin_title_id = 'create-title';
    $admin_subtitle = 'Upload gambar tervalidasi dan simpan deskripsi aman untuk halaman publik.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($errors): ?>
      <div class="admin-alert" role="alert">
        <strong>Destinasi belum bisa disimpan.</strong>
        <ul>
          <?php foreach (array_unique($errors) as $error): ?>
            <li><?= e($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <section class="admin-panel">
      <?php
      $formAction = 'admin/destinations/create.php';
      $formMode = 'create';
      $submitLabel = 'Buat Destinasi';
      include __DIR__ . '/_form.php';
      ?>
    </section>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
