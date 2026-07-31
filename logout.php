<?php
require_once __DIR__ . '/includes/auth.php';

auth_start_session();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        auth_logout();
    }

    header('Location: index.php');
    exit;
}

$user = auth_user();
$page_title = 'Logout';
$page_desc = 'Keluar dari akun Bali Paradise.';
$page_css = 'styles/auth.css';
$active = '';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';
?>

<main class="auth-page auth-page--compact">
  <section class="auth-card auth-card--center" aria-labelledby="logout-title">
    <div class="auth-card__head">
      <span class="auth-eyebrow">Secure Logout</span>
      <h1 id="logout-title">Keluar dari akun?</h1>
      <p>
        <?php if ($user): ?>
          Anda sedang login sebagai <strong><?= e($user['name']) ?></strong>.
        <?php else: ?>
          Tidak ada sesi login aktif saat ini.
        <?php endif; ?>
      </p>
    </div>

    <?php if ($user): ?>
      <form class="auth-form" action="logout.php" method="post">
        <?= csrf_field() ?>
        <button class="btn btn--primary btn--block" type="submit">Logout</button>
      </form>
    <?php endif; ?>

    <p class="auth-switch">
      <a href="index.php">Kembali ke homepage</a>
    </p>
  </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
