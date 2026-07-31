<?php
require_once __DIR__ . '/_helpers.php';

auth_start_session();

$catalog = booking_service_catalog();
$cities = booking_city_options();
$values = booking_default_values();
$errors = $_SESSION['booking_errors'] ?? [];

if (!empty($_SESSION['booking_old']) && is_array($_SESSION['booking_old'])) {
    $values = array_merge($values, $_SESSION['booking_old']);
}

unset($_SESSION['booking_errors'], $_SESSION['booking_old']);

$page_title = 'Booking Internal';
$page_desc = 'Booking tiket internal Bali Paradise dengan status pembayaran manual.';
$page_css = 'styles/booking.internal.css';
$active = 'tiket';
$base_href = '../';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/navbar.php';
?>

<main class="booking-page">
  <section class="booking-hero" aria-labelledby="booking-title">
    <div>
      <span class="booking-eyebrow">Internal Booking</span>
      <h1 id="booking-title">Pesan tiket dan layanan perjalanan dari satu form.</h1>
      <p>
        Pilih layanan, tanggal, jumlah, dan data pemesan. Status booking dibuat
        pending dengan pembayaran manual terlebih dahulu.
      </p>
      <?php if (!auth_check()): ?>
        <p class="booking-login-note">
          Anda bisa booking sebagai guest. Untuk menyimpan riwayat ke akun, silakan <a href="login.php">login</a>.
        </p>
      <?php else: ?>
        <p class="booking-login-note">
          Booking yang dibuat dari akun ini akan masuk ke <a href="booking/history.php">riwayat invoice</a> secara otomatis.
        </p>
      <?php endif; ?>
    </div>
  </section>

  <section class="booking-shell">
    <form class="booking-form" action="booking/store.php" method="post" novalidate>
      <?= csrf_field() ?>

      <?php if ($errors): ?>
        <div class="booking-alert" role="alert">
          <strong>Booking belum bisa dibuat.</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= e($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="booking-section-title">
        <h2>Detail Layanan</h2>
        <p>Pilih tipe tiket dan jadwal perjalanan.</p>
      </div>

      <div class="booking-grid">
        <div class="input-group">
          <label for="service_type">Jenis tiket</label>
          <select id="service_type" name="service_type" required>
            <?php foreach ($catalog as $key => $service): ?>
              <option value="<?= e($key) ?>" <?= $values['service_type'] === $key ? 'selected' : '' ?>>
                <?= e($service['label']) ?> - <?= e(booking_format_money($service['unit_price'])) ?>/<?= e($service['unit_label']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group">
          <label for="quantity">Jumlah</label>
          <input id="quantity" name="quantity" type="number" min="1" max="10" value="<?= e($values['quantity']) ?>" required />
        </div>
      </div>

      <div class="booking-grid">
        <div class="input-group">
          <label for="origin_label">Kota asal</label>
          <select id="origin_label" name="origin_label">
            <?php foreach ($cities as $value => $label): ?>
              <option value="<?= e($value) ?>" <?= $values['origin_label'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="input-group">
          <label for="destination_label">Kota tujuan</label>
          <select id="destination_label" name="destination_label" required>
            <?php foreach ($cities as $value => $label): ?>
              <option value="<?= e($value) ?>" <?= $values['destination_label'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="booking-grid">
        <div class="input-group">
          <label for="start_date">Tanggal mulai</label>
          <input id="start_date" name="start_date" type="date" value="<?= e($values['start_date']) ?>" required />
        </div>

        <div class="input-group">
          <label for="end_date">Tanggal selesai / pulang</label>
          <input id="end_date" name="end_date" type="date" value="<?= e($values['end_date']) ?>" />
        </div>
      </div>

      <div class="booking-section-title">
        <h2>Data Pemesan</h2>
        <p>Invoice akan memakai data berikut.</p>
      </div>

      <div class="booking-grid">
        <div class="input-group">
          <label for="customer_name">Nama pemesan</label>
          <input id="customer_name" name="customer_name" type="text" value="<?= e($values['customer_name']) ?>" maxlength="120" required />
        </div>

        <div class="input-group">
          <label for="customer_email">Email</label>
          <input id="customer_email" name="customer_email" type="email" value="<?= e($values['customer_email']) ?>" maxlength="190" required />
        </div>
      </div>

      <div class="booking-grid booking-grid--wide">
        <div class="input-group">
          <label for="customer_phone">Nomor telepon</label>
          <input id="customer_phone" name="customer_phone" type="tel" value="<?= e($values['customer_phone']) ?>" maxlength="30" required />
        </div>

        <div class="input-group">
          <label for="notes">Catatan</label>
          <textarea id="notes" name="notes" rows="4" maxlength="1000"><?= e($values['notes']) ?></textarea>
        </div>
      </div>

      <div class="booking-summary" aria-live="polite">
        <span>Status awal</span>
        <strong>Pending - Pembayaran Manual</strong>
      </div>

      <button class="btn btn--primary btn--block" type="submit">Buat Booking</button>
    </form>
  </section>
</main>

<script>
  (function () {
    var service = document.getElementById('service_type');
    var origin = document.getElementById('origin_label');
    if (!service || !origin) return;

    function syncOrigin() {
      var needsOrigin = service.value === 'bus' || service.value === 'flight';
      origin.disabled = !needsOrigin;
      origin.closest('.input-group').style.opacity = needsOrigin ? '1' : '0.55';
    }

    service.addEventListener('change', syncOrigin);
    syncOrigin();
  })();
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
