<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$articles = [];
$tableReady = $connection && admin_table_exists($connection, 'articles');

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia.';
} elseif ($tableReady) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
        } elseif ($action === 'create') {
            $title = trim((string) ($_POST['title'] ?? ''));
            $slug = admin_slugify($_POST['slug'] ?? $title);
            $excerpt = trim((string) ($_POST['excerpt'] ?? ''));
            $content = trim((string) ($_POST['content'] ?? ''));
            $status = admin_normalize_status($_POST['status'] ?? 'draft', ['draft', 'published', 'archived'], 'draft');
            $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : null;

            if ($title === '' || strlen($title) > 190) {
                $errorMessage = 'Judul artikel wajib diisi maksimal 190 karakter.';
            } else {
                $stmt = mysqli_prepare(
                    $connection,
                    'INSERT INTO articles (title, slug, excerpt, content, status, published_at) VALUES (?, ?, ?, ?, ?, ?)'
                );

                if ($stmt) {
                    $excerptValue = $excerpt !== '' ? $excerpt : null;
                    $contentValue = $content !== '' ? $content : null;
                    mysqli_stmt_bind_param($stmt, 'ssssss', $title, $slug, $excerptValue, $contentValue, $status, $publishedAt);

                    if (mysqli_stmt_execute($stmt)) {
                        admin_log_activity($connection, 'article.created', 'articles', mysqli_insert_id($connection), $title);
                        admin_redirect('index.php?notice=created');
                    }

                    error_log('Admin article create failed: ' . mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);
                }

                $errorMessage = 'Artikel belum bisa dibuat. Pastikan slug belum digunakan.';
            }
        } elseif ($action === 'status') {
            $articleId = $_POST['article_id'] ?? '';
            $status = admin_normalize_status($_POST['status'] ?? 'draft', ['draft', 'published', 'archived'], 'draft');
            $publishedAt = $status === 'published' ? date('Y-m-d H:i:s') : null;

            if (!is_valid_positive_id($articleId)) {
                $errorMessage = 'Artikel tidak valid.';
            } else {
                $stmt = mysqli_prepare(
                    $connection,
                    "UPDATE articles
                     SET status = ?, published_at = CASE WHEN ? = 'published' AND published_at IS NULL THEN ? ELSE published_at END
                     WHERE id = ?
                     LIMIT 1"
                );

                if ($stmt) {
                    $id = (int) $articleId;
                    mysqli_stmt_bind_param($stmt, 'sssi', $status, $status, $publishedAt, $id);

                    if (mysqli_stmt_execute($stmt)) {
                        admin_log_activity($connection, 'article.status_updated', 'articles', $id, $status);
                        admin_redirect('index.php?notice=status-updated');
                    }

                    error_log('Admin article status update failed: ' . mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);
                }

                $errorMessage = 'Status artikel belum bisa diperbarui.';
            }
        }
    }

    $articles = admin_fetch_all(mysqli_query($connection, 'SELECT id, title, slug, status, published_at, excerpt FROM articles ORDER BY id DESC LIMIT 100'));
}

$page_title = 'Artikel Wisata';
$page_desc = 'Kelola artikel dan blog wisata.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'articles';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-articles-title">
    <?php
    $admin_eyebrow = 'Content Marketing';
    $admin_title = 'Manajemen Artikel / Blog';
    $admin_title_id = 'admin-articles-title';
    $admin_subtitle = 'Siapkan artikel wisata, SEO konten, dan publikasi bertahap.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if (!$tableReady): ?>
      <div class="admin-alert admin-alert--warning" role="status">
        Tabel <code>articles</code> belum tersedia. Review manual SQL <code>database/2026_06_09_create_admin_content_tables.sql</code> sebelum mengaktifkan CRUD artikel.
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
              <h2>Tambah Artikel</h2>
              <p>Buat draft artikel wisata untuk SEO dan konten Bali Paradise.</p>
            </div>
          </div>
          <form class="admin-form" action="admin/articles/index.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create">
            <div class="admin-form__grid">
              <div class="admin-field">
                <label for="title">Judul</label>
                <input id="title" name="title" type="text" maxlength="190" required>
              </div>
              <div class="admin-field">
                <label for="slug">Slug</label>
                <input id="slug" name="slug" type="text" maxlength="210" placeholder="auto jika kosong">
              </div>
              <div class="admin-field">
                <label for="status">Status</label>
                <select id="status" name="status">
                  <option value="draft">draft</option>
                  <option value="published">published</option>
                  <option value="archived">archived</option>
                </select>
              </div>
              <div class="admin-field admin-field--wide">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="3" maxlength="600"></textarea>
              </div>
              <div class="admin-field admin-field--wide">
                <label for="content">Konten</label>
                <textarea id="content" name="content" rows="7"></textarea>
              </div>
            </div>
            <div class="admin-form__actions">
              <button class="btn btn--primary" type="submit">Simpan Artikel</button>
            </div>
          </form>
        </section>
      <?php endif; ?>

      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Daftar Artikel</h2>
            <p><?= e(admin_format_number(count($articles))) ?> artikel tersedia.</p>
          </div>
        </div>

        <?php if (!$tableReady): ?>
          <div class="admin-empty">Artikel belum aktif karena tabel belum dibuat.</div>
        <?php elseif (!$articles): ?>
          <div class="admin-empty">Belum ada artikel wisata.</div>
        <?php else: ?>
          <div class="admin-data-table admin-data-table--actions">
            <table>
              <thead><tr><th>Artikel</th><th>Slug</th><th>Status</th><th>Publikasi</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php foreach ($articles as $article): ?>
                  <tr>
                    <td><strong><?= e($article['title']) ?></strong><small><?= e(admin_excerpt($article['excerpt'] ?? '', 80)) ?></small></td>
                    <td><?= e($article['slug']) ?></td>
                    <td><span class="admin-badge <?= e(admin_badge_class($article['status'])) ?>"><?= e($article['status']) ?></span></td>
                    <td><?= e($article['published_at'] ?? '-') ?></td>
                    <td>
                      <form class="admin-inline-form" action="admin/articles/index.php" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="status">
                        <input type="hidden" name="article_id" value="<?= (int) $article['id'] ?>">
                        <select name="status" aria-label="Status artikel">
                          <?php foreach (['draft', 'published', 'archived'] as $status): ?>
                            <option value="<?= e($status) ?>" <?= $article['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
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
