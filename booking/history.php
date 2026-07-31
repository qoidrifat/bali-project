<?php
require_once __DIR__ . '/_helpers.php';

auth_start_session();

if (!auth_check()) {
    header('Location: ../login.php');
    exit;
}

$connection = db_connect();
$bookings = [];
$errorMessage = null;

if (!$connection) {
    http_response_code(503);
    $errorMessage = 'Riwayat invoice belum bisa dimuat. Silakan coba lagi nanti.';
} elseif (!booking_schema_ready($connection)) {
    http_response_code(503);
    $errorMessage = 'Tabel booking internal belum lengkap. Jalankan SQL booking manual terlebih dahulu.';
} else {
    $user = auth_user();
    $bookings = booking_find_for_user($connection, $user['id'] ?? 0);
}

$page_title = 'Riwayat Invoice';
$page_desc = 'Riwayat invoice internal Bali Paradise untuk akun login.';
$page_css = 'styles/booking.internal.css';
$active = 'invoice';
$base_href = '../';
include __DIR__ . '/../partials/head.php';
include __DIR__ . '/../partials/navbar.php';
?>

<main class="booking-page">
  <section class="booking-history-shell" aria-labelledby="invoice-history-title">
    <header class="booking-history-head">
      <div>
        <span class="booking-eyebrow">Account Invoice</span>
        <h1 id="invoice-history-title">Riwayat invoice Anda.</h1>
        <p>Booking yang dibuat saat akun login akan otomatis tersinkron ke daftar ini.</p>
      </div>
      <div class="booking-sync-status" aria-live="polite">
        <span class="booking-sync-status__dot" aria-hidden="true"></span>
        <span id="booking-sync-text">Sinkron otomatis aktif</span>
      </div>
    </header>

    <?php if ($errorMessage): ?>
      <div class="booking-alert" role="alert"><?= e($errorMessage) ?></div>
    <?php else: ?>
      <div
        class="invoice-history-list"
        id="invoice-history-list"
        data-history-feed="booking/history-feed.php"
      >
        <?php booking_render_history_list($bookings); ?>
      </div>
    <?php endif; ?>
  </section>
</main>

<script>
  (function () {
    var list = document.getElementById('invoice-history-list');
    var statusText = document.getElementById('booking-sync-text');
    if (!list) return;

    var feedUrl = list.getAttribute('data-history-feed');
    var isLoading = false;

    function setStatus(text) {
      if (statusText) statusText.textContent = text;
    }

    function refreshHistory() {
      if (isLoading || document.hidden) return;
      isLoading = true;
      setStatus('Menyinkronkan...');

      fetch(feedUrl, {
        credentials: 'same-origin',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function (response) {
          if (!response.ok) throw new Error('sync-failed');
          return response.text();
        })
        .then(function (html) {
          list.innerHTML = html;
          setStatus('Sinkron otomatis aktif');
        })
        .catch(function () {
          setStatus('Sinkron tertunda, akan dicoba lagi');
        })
        .finally(function () {
          isLoading = false;
        });
    }

    window.addEventListener('focus', refreshHistory);
    document.addEventListener('visibilitychange', refreshHistory);
    window.setInterval(refreshHistory, 10000);
  })();
</script>

<?php include __DIR__ . '/../partials/footer.php'; ?>
