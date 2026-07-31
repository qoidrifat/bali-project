<?php
$page_title = 'Home';
$page_desc  = 'Discover the beauty of Bali with premium destinations, transport, tickets, and booking support in one place.';
$page_css   = 'styles/page.home.css';
$active     = 'home';
include 'partials/head.php';
include 'partials/navbar.php';

// Featured destinations (try to fetch from DB; gracefully fallback)
$featured = [];
require_once __DIR__ . '/connection.php';
if (isset($connection) && $connection) {
  $res = mysqli_query($connection, "SELECT id_des, nama_des, gambar FROM destination LIMIT 3");
  if ($res) {
    while ($row = mysqli_fetch_assoc($res)) $featured[] = $row;
    mysqli_free_result($res);
  } else {
    error_log('Homepage featured destination query failed: ' . mysqli_error($connection));
  }
}
if (empty($featured)) {
  $featured = [
    ['id_des' => 1, 'nama_des' => 'Pantai Kuta',  'gambar' => 'kuta.png'],
    ['id_des' => 2, 'nama_des' => 'Pura Tanah Lot','gambar' => 'tanahlot.png'],
    ['id_des' => 6, 'nama_des' => 'Garuda Wisnu Kencana', 'gambar' => 'garuda.png'],
  ];
}
?>

<main class="home-page">
  <!-- ============= HERO ============= -->
  <section class="home-hero">
    <div class="home-hero__bg" aria-hidden="true"></div>

    <div class="home-shell home-hero__grid">
      <div class="home-hero__copy">
        <span class="home-kicker anim-fade-down">
          <span class="pulse-dot"></span> Premium Bali travel guide
        </span>

        <h1 class="home-hero__title anim-fade-up">
          Plan a refined escape across Bali.
        </h1>

        <p class="home-hero__lead anim-fade-up delay-2">
          Explore curated destinations, compare transport options, and move from inspiration to booking with one clean travel workspace.
        </p>

        <div class="home-hero__ctas anim-fade-up delay-3">
          <a href="destination.php" class="home-btn home-btn--primary">
            Explore destinations
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
          </a>
          <a href="tiket.php" class="home-btn home-btn--glass">Book tickets</a>
          <a href="transport.php" class="home-link">Find flights</a>
        </div>

        <dl class="home-hero__stats anim-fade-up delay-4" aria-label="Bali Paradise highlights">
          <div>
            <dt>6+</dt>
            <dd>Curated places</dd>
          </div>
          <div>
            <dt>4.9</dt>
            <dd>Trip rating</dd>
          </div>
          <div>
            <dt>24/7</dt>
            <dd>Plan access</dd>
          </div>
        </dl>
      </div>

      <aside class="home-planner anim-fade-right delay-2" aria-label="Quick planning links">
        <div class="home-planner__media">
          <?php $plannerImage = public_image_path('gwk.jpeg'); ?>
          <img src="<?= e($plannerImage) ?>" alt="Garuda Wisnu Kencana cultural park in Bali"<?= image_dimensions_attr($plannerImage) ?> decoding="async" fetchpriority="high" />
          <span>Featured route</span>
        </div>
        <div class="home-planner__body">
          <p class="home-planner__label">Start here</p>
          <h2>Bali essentials in one flow</h2>
          <div class="home-planner__actions">
            <a href="transport.php">
              <span>01</span>
              Flight search
            </a>
            <a href="tiket.php">
              <span>02</span>
              Tickets and stays
            </a>
            <a href="contact.php">
              <span>03</span>
              Trip support
            </a>
          </div>
        </div>
      </aside>
    </div>

    <div class="home-scroll-hint anim-fade delay-5">Scroll</div>
  </section>

  <!-- ============= FEATURED DESTINATIONS ============= -->
  <section class="home-section home-destinations">
    <div class="home-shell">
      <div class="home-section__head" data-reveal>
        <span class="home-kicker">Curated from database</span>
        <div>
          <h2>Iconic Bali places, selected for a first-class itinerary.</h2>
          <p>These destination cards still come from the existing database query, with a cleaner premium presentation.</p>
        </div>
      </div>

      <div class="home-destination-grid">
        <?php foreach ($featured as $i => $d): ?>
          <a class="home-destination-card" href="detail.php?id=<?= (int)$d['id_des'] ?>" data-reveal style="--i: <?= (int) $i ?>">
            <?php $destinationImage = public_image_path($d['gambar']); ?>
            <img src="<?= e($destinationImage) ?>" alt="<?= e($d['nama_des']) ?>"<?= image_dimensions_attr($destinationImage) ?> loading="lazy" decoding="async" />
            <span class="home-destination-card__number"><?= str_pad((string)($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <div class="home-destination-card__content">
              <span>Top destination</span>
              <h3><?= e($d['nama_des']) ?></h3>
              <p>View guide</p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>

      <div class="home-section__footer" data-reveal>
        <a href="destination.php" class="home-btn home-btn--outline">See all destinations</a>
      </div>
    </div>
  </section>

  <!-- ============= TRIP FLOW ============= -->
  <section class="home-section home-flow">
    <div class="home-shell home-flow__grid">
      <div class="home-flow__copy" data-reveal>
        <span class="home-kicker">Travel workspace</span>
        <h2>Move from idea to itinerary without switching context.</h2>
        <p>Bali Paradise keeps the core travel steps close: destination research, visa guidance, transport search, and booking options.</p>
      </div>

      <div class="home-flow__cards">
        <a class="home-flow-card" href="visa.php" data-reveal style="--i:0">
          <span>01</span>
          <h3>Visa guidance</h3>
          <p>Review entry steps before you finalize dates.</p>
        </a>
        <a class="home-flow-card" href="transport.php" data-reveal style="--i:1">
          <span>02</span>
          <h3>Transport search</h3>
          <p>Find flight routes and continue to available options.</p>
        </a>
        <a class="home-flow-card" href="tiket.php" data-reveal style="--i:2">
          <span>03</span>
          <h3>Tickets and stays</h3>
          <p>Continue to bus, hotel, and car rental flows.</p>
        </a>
      </div>
    </div>
  </section>

  <!-- ============= CTA BLOCK ============= -->
  <section class="home-section home-final">
    <div class="home-shell">
      <div class="home-final__card" data-reveal="scale">
        <span class="home-kicker">Ready when you are</span>
        <h2>Build your Bali plan with fewer loose ends.</h2>
        <p>Choose a destination, compare routes, and continue to booking support from one polished travel surface.</p>
        <div class="home-final__actions">
          <a href="destination.php" class="home-btn home-btn--primary">Start exploring</a>
          <a href="contact.php" class="home-btn home-btn--glass">Talk to us</a>
        </div>
      </div>
    </div>
  </section>
</main>

<?php include 'partials/footer.php'; ?>
