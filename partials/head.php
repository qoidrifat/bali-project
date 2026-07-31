<?php
/**
 * Universal <head> partial
 * Usage:
 *   $page_title = 'Page Title';
 *   $page_desc  = 'Page description';
 *   $page_css   = 'styles/page.home.css'; // optional
 *   include 'partials/head.php';
 */

require_once __DIR__ . '/../includes/auth.php';

auth_start_session();

$page_title = $page_title ?? 'Bali Tourism';
$page_desc  = $page_desc  ?? 'Discover the beauty of Bali - destinations, visa, transport and bookings.';
$page_css   = $page_css   ?? null;
$base_href  = $base_href  ?? null;
$extra_head = $extra_head ?? '';

if (!function_exists('asset_href')) {
  function asset_href($path) {
    $path = (string) $path;

    if ($path === '' || preg_match('#^(https?:)?//#', $path) || strpos($path, 'data:') === 0) {
      return $path;
    }

    $assetPath = strtok($path, '?');
    $file = __DIR__ . '/../' . ltrim($assetPath, '/');

    if (!is_file($file)) {
      return $path;
    }

    $separator = strpos($path, '?') === false ? '?' : '&';

    return $path . $separator . 'v=' . filemtime($file);
  }
}

if (!headers_sent()) {
  header('X-Content-Type-Options: nosniff');
  header('X-Frame-Options: SAMEORIGIN');
  header('Referrer-Policy: strict-origin-when-cross-origin');
  header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
}
?>
<!DOCTYPE html>
<html lang="id" data-theme="light">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?= htmlspecialchars($page_desc) ?>" />
  <title><?= htmlspecialchars($page_title) ?> - Bali Paradise</title>

  <?php if ($base_href): ?>
  <base href="<?= htmlspecialchars($base_href) ?>" />
  <?php endif; ?>

  <!-- Theme color -->
  <meta name="theme-color" content="#0D9488" />

  <!-- Inline theme bootstrap (avoid flash) -->
  <script>
    (function() {
      try {
        var t = localStorage.getItem('bali-theme');
        if (!t) {
          t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
        }
        document.documentElement.setAttribute('data-theme', t);
      } catch (e) {}
    })();
  </script>

  <!-- Preconnect & fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
  />

  <!-- Core stylesheets -->
  <link rel="stylesheet" href="<?= htmlspecialchars(asset_href('styles/_tokens.css')) ?>" />
  <link rel="stylesheet" href="<?= htmlspecialchars(asset_href('styles/_base.css')) ?>" />
  <link rel="stylesheet" href="<?= htmlspecialchars(asset_href('styles/_animations.css')) ?>" />
  <link rel="stylesheet" href="<?= htmlspecialchars(asset_href('styles/_components.css')) ?>" />

  <?php if ($page_css): ?>
  <link rel="stylesheet" href="<?= htmlspecialchars(asset_href($page_css)) ?>" />
  <?php endif; ?>
  <?= $extra_head ?>
  <link rel="stylesheet" href="<?= htmlspecialchars(asset_href('styles/_theme-sync.css')) ?>" />

  <link rel="icon" type="image/svg+xml" href="<?= htmlspecialchars(asset_href('images/logo.svg')) ?>" />
  <link rel="alternate icon" type="image/png" href="<?= htmlspecialchars(asset_href('images/logo.png')) ?>" />
</head>
<body>
