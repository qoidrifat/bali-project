<?php
/** Universal footer */
require_once __DIR__ . '/../includes/brand.php';
?>
<footer class="footer" data-reveal="fade">
  <div class="footer__grid">
    <div class="footer__col">
      <div class="footer__brand">
        <?= brand_mark('nav__brand-mark') ?>
        <span><?= brand_wordmark('var(--brand-300)') ?></span>
      </div>
      <p class="footer__tagline">
        Your gateway to the Island of Gods. Discover stunning beaches, vibrant culture, and unforgettable journeys.
      </p>
      <div class="footer__social">
        <a href="#" aria-label="Instagram">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
        </a>
        <a href="#" aria-label="Facebook">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        </a>
        <a href="#" aria-label="Twitter">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"/></svg>
        </a>
        <a href="#" aria-label="YouTube">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
        </a>
      </div>
    </div>

    <div class="footer__col">
      <h4 class="footer__title">Explore</h4>
      <ul class="footer__list">
        <li><a href="destination.php">Destinations</a></li>
        <li><a href="visa.php">Visa Info</a></li>
        <li><a href="transport.php">Transport</a></li>
        <li><a href="tiket.php">Tickets</a></li>
      </ul>
    </div>

    <div class="footer__col">
      <h4 class="footer__title">Company</h4>
      <ul class="footer__list">
        <li><a href="about.php">About Us</a></li>
        <li><a href="contact.php">Contact</a></li>
        <li><a href="#">Careers</a></li>
        <li><a href="#">Press</a></li>
      </ul>
    </div>

    <div class="footer__col">
      <h4 class="footer__title">Newsletter</h4>
      <p style="color: var(--ink-300); font-size: var(--fs-sm); line-height: var(--lh-base);">
        Get tips, deals and inspirations for your next trip.
      </p>
      <form class="footer__newsletter" onsubmit="event.preventDefault(); this.reset(); window.showToast && window.showToast('Subscribed! Welcome aboard.');">
        <input type="email" placeholder="you@email.com" required aria-label="Email" />
        <button type="submit" aria-label="Subscribe">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" width="18" height="18"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
        </button>
      </form>
    </div>
  </div>

  <div class="footer__bottom">
    <span>&copy; <?= date('Y') ?> Bali Paradise. All rights reserved.</span>
    <span>Made with <span style="color: var(--sunset-400)">&#9829;</span> in Indonesia</span>
  </div>
</footer>

<script src="assets/js/app.js" defer></script>
</body>
</html>
