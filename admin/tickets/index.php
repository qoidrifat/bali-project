<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';
require_once __DIR__ . '/../../booking/_helpers.php';

$adminUser = require_admin('../../login.php', '../../');
$connection = db_connect();
$errorMessage = null;
$notice = $_GET['notice'] ?? '';
$ticketTableReady = $connection && admin_table_exists($connection, 'tickets');
$tickets = [];
$legacySources = [];
$destinations = [];

if (!$connection) {
    http_response_code(500);
    $errorMessage = 'Koneksi database belum tersedia.';
} else {
    if ($ticketTableReady) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_POST['action'] ?? '';

            if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
                $errorMessage = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
            } elseif ($action === 'create') {
                $destinationId = is_valid_positive_id($_POST['destination_id'] ?? '') ? (int) $_POST['destination_id'] : null;
                $name = trim((string) ($_POST['name'] ?? ''));
                $type = trim((string) ($_POST['type'] ?? ''));
                $price = filter_var($_POST['price'] ?? 0, FILTER_VALIDATE_FLOAT);
                $quota = trim((string) ($_POST['quota'] ?? ''));
                $quotaValue = $quota !== '' && ctype_digit($quota) ? (int) $quota : null;
                $description = trim((string) ($_POST['description'] ?? ''));
                $status = admin_normalize_status($_POST['status'] ?? 'active', ['active', 'inactive'], 'active');

                if ($name === '' || strlen($name) > 160) {
                    $errorMessage = 'Nama tiket/paket wajib diisi maksimal 160 karakter.';
                } elseif ($price === false || $price < 0) {
                    $errorMessage = 'Harga tiket/paket tidak valid.';
                } else {
                    $stmt = mysqli_prepare(
                        $connection,
                        'INSERT INTO tickets (destination_id, name, type, price, quota, description, status) VALUES (?, ?, ?, ?, ?, ?, ?)'
                    );

                    if ($stmt) {
                        $typeValue = $type !== '' ? $type : null;
                        $descriptionValue = $description !== '' ? $description : null;
                        mysqli_stmt_bind_param($stmt, 'issdiss', $destinationId, $name, $typeValue, $price, $quotaValue, $descriptionValue, $status);

                        if (mysqli_stmt_execute($stmt)) {
                            admin_log_activity($connection, 'ticket.created', 'tickets', mysqli_insert_id($connection), $name);
                            admin_redirect('index.php?notice=created');
                        }

                        error_log('Admin ticket create failed: ' . mysqli_stmt_error($stmt));
                        mysqli_stmt_close($stmt);
                    }

                    $errorMessage = 'Tiket/paket belum bisa dibuat.';
                }
            } elseif ($action === 'status') {
                $ticketId = $_POST['ticket_id'] ?? '';
                $status = admin_normalize_status($_POST['status'] ?? 'active', ['active', 'inactive'], 'active');

                if (!is_valid_positive_id($ticketId)) {
                    $errorMessage = 'Tiket/paket tidak valid.';
                } else {
                    $stmt = mysqli_prepare($connection, 'UPDATE tickets SET status = ? WHERE id = ? LIMIT 1');

                    if ($stmt) {
                        $id = (int) $ticketId;
                        mysqli_stmt_bind_param($stmt, 'si', $status, $id);

                        if (mysqli_stmt_execute($stmt)) {
                            admin_log_activity($connection, 'ticket.status_updated', 'tickets', $id, $status);
                            admin_redirect('index.php?notice=status-updated');
                        }

                        error_log('Admin ticket status update failed: ' . mysqli_stmt_error($stmt));
                        mysqli_stmt_close($stmt);
                    }

                    $errorMessage = 'Status tiket/paket belum bisa diperbarui.';
                }
            }
        }

        $tickets = admin_fetch_all(mysqli_query(
            $connection,
            'SELECT t.id, t.destination_id, t.name, t.type, t.price, t.quota, t.description, t.status,
                    d.nama_des AS destination_name
             FROM tickets t
             LEFT JOIN destination d ON d.id_des = t.destination_id
             ORDER BY t.id DESC
             LIMIT 100'
        ));
    }

    if (admin_table_exists($connection, 'destination')) {
        $destinations = admin_fetch_all(mysqli_query($connection, 'SELECT id_des, nama_des FROM destination ORDER BY nama_des ASC LIMIT 200'));
    }

    foreach (['routes_bus', 'bookings_pesawat', 'bookings_hotel', 'bookings_mobil'] as $table) {
        $legacySources[$table] = admin_count_table($connection, $table, ['routes_bus', 'bookings_pesawat', 'bookings_hotel', 'bookings_mobil']);
    }
}

$catalog = booking_service_catalog();
$page_title = 'Tiket dan Paket';
$page_desc = 'Kelola tiket, paket, dan sumber layanan booking.';
$page_css = 'styles/admin.css';
$base_href = '../../';
$admin_active = 'tickets';
include __DIR__ . '/../../partials/head.php';
?>

