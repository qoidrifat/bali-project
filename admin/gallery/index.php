<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$images = [];
$galleryItems = [];
$destinations = [];
$galleryTableReady = $connection && admin_table_exists($connection, 'galleries');

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia. Galeri belum bisa dimuat.';
} elseif (!admin_table_exists($connection, 'detail_image')) {
    $errorMessage = 'Tabel galeri legacy detail_image belum tersedia.';
} else {
    if ($galleryTableReady && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
        } elseif ($action === 'create') {
            $destinationId = is_valid_positive_id($_POST['destination_id'] ?? '') ? (int) $_POST['destination_id'] : null;
            $title = trim((string) ($_POST['title'] ?? ''));
            $image = trim(str_replace('\\', '/', (string) ($_POST['image'] ?? '')));
            $image = preg_replace('#^/?images/#', '', $image);
            $altText = trim((string) ($_POST['alt_text'] ?? ''));
            $sortOrder = filter_var($_POST['sort_order'] ?? 0, FILTER_VALIDATE_INT);
            $status = admin_normalize_status($_POST['status'] ?? 'active', ['active', 'inactive'], 'active');

            if ($image === '' || strpos($image, '..') !== false || !preg_match('/\.(jpe?g|png|webp|gif)$/i', $image)) {
                $errorMessage = 'Path gambar tidak valid. Gunakan file gambar dari folder images.';
            } else {
                $stmt = mysqli_prepare(
                    $connection,
                    'INSERT INTO galleries (destination_id, title, image, alt_text, sort_order, status) VALUES (?, ?, ?, ?, ?, ?)'
                );

                if ($stmt) {
                    $titleValue = $title !== '' ? $title : null;
                    $altTextValue = $altText !== '' ? $altText : null;
                    $sortValue = $sortOrder !== false ? (int) $sortOrder : 0;
                    mysqli_stmt_bind_param($stmt, 'isssis', $destinationId, $titleValue, $image, $altTextValue, $sortValue, $status);

                    if (mysqli_stmt_execute($stmt)) {
                        admin_log_activity($connection, 'gallery.created', 'galleries', mysqli_insert_id($connection), $image);
                        admin_redirect('index.php?notice=created');
                    }

                    error_log('Admin gallery create failed: ' . mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);
                }

                $errorMessage = 'Galeri belum bisa dibuat.';
            }
        } elseif ($action === 'status') {
            $galleryId = $_POST['gallery_id'] ?? '';
            $status = admin_normalize_status($_POST['status'] ?? 'active', ['active', 'inactive'], 'active');

            if (!is_valid_positive_id($galleryId)) {
                $errorMessage = 'Galeri tidak valid.';
            } else {
                $stmt = mysqli_prepare($connection, 'UPDATE galleries SET status = ? WHERE id = ? LIMIT 1');

                if ($stmt) {
                    $id = (int) $galleryId;
                    mysqli_stmt_bind_param($stmt, 'si', $status, $id);

                    if (mysqli_stmt_execute($stmt)) {
                        admin_log_activity($connection, 'gallery.status_updated', 'galleries', $id, $status);
                        admin_redirect('index.php?notice=status-updated');
                    }

                    error_log('Admin gallery status update failed: ' . mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);
                }

                $errorMessage = 'Status galeri belum bisa diperbarui.';
            }
        }
    }

    if ($galleryTableReady) {
        $galleryItems = admin_fetch_all(mysqli_query(
            $connection,
            'SELECT g.*, d.nama_des AS destination_name
             FROM galleries g
             LEFT JOIN destination d ON d.id_des = g.destination_id
             ORDER BY g.sort_order ASC, g.id DESC
             LIMIT 120'
        ));
    }

    if (admin_table_exists($connection, 'destination')) {
        $destinations = admin_fetch_all(mysqli_query($connection, 'SELECT id_des, nama_des FROM destination ORDER BY nama_des ASC LIMIT 200'));
    }

    $result = mysqli_query(
        $connection,
        "SELECT detail_image.id_img, detail_image.gambar, detail.id_des, destination.nama_des
         FROM detail_image
         LEFT JOIN detail ON detail.id_detail = detail_image.id_detail
         LEFT JOIN destination ON destination.id_des = detail.id_des
         ORDER BY detail_image.id_img DESC
         LIMIT 120"
    );
    $images = $result ? admin_fetch_all($result) : [];
}

