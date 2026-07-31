<?php
$page_title = 'Ticket Menu';
$page_desc  = 'Pilih tiket bus, hotel, atau sewa mobil untuk perjalanan Bali.';
$page_css   = 'styles/tiket.css';
$active     = 'tiket';
include 'partials/head.php';
include 'partials/navbar.php';

$ticket_options = [
  [
    'title' => 'Internal Booking',
    'desc' => 'Buat booking internal dengan status pembayaran manual dan invoice sederhana.',
    'href' => 'booking/index.php',
    'image' => 'images/optimized/tiket/cards/internal.jpg',
    'alt' => 'Internal booking travel stay',
    'label' => 'Direct booking',
    'badge' => 'Manual payment',
    'action' => 'Mulai booking',
    'featured' => true,
  ],
  [
    'title' => 'Bus Tickets',
    'desc' => 'Cari rute bus antar kota dengan pilihan operator dan jadwal tersedia.',
    'href' => 'tiket.bus.php',
    'image' => 'images/optimized/tiket/cards/bus.jpg',
    'alt' => 'Bus ticket service',
    'label' => 'Transport',
    'badge' => '',
    'action' => 'Cari tiket bus',
    'featured' => false,
  ],
  [
    'title' => 'Hotel Booking',
    'desc' => 'Temukan hotel berdasarkan tujuan, tanggal, dan jumlah tamu.',
    'href' => 'booking.hotel.php',
    'image' => 'images/optimized/tiket/cards/hotel.jpg',
    'alt' => 'Hotel booking service',
    'label' => 'Stay',
    'badge' => '',
    'action' => 'Cari hotel',
    'featured' => false,
  ],
  [
    'title' => 'Car Rental',
    'desc' => 'Pilih kendaraan untuk perjalanan yang lebih fleksibel selama di Bali.',
    'href' => 'sewa.mobil.php',
    'image' => 'images/optimized/tiket/cards/rental.jpg',
    'alt' => 'Car rental service',
    'label' => 'Private ride',
    'badge' => '',
    'action' => 'Sewa mobil',
    'featured' => false,
  ],
];
?>

<main class="ticket-page">
  <section class="ticket-hero" aria-labelledby="ticket-title">
    <div class="ticket-shell">
      <div class="ticket-heading">
        <span class="ticket-kicker">Travel services</span>
        <h1 id="ticket-title">Pilih kebutuhan perjalanan Anda.</h1>
        <p>Bandingkan opsi booking, tiket bus, hotel, dan rental mobil dalam satu halaman yang ringkas.</p>
      </div>

      <div class="ticket-summary" aria-label="Ticket service highlights">
        <span>4 layanan</span>
        <span>Light dan dark mode</span>
        <span>Booking cepat</span>
      </div>
    </div>
  </section>

  <section class="ticket-section" aria-label="Pilihan layanan tiket">
    <div class="ticket-shell">
      <div class="ticket-options">
        <?php foreach ($ticket_options as $index => $option): ?>
          <a
            class="ticket-card <?= $option['featured'] ? 'ticket-card--featured' : '' ?>"
            href="<?= htmlspecialchars($option['href'], ENT_QUOTES, 'UTF-8') ?>"
            style="--item-index: <?= (int) $index ?>; --card-image: url('<?= htmlspecialchars($option['image'], ENT_QUOTES, 'UTF-8') ?>')"
          >
            <span class="ticket-card__media" aria-hidden="true">
              <img
                src="<?= htmlspecialchars($option['image'], ENT_QUOTES, 'UTF-8') ?>"
                alt=""
                width="1200"
                height="1500"
                loading="eager"
                fetchpriority="high"
                decoding="async"
              />
            </span>

            <span class="ticket-card__content">
              <span class="ticket-card__topline">
                <span><?= htmlspecialchars($option['label'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php if ($option['badge'] !== ''): ?>
                  <span class="ticket-card__badge"><?= htmlspecialchars($option['badge'], ENT_QUOTES, 'UTF-8') ?></span>
                <?php endif; ?>
              </span>

              <span class="ticket-card__title"><?= htmlspecialchars($option['title'], ENT_QUOTES, 'UTF-8') ?></span>
              <span class="ticket-card__desc"><?= htmlspecialchars($option['desc'], ENT_QUOTES, 'UTF-8') ?></span>

              <span class="ticket-card__action">
                <?= htmlspecialchars($option['action'], ENT_QUOTES, 'UTF-8') ?>
                <span aria-hidden="true">&rarr;</span>
              </span>
            </span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>

<?php include 'partials/footer.php'; ?>
