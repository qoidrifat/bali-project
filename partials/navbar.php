<?php
/**
 * Universal navbar
 * Usage:
 *   $active = 'home'; // home | destination | about | contact | visa | transport | tiket
 *   include 'partials/navbar.php';
 */

require_once __DIR__ . '/../includes/brand.php';

$active = $active ?? '';

$nav_items = [
  ['key' => 'home',        'label' => 'Home',        'href' => 'index.php'],
  ['key' => 'destination', 'label' => 'Destination', 'href' => 'destination.php'],
  ['key' => 'about',       'label' => 'About',       'href' => 'about.php'],
  ['key' => 'visa',        'label' => 'Visa',        'href' => 'visa.php'],
  ['key' => 'transport',   'label' => 'Transport',   'href' => 'transport.php'],
  ['key' => 'tiket',       'label' => 'Tiket',       'href' => 'tiket.php'],
  ['key' => 'contact',     'label' => 'Contact',     'href' => 'contact.php'],
];

$nav_user = function_exists('auth_check') && auth_check() ? auth_user() : null;
$nav_initial = $nav_user ? strtoupper(substr((string) ($nav_user['name'] ?? 'U'), 0, 1)) : '';
$nav_is_invoice_active = $active === 'invoice';
$nav_is_profile_active = $active === 'profile';
?>
<header class="nav" id="site-nav">
  <div class="nav__inner">
    <a class="nav__brand" href="index.php" aria-label="Bali Paradise home">
      <?= brand_mark('nav__brand-mark') ?>
      <span><?= brand_wordmark('var(--brand-500)') ?></span>
    </a>

    <nav class="nav__menu" id="nav-menu" aria-label="Main">
      <?php foreach ($nav_items as $item): ?>
        <a
          class="nav__link <?= $active === $item['key'] ? 'is-active' : '' ?>"
          href="<?= htmlspecialchars($item['href']) ?>"
        >
          <span><?= htmlspecialchars($item['label']) ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="nav__actions">
      <button
        class="nav__theme-toggle"
        type="button"
        id="theme-toggle"
        aria-label="Toggle theme"
        title="Toggle theme"
      >
        <svg class="icon-sun" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <circle cx="12" cy="12" r="4"/>
          <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
        </svg>
        <svg class="icon-moon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
        </svg>
      </button>

      <?php if ($nav_user): ?>
        <div class="nav__topbar-menu" data-topbar-menu>
          <button
            class="nav__topbar-toggle <?= $nav_is_invoice_active ? 'is-active' : '' ?>"
            type="button"
            aria-label="Buka menu invoice"
            aria-haspopup="true"
            aria-expanded="false"
            data-topbar-toggle
            data-invoice-nav="true"
            data-invoice-count-url="booking/history-count.php"
          >
            <span class="nav__topbar-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/>
              </svg>
            </span>
            <span class="nav__invoice-badge" data-invoice-count aria-label="Jumlah invoice">0</span>
          </button>

          <div class="nav__topbar-dropdown nav__topbar-dropdown--invoice" data-topbar-dropdown role="menu" aria-label="Menu invoice">
            <div class="nav__topbar-head">
              <span class="nav__topbar-avatar" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/>
                </svg>
              </span>
              <div>
                <strong>Invoice</strong>
                <small>Booking dan invoice akun</small>
              </div>
            </div>

            <a class="nav__topbar-item <?= $nav_is_invoice_active ? 'is-active' : '' ?>" href="booking/history.php" role="menuitem">
              <span class="nav__topbar-item-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M4 4h16v16H4z"/><path d="M8 9h8"/><path d="M8 13h8"/><path d="M8 17h5"/>
                </svg>
              </span>
              <span>
                <strong>Riwayat Invoice</strong>
                <small>Riwayat booking akun</small>
              </span>
            </a>

            <a class="nav__topbar-item" href="booking/index.php" role="menuitem">
              <span class="nav__topbar-item-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M12 5v14"/><path d="M5 12h14"/>
                </svg>
              </span>
              <span>
                <strong>Buat Booking</strong>
                <small>Mulai invoice baru</small>
              </span>
            </a>
          </div>
        </div>

        <div class="nav__topbar-menu" data-topbar-menu>
          <button
            class="nav__topbar-toggle <?= $nav_is_profile_active ? 'is-active' : '' ?>"
            type="button"
            aria-label="Buka menu profile"
            aria-haspopup="true"
            aria-expanded="false"
            data-topbar-toggle
          >
            <span class="nav__profile-initial"><?= htmlspecialchars($nav_initial) ?></span>
          </button>

          <div class="nav__topbar-dropdown nav__topbar-dropdown--profile" data-topbar-dropdown role="menu" aria-label="Menu profile">
            <div class="nav__topbar-head">
              <span class="nav__topbar-avatar"><?= htmlspecialchars($nav_initial) ?></span>
              <div>
                <strong><?= htmlspecialchars($nav_user['name'] ?? 'User') ?></strong>
                <small><?= htmlspecialchars($nav_user['email'] ?? '') ?></small>
                <em><?= htmlspecialchars(ucfirst((string) ($nav_user['role'] ?? 'user'))) ?></em>
              </div>
            </div>

            <a class="nav__topbar-item <?= $nav_is_profile_active ? 'is-active' : '' ?>" href="profile.php" role="menuitem">
              <span class="nav__topbar-item-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M20 21a8 8 0 0 0-16 0"/><circle cx="12" cy="7" r="4"/>
                </svg>
              </span>
              <span>
                <strong>Profile</strong>
                <small>Pengaturan akun</small>
              </span>
            </a>

            <?php if (($nav_user['role'] ?? '') === 'admin'): ?>
              <a class="nav__topbar-item" href="admin/index.php" role="menuitem">
                <span class="nav__topbar-item-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>
                  </svg>
                </span>
                <span>
                  <strong>Admin</strong>
                  <small>Dashboard pengelola</small>
                </span>
              </a>
            <?php endif; ?>

            <form class="nav__topbar-signout" action="logout.php" method="post">
              <?= csrf_field() ?>
              <button class="nav__topbar-item nav__topbar-item--danger" type="submit" role="menuitem">
                <span class="nav__topbar-item-icon" aria-hidden="true">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/>
                  </svg>
                </span>
                <span>
                  <strong>Sign Out</strong>
                  <small>Keluar dari akun</small>
                </span>
              </button>
            </form>
          </div>
        </div>
      <?php endif; ?>

      <button
        class="nav__hamburger"
        type="button"
        id="nav-hamburger"
        aria-label="Toggle menu"
        aria-expanded="false"
        aria-controls="nav-menu"
      >
        <span></span>
        <span></span>
        <span></span>
      </button>
    </div>
  </div>
</header>