$page_title = 'Manajemen Galeri';
$page_desc = 'Kelola aset gambar destinasi.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'gallery';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-gallery-title">
    <?php
    $admin_eyebrow = 'Destination Media';
    $admin_title = 'Manajemen Galeri';
    $admin_title_id = 'admin-gallery-title';
    $admin_subtitle = 'Galeri saat ini memakai tabel legacy detail_image dan upload melalui CRUD destinasi.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php $flashMessage = admin_flash_message($notice); ?>
    <?php if ($flashMessage): ?>
      <div class="admin-alert admin-alert--success" role="status"><?= e($flashMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <?php if ($galleryTableReady): ?>
        <section class="admin-panel admin-panel--form">
          <div class="admin-panel__head">
            <div>
              <h2>Tambah Galeri Baru</h2>
              <p>Gunakan gambar yang sudah tersedia di folder <code>images</code>. Upload baru tetap lewat CRUD destinasi.</p>
            </div>
          </div>
          <form class="admin-form" action="admin/gallery/index.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create">
            <div class="admin-form__grid">
              <div class="admin-field">
                <label for="destination-id">Destinasi</label>
                <select id="destination-id" name="destination_id">
                  <option value="">Tidak terkait</option>
                  <?php foreach ($destinations as $destination): ?>
                    <option value="<?= (int) $destination['id_des'] ?>"><?= e($destination['nama_des']) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="admin-field">
                <label for="image">Path gambar</label>
                <input id="image" name="image" type="text" maxlength="255" required placeholder="nama-file.jpg">
              </div>
              <div class="admin-field">
                <label for="title">Judul</label>
                <input id="title" name="title" type="text" maxlength="160">
              </div>
              <div class="admin-field">
                <label for="sort-order">Urutan</label>
                <input id="sort-order" name="sort_order" type="number" value="0">
              </div>
              <div class="admin-field">
                <label for="status">Status</label>
                <select id="status" name="status">
                  <option value="active">active</option>
                  <option value="inactive">inactive</option>
                </select>
              </div>
              <div class="admin-field admin-field--wide">
                <label for="alt-text">Alt text</label>
                <input id="alt-text" name="alt_text" type="text" maxlength="190">
              </div>
            </div>
            <div class="admin-form__actions">
              <button class="btn btn--primary" type="submit">Simpan Galeri</button>
            </div>
          </form>
        </section>

        <section class="admin-panel">
          <div class="admin-panel__head">
            <div>
              <h2>Galeri Baru</h2>
              <p><?= e(admin_format_number(count($galleryItems))) ?> item dari tabel <code>galleries</code>.</p>
            </div>
          </div>
          <?php if (!$galleryItems): ?>
            <div class="admin-empty">Belum ada item galeri baru.</div>
          <?php else: ?>
            <div class="admin-feature-grid">
              <?php foreach ($galleryItems as $item): ?>
                <?php $imagePath = public_image_path($item['image'] ?? ''); ?>
                <article class="admin-feature-card">
                  <img class="admin-feature-card__image" src="<?= e($imagePath) ?>" alt="<?= e($item['alt_text'] ?: ($item['title'] ?: 'Galeri destinasi')) ?>" loading="lazy">
                  <span><?= e($item['destination_name'] ?? 'General') ?></span>
                  <h2><?= e($item['title'] ?: $item['image']) ?></h2>
                  <p><?= e($item['image']) ?></p>
                  <form class="admin-inline-form" action="admin/gallery/index.php" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="action" value="status">
                    <input type="hidden" name="gallery_id" value="<?= (int) $item['id'] ?>">
                    <select name="status" aria-label="Status galeri">
                      <?php foreach (['active', 'inactive'] as $status): ?>
                        <option value="<?= e($status) ?>" <?= $item['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
                      <?php endforeach; ?>
                    </select>
                    <button type="submit">Update</button>
                  </form>
                </article>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>
      <?php endif; ?>

      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Galeri Destinasi</h2>
            <p><?= e(admin_format_number(count($images))) ?> gambar ditampilkan.</p>
          </div>
          <a class="admin-link" href="admin/destinations/index.php">Kelola Destinasi</a>
        </div>

        <?php if (!$images): ?>
          <div class="admin-empty">Belum ada gambar galeri.</div>
        <?php else: ?>
          <div class="admin-feature-grid">
            <?php foreach ($images as $image): ?>
              <article class="admin-feature-card">
                <img class="admin-feature-card__image" src="images/<?= e($image['gambar']) ?>" alt="<?= e($image['nama_des'] ?? 'Galeri destinasi') ?>">
                <span>ID #<?= (int) $image['id_img'] ?></span>
                <h2><?= e($image['nama_des'] ?? 'Destinasi') ?></h2>
                <p><?= e($image['gambar']) ?></p>
              </article>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </section>
    <?php endif; ?>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