<main class="admin-shell">
  <?php include __DIR__ . '/../partials/nav.php'; ?>

  <section class="admin-main" aria-labelledby="admin-tickets-title">
    <?php
    $admin_eyebrow = 'Tickets & Packages';
    $admin_title = 'Manajemen Tiket / Paket';
    $admin_title_id = 'admin-tickets-title';
    $admin_subtitle = 'Pantau katalog layanan booking internal dan legacy search data.';
    include __DIR__ . '/../partials/topbar.php';
    ?>

    <?php if (!$ticketTableReady): ?>
      <div class="admin-alert admin-alert--warning" role="status">
        Tabel <code>tickets</code> khusus belum tersedia. Saat ini paket internal berasal dari katalog statis di <code>booking/_helpers.php</code>.
      </div>
    <?php endif; ?>

    <?php $flashMessage = admin_flash_message($notice); ?>
    <?php if ($flashMessage): ?>
      <div class="admin-alert admin-alert--success" role="status"><?= e($flashMessage) ?></div>
    <?php endif; ?>

    <?php if ($errorMessage): ?>
      <div class="admin-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <section class="admin-kpi-row">
        <?php foreach ($legacySources as $table => $count): ?>
          <article class="admin-kpi-card">
            <span><?= e($table) ?></span>
            <strong><?= $count === null ? '-' : e(admin_format_number($count)) ?></strong>
          </article>
        <?php endforeach; ?>
      </section>

      <?php if ($ticketTableReady): ?>
        <section class="admin-panel admin-panel--form">
          <div class="admin-panel__head">
            <div>
              <h2>Tambah Tiket / Paket</h2>
              <p>Kelola paket yang siap dipakai untuk katalog admin dan pengembangan booking berikutnya.</p>
            </div>
          </div>
          <form class="admin-form" action="admin/tickets/index.php" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="create">
            <div class="admin-form__grid">
              <div class="admin-field">
                <label for="ticket-name">Nama</label>
                <input id="ticket-name" name="name" type="text" maxlength="160" required>
              </div>
              <div class="admin-field">
                <label for="ticket-type">Tipe</label>
                <input id="ticket-type" name="type" type="text" maxlength="80" placeholder="tour, transport, hotel">
              </div>
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
                <label for="price">Harga</label>
                <input id="price" name="price" type="number" min="0" step="1000" value="0" required>
              </div>
              <div class="admin-field">
                <label for="quota">Kuota</label>
                <input id="quota" name="quota" type="number" min="0" step="1" placeholder="opsional">
              </div>
              <div class="admin-field">
                <label for="ticket-status">Status</label>
                <select id="ticket-status" name="status">
                  <option value="active">active</option>
                  <option value="inactive">inactive</option>
                </select>
              </div>
              <div class="admin-field admin-field--wide">
                <label for="ticket-description">Deskripsi</label>
                <textarea id="ticket-description" name="description" rows="3" maxlength="1200"></textarea>
              </div>
            </div>
            <div class="admin-form__actions">
              <button class="btn btn--primary" type="submit">Simpan Paket</button>
            </div>
          </form>
        </section>

        <section class="admin-panel">
          <div class="admin-panel__head">
            <div>
              <h2>Daftar Tiket / Paket</h2>
              <p><?= e(admin_format_number(count($tickets))) ?> paket tersimpan.</p>
            </div>
          </div>

          <?php if (!$tickets): ?>
            <div class="admin-empty">Belum ada tiket/paket tersimpan.</div>
          <?php else: ?>
            <div class="admin-data-table admin-data-table--actions admin-data-table--payment">
              <table>
                <thead><tr><th>Paket</th><th>Destinasi</th><th>Harga</th><th>Kuota</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                  <?php foreach ($tickets as $ticket): ?>
                    <tr>
                      <td><strong><?= e($ticket['name']) ?></strong><small><?= e($ticket['type'] ?? '-') ?></small></td>
                      <td><?= e($ticket['destination_name'] ?? 'Tidak terkait') ?></td>
                      <td><?= e(admin_format_money($ticket['price'])) ?></td>
                      <td><?= $ticket['quota'] === null ? '-' : e(admin_format_number($ticket['quota'])) ?></td>
                      <td><span class="admin-badge <?= e(admin_badge_class($ticket['status'])) ?>"><?= e($ticket['status']) ?></span></td>
                      <td>
                        <form class="admin-inline-form" action="admin/tickets/index.php" method="post">
                          <?= csrf_field() ?>
                          <input type="hidden" name="action" value="status">
                          <input type="hidden" name="ticket_id" value="<?= (int) $ticket['id'] ?>">
                          <select name="status" aria-label="Status tiket">
                            <?php foreach (['active', 'inactive'] as $status): ?>
                              <option value="<?= e($status) ?>" <?= $ticket['status'] === $status ? 'selected' : '' ?>><?= e($status) ?></option>
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

      <section class="admin-panel">
        <div class="admin-panel__head">
          <div>
            <h2>Katalog Paket Internal</h2>
            <p>Digunakan oleh form internal booking.</p>
          </div>
        </div>
        <div class="admin-feature-grid">
          <?php foreach ($catalog as $key => $service): ?>
            <article class="admin-feature-card">
              <span><?= e($key) ?></span>
              <h2><?= e($service['label']) ?></h2>
              <p><?= e($service['service_name']) ?> · <?= e(admin_format_money($service['unit_price'])) ?> per <?= e($service['unit_label']) ?></p>
              <div class="admin-row-actions">
                <a href="booking/index.php">Preview Booking</a>
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </section>
</main>

<script src="assets/js/app.js" defer></script>
</body>
</html>
