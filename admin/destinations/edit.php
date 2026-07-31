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

$values = [
    'nama_des' => $destination['nama_des'] ?? '',
    'detail_text' => admin_destination_detail_text($destination['detail_desc'] ?? ''),
    'gambar' => $destination['gambar'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $destination) {
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

    $mainImageError = admin_destination_validate_upload($_FILES['main_image'] ?? null, false);
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

    if (!$errors) {
        $mainImage = admin_destination_store_upload($_FILES['main_image'] ?? null, $errors);
        $imagePath = $mainImage ?: $destination['gambar'];
    }

    if (!$errors) {
        $schema = admin_destination_schema($connection);
        $sql = 'UPDATE destination SET nama_des = ?, gambar = ?';
        $sql .= $schema['has_updated_at'] ? ', updated_at = NOW()' : '';
        $sql .= ' WHERE id_des = ?';

        mysqli_begin_transaction($connection);
        $stmt = mysqli_prepare($connection, $sql);

        if (!$stmt) {
            error_log('Destination update prepare failed: ' . mysqli_error($connection));
            $errors[] = 'Destinasi belum bisa diperbarui.';
        } else {
            mysqli_stmt_bind_param($stmt, 'ssi', $values['nama_des'], $imagePath, $id);

            if (!mysqli_stmt_execute($stmt)) {
                error_log('Destination update failed: ' . mysqli_stmt_error($stmt));
                $errors[] = 'Destinasi belum bisa diperbarui.';
            }

            mysqli_stmt_close($stmt);
        }

        if (!$errors) {
            $detailHtml = admin_destination_detail_html($values['detail_text']);
            $detailId = (int) ($destination['id_detail'] ?? 0);

            if (!admin_destination_upsert_detail($connection, $id, $detailId, $detailHtml)) {
                $errors[] = 'Detail destinasi belum bisa diperbarui.';
            } elseif (!$detailId) {
                $detailId = (int) mysqli_insert_id($connection);
            }
        }

        if (!$errors && !empty($detailId) && $galleryFiles) {
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
            header('Location: index.php?notice=updated');
            exit;
        }
    }
}

$page_title = 'Edit Destinasi';
$page_desc = 'Edit destinasi wisata.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'destinations';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="edit-title">
    <?php
    $admin_eyebrow = 'Edit Destination';
    $admin_title = 'Edit Destinasi';
    $admin_title_id = 'edit-title';
    $admin_subtitle = 'Perbarui nama, deskripsi, dan gambar tanpa menghapus file lama otomatis.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($errors): ?>
      <div class="admin-alert" role="alert">
        <strong>Periksa kembali data.</strong>
        <ul>
          <?php foreach (array_unique($errors) as $error): ?>
            <li><?= e($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($destination): ?>
      <section class="admin-panel">
        <?php
        $formAction = 'admin/destinations/edit.php?id=' . (int) $id;
        $formMode = 'edit';
        $submitLabel = 'Update Destinasi';
        include __DIR__ . '/_form.php';
        ?>
      </section>
    <?php else: ?>
      <a class="btn btn--outline" href="admin/destinations/index.php">Kembali</a>
    <?php endif; ?>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
