<?php
require_once __DIR__ . '/../../includes/brand.php';

$admin_active = $admin_active ?? 'dashboard';

$admin_groups = [
    [
        'label' => 'Overview',
        'items' => [
            ['key' => 'dashboard', 'label' => 'Dashboard', 'href' => 'admin/index.php', 'icon' => 'dashboard'],
            ['key' => 'reports', 'label' => 'Laporan', 'href' => 'admin/reports/index.php', 'icon' => 'reports'],
        ],
    ],
    [
        'label' => 'Akses',
        'items' => [
            ['key' => 'users', 'label' => 'Users', 'href' => 'admin/users/index.php', 'icon' => 'users'],
            ['key' => 'roles', 'label' => 'Roles', 'href' => 'admin/roles/index.php', 'icon' => 'roles'],
        ],
    ],
    [
        'label' => 'Konten & Wisata',
        'items' => [
            ['key' => 'destinations', 'label' => 'Destinasi', 'href' => 'admin/destinations/index.php', 'icon' => 'destinations'],
            ['key' => 'categories', 'label' => 'Kategori', 'href' => 'admin/categories/index.php', 'icon' => 'categories'],
            ['key' => 'gallery', 'label' => 'Galeri', 'href' => 'admin/gallery/index.php', 'icon' => 'gallery'],
            ['key' => 'articles', 'label' => 'Artikel', 'href' => 'admin/articles/index.php', 'icon' => 'articles'],
        ],
    ],
    [
        'label' => 'Booking & Finance',
        'items' => [
            ['key' => 'tickets', 'label' => 'Tiket/Paket', 'href' => 'admin/tickets/index.php', 'icon' => 'tickets'],
            ['key' => 'bookings', 'label' => 'Booking', 'href' => 'admin/bookings/index.php', 'icon' => 'bookings'],
            ['key' => 'invoices', 'label' => 'Invoice', 'href' => 'admin/invoices/index.php', 'icon' => 'invoices'],
            ['key' => 'payments', 'label' => 'Pembayaran', 'href' => 'admin/payments/index.php', 'icon' => 'payments'],
        ],
    ],
    [
        'label' => 'Komunikasi',
        'items' => [
            ['key' => 'messages', 'label' => 'Pesan', 'href' => 'admin/messages/index.php', 'icon' => 'messages'],
        ],
    ],
    [
        'label' => 'Sistem',
        'items' => [
            ['key' => 'settings', 'label' => 'Settings', 'href' => 'admin/settings/index.php', 'icon' => 'settings'],
            ['key' => 'activity', 'label' => 'Activity', 'href' => 'admin/activity/index.php', 'icon' => 'activity'],
        ],
    ],
];
?>
<aside class="admin-sidebar" aria-label="Admin navigation">
  <a class="admin-sidebar__brand" href="admin/index.php">
    <?= brand_mark('admin-sidebar__brand-logo') ?>
    <span class="admin-sidebar__brand-copy">
      <strong><?= brand_wordmark('var(--brand-500)') ?></strong>
      <small>Control Panel</small>
    </span>
  </a>

  <nav class="admin-sidebar__nav" aria-label="Admin menu">
    <?php foreach ($admin_groups as $group): ?>
      <section class="admin-sidebar__group" aria-label="<?= e($group['label']) ?>">
        <span class="admin-sidebar__label"><?= e($group['label']) ?></span>
        <div class="admin-sidebar__items">
          <?php foreach ($group['items'] as $link): ?>
            <a class="<?= $admin_active === $link['key'] ? 'is-active' : '' ?>" href="<?= e($link['href']) ?>">
              <span class="admin-sidebar__icon-wrap"><?= admin_icon($link['icon'], 'admin-sidebar__icon') ?></span>
              <span class="admin-sidebar__text"><?= e($link['label']) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </nav>

  <div class="admin-sidebar__meta">
    <span>Secure admin area</span>
    <small>Kelola data utama dengan akses terbatas.</small>
  </div>
</aside>
