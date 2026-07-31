<?php
$adminUser = $adminUser ?? (function_exists('auth_user') ? auth_user() : null);
$admin_eyebrow = $admin_eyebrow ?? 'Admin Area';
$admin_title = $admin_title ?? 'Dashboard Admin';
$admin_subtitle = $admin_subtitle ?? 'Kelola data Bali Paradise dengan aman.';
$admin_title_id = $admin_title_id ?? 'admin-page-title';
$admin_action_html = $admin_action_html ?? '';
$adminInitial = strtoupper(substr((string) ($adminUser['name'] ?? 'A'), 0, 1));
?>
<header class="admin-topbar">
  <div>
    <span class="admin-eyebrow"><?= e($admin_eyebrow) ?></span>
    <h1 id="<?= e($admin_title_id) ?>"><?= e($admin_title) ?></h1>
    <p><?= e($admin_subtitle) ?></p>
  </div>

  <div class="admin-topbar__actions">
    <?php if ($admin_action_html !== ''): ?>
      <div class="admin-topbar__custom-action">
        <?= $admin_action_html ?>
      </div>
    <?php endif; ?>

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

    <div class="nav__topbar-menu" data-topbar-menu>
      <button
        class="nav__topbar-toggle"
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

        <a class="nav__topbar-item" href="admin/invoices/index.php" role="menuitem">
          <span class="nav__topbar-item-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M4 4h16v16H4z"/><path d="M8 9h8"/><path d="M8 13h8"/><path d="M8 17h5"/>
            </svg>
          </span>
          <span>
            <strong>Kelola Invoice</strong>
            <small>Invoice semua akun</small>
          </span>
        </a>

        <a class="nav__topbar-item" href="booking/history.php" role="menuitem">
          <span class="nav__topbar-item-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M12 5v14"/><path d="M5 12h14"/>
            </svg>
          </span>
          <span>
            <strong>Invoice Saya</strong>
            <small>Riwayat akun admin</small>
          </span>
        </a>
      </div>
    </div>

    <div class="nav__topbar-menu" data-topbar-menu>
      <button
        class="nav__topbar-toggle"
        type="button"
        aria-label="Buka menu profile"
        aria-haspopup="true"
        aria-expanded="false"
        data-topbar-toggle
      >
        <span class="nav__profile-initial"><?= e($adminInitial) ?></span>
      </button>

      <div class="nav__topbar-dropdown nav__topbar-dropdown--profile" data-topbar-dropdown role="menu" aria-label="Menu profile">
        <div class="nav__topbar-head">
          <span class="nav__topbar-avatar"><?= e($adminInitial) ?></span>
          <div>
            <strong><?= e($adminUser['name'] ?? 'Admin') ?></strong>
            <small><?= e($adminUser['email'] ?? '') ?></small>
            <em><?= e(ucfirst((string) ($adminUser['role'] ?? 'admin'))) ?></em>
          </div>
        </div>

        <a class="nav__topbar-item" href="profile.php" role="menuitem">
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

        <a class="nav__topbar-item is-active" href="admin/index.php" role="menuitem">
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
  </div>
</header>
