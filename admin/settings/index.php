<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$settings = [];
$tableReady = $connection && admin_table_exists($connection, 'site_settings');

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia.';
} elseif ($tableReady) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
        } else {
            $key = trim((string) ($_POST['key'] ?? ''));
            $value = trim((string) ($_POST['value'] ?? ''));
            $type = admin_normalize_status($_POST['type'] ?? 'text', ['text', 'url', 'email', 'phone', 'longtext'], 'text');

            if (!preg_match('/^[a-z0-9_]{2,120}$/', $key)) {
                $errorMessage = 'Key settings hanya boleh huruf kecil, angka, dan underscore.';
            } elseif ($type === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errorMessage = 'Value email tidak valid.';
            } elseif ($type === 'url' && $value !== '' && safe_external_url($value) === '#') {
                $errorMessage = 'Value URL tidak valid.';
            } else {
                $stmt = mysqli_prepare(
                    $connection,
                    'INSERT INTO site_settings (`key`, value, type) VALUES (?, ?, ?)
                     ON DUPLICATE KEY UPDATE value = VALUES(value), type = VALUES(type)'
                );

                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, 'sss', $key, $value, $type);

                    if (mysqli_stmt_execute($stmt)) {
                        admin_log_activity($connection, 'setting.saved', 'site_settings', $key, $type);
                        admin_redirect('index.php?notice=settings-saved');
                    }

                    error_log('Admin setting upsert failed: ' . mysqli_stmt_error($stmt));
                    mysqli_stmt_close($stmt);
                }

                $errorMessage = 'Settings belum bisa disimpan.';
            }
        }
    }

    $settings = admin_fetch_all(mysqli_query($connection, 'SELECT id, `key`, value, type FROM site_settings ORDER BY `key` ASC LIMIT 200'));
}

$recommended = [
    'site_name' => 'Nama website',
    'tagline' => 'Tagline pendek',
    'contact_email' => 'Email kontak',
    'phone' => 'Nomor telepon',
    'address' => 'Alamat kantor',
    'instagram' => 'URL Instagram',
    'whatsapp' => 'Nomor WhatsApp',
    'footer_text' => 'Teks footer',
];

$page_title = 'Pengaturan Website';
$page_desc = 'Kelola konfigurasi website.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'settings';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-settings-title">
    <?php
    $admin_eyebrow = 'Site Configuration';
    $admin_title = 'Pengaturan Website';
    $admin_title_id = 'admin-settings-title';
    $admin_subtitle = 'Kelola konfigurasi branding dan kontak setelah tabel settings diaktifkan.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if (!$tableReady): ?>
      <div class="admin-alert admin-alert--warning" role="status">
        Tabel <code>site_settings</code> belum tersedia. Review manual SQL <code>database/2026_06_09_create_admin_content_tables.sql</code> untuk mengaktifkan settings.
      </div>
    <?php endif; ?>

    <?php $flashMessage = admin_flash_message($notice); ?>
    <?php if ($flashMessage): ?>
      <div class="admin-alert admin-alert--success" role="status"><?= e($flashMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-grid">
        <article class="admin-panel">
          <div class="admin-panel__head">
            <div>
              <h2>Settings Aktif</h2>
              <p><?= e(admin_format_number(count($settings))) ?> key tersimpan.</p>
            </div>
          </div>

          <?php if (!$tableReady): ?>
            <div class="admin-empty">Settings belum aktif karena tabel belum dibuat.</div>
          <?php elseif (!$settings): ?>
            <div class="admin-empty">Belum ada settings tersimpan.</div>
          <?php else: ?>
            <div class="admin-data-table admin-data-table--settings">
              <table>
                <thead><tr><th>Key</th><th>Value</th><th>Type</th></tr></thead>
                <tbody>
                  <?php foreach ($settings as $setting): ?>
                    <tr>
                      <td><strong><?= e($setting['key']) ?></strong></td>
                      <td><?= e(admin_excerpt($setting['value'] ?? '', 120)) ?></td>
                      <td><?= e($setting['type'] ?? 'text') ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>
        </article>

        <article class="admin-panel">
          <div class="admin-panel__head">
            <div>
              <h2>Rekomendasi Key</h2>
              <p>Key berikut cocok untuk website wisata Bali.</p>
            </div>
          </div>
          <div class="admin-table">
            <?php foreach ($recommended as $key => $label): ?>
              <div class="admin-table__row">
                <span><strong><?= e($key) ?></strong><small><?= e($label) ?></small></span>
                <b>text</b>
              </div>
            <?php endforeach; ?>
          </div>
        </article>
      </section>

      <?php if ($tableReady): ?>
        <section class="admin-panel admin-panel--form">
          <div class="admin-panel__head">
            <div>
              <h2>Simpan Settings</h2>
              <p>Upsert key-value konfigurasi website tanpa menyimpan credential sensitif.</p>
            </div>
          </div>
          <form class="admin-form" action="admin/settings/index.php" method="post">
            <?= csrf_field() ?>
            <div class="admin-form__grid">
              <div class="admin-field">
                <label for="setting-key">Key</label>
                <input id="setting-key" name="key" type="text" maxlength="120" pattern="[a-z0-9_]{2,120}" required placeholder="site_name">
              </div>
              <div class="admin-field">
                <label for="setting-type">Type</label>
                <select id="setting-type" name="type">
                  <option value="text">text</option>
                  <option value="longtext">longtext</option>
                  <option value="email">email</option>
                  <option value="phone">phone</option>
                  <option value="url">url</option>
                </select>
              </div>
              <div class="admin-field admin-field--wide">
                <label for="setting-value">Value</label>
                <textarea id="setting-value" name="value" rows="4"></textarea>
                <small>Jangan simpan password, API key, token, atau credential production di sini.</small>
              </div>
            </div>
            <div class="admin-form__actions">
              <button class="btn btn--primary" type="submit">Simpan Settings</button>
            </div>
          </form>
        </section>
      <?php endif; ?>
    <?php endif; ?>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
