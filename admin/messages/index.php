<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$messages = [];
$tableReady = $connection && admin_table_exists($connection, 'contact_messages');

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia.';
} elseif ($tableReady) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $messageId = $_POST['message_id'] ?? '';
        $status = admin_normalize_status($_POST['status'] ?? '', ['new', 'read', 'archived'], 'read');

        if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
            $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
        } elseif (!is_valid_positive_id($messageId)) {
            $errorMessage = 'Pesan tidak valid.';
        } else {
            $stmt = mysqli_prepare($connection, 'UPDATE contact_messages SET status = ? WHERE id = ? LIMIT 1');

            if ($stmt) {
                $id = (int) $messageId;
                mysqli_stmt_bind_param($stmt, 'si', $status, $id);

                if (mysqli_stmt_execute($stmt)) {
                    admin_log_activity($connection, 'message.status_updated', 'contact_messages', $id, $status);
                    header('Location: index.php?notice=updated');
                    exit;
                }

                error_log('Admin message update failed: ' . mysqli_stmt_error($stmt));
                mysqli_stmt_close($stmt);
            }

            $errorMessage = 'Status pesan belum bisa diperbarui.';
        }
    }

    $messages = admin_fetch_all(mysqli_query($connection, 'SELECT id, name, email, topic, message, status, created_at FROM contact_messages ORDER BY id DESC LIMIT 100'));
}

$page_title = 'Pesan Masuk';
$page_desc = 'Kelola contact message.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'messages';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-messages-title">
    <?php
    $admin_eyebrow = 'Inbox';
    $admin_title = 'Manajemen Pesan Masuk';
    $admin_title_id = 'admin-messages-title';
    $admin_subtitle = 'Pantau pesan dari contact form dan tandai status follow-up.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if ($notice === 'updated'): ?>
      <div class="admin-alert admin-alert--success" role="status">Status pesan berhasil diperbarui.</div>
    <?php endif; ?>

    <?php if (!$tableReady): ?>
      <div class="admin-alert admin-alert--warning" role="status">
        Tabel <code>contact_messages</code> belum tersedia. Review manual SQL <code>database/2026_06_09_create_contact_messages_table.sql</code> untuk mengaktifkan inbox.
      </div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Pesan Contact</h2>
            <p><?= e(admin_format_number(count($messages))) ?> pesan ditampilkan.</p>
          </div>
        </div>

        <?php if (!$tableReady): ?>
          <div class="admin-empty">Inbox belum aktif karena tabel belum dibuat.</div>
        <?php elseif (!$messages): ?>
          <div class="admin-empty">Belum ada pesan masuk.</div>
        <?php else: ?>
          <div class="admin-data-table admin-data-table--actions">
            <table>
              <thead><tr><th>Pengirim</th><th>Topik</th><th>Pesan</th><th>Status</th><th>Aksi</th></tr></thead>
              <tbody>
                <?php foreach ($messages as $message): ?>
                  <tr>
                    <td><strong><?= e($message['name']) ?></strong><small><?= e($message['email']) ?> · <?= e($message['created_at']) ?></small></td>
                    <td><?= e($message['topic']) ?></td>
                    <td><?= e(admin_excerpt($message['message'], 110)) ?></td>
                    <td><span class="admin-badge <?= e(admin_badge_class($message['status'])) ?>"><?= e($message['status']) ?></span></td>
                    <td>
                      <form class="admin-inline-form" action="admin/messages/index.php" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="message_id" value="<?= (int) $message['id'] ?>">
                        <select name="status">
                          <?php foreach (['new', 'read', 'archived'] as $status): ?>
                            <option value="<?= e($status) ?>" <?= $message['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
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
