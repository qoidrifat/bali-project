<?php
require_once __DIR__ . '/includes/auth.php';

auth_start_session();

if (auth_check()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$old = [
    'email' => '',
];
$registered = isset($_GET['registered']) && $_GET['registered'] === '1';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
    }

    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid.';
    }

    if ($password === '') {
        $errors[] = 'Password wajib diisi.';
    }

    if (!$errors) {
        $connection = db_connect();

        if (!$connection) {
            $errors[] = 'Sistem akun belum tersedia. Silakan coba lagi nanti.';
        } elseif (!auth_schema_ready($connection)) {
            $errors[] = 'Sistem akun belum siap. Jalankan migration auth manual terlebih dahulu.';
        } else {
            $user = auth_find_user_by_email($connection, $old['email']);

            if ($user && password_verify($password, $user['password_hash'])) {
                auth_login($user);
                header('Location: index.php');
                exit;
            }

            $errors[] = 'Email atau password tidak sesuai.';
        }
    }
}

$page_title = 'Login';
$page_desc = 'Masuk ke akun Bali Paradise.';
$page_css = 'styles/auth.css';
$active = '';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';
?>

<main class="auth-page">
  <section class="auth-shell" aria-labelledby="login-title">
    <div class="auth-panel auth-panel--intro">
      <span class="auth-eyebrow">Welcome Back</span>
      <h1 id="login-title">Masuk untuk melanjutkan perjalanan.</h1>
      <p>
        Akses akun Anda untuk mengelola rencana wisata, tiket, transportasi, dan
        booking dengan sesi yang lebih aman.
      </p>
      <div class="auth-highlights" aria-label="Keamanan login">
        <span>password_verify</span>
        <span>Session regenerate</span>
        <span>CSRF protected</span>
      </div>
    </div>

    <div class="auth-card">
      <div class="auth-card__head">
        <h2>Login</h2>
        <p>Masukkan email dan password akun Anda.</p>
      </div>

      <?php if ($registered): ?>
        <div class="auth-alert auth-alert--success" role="status" aria-live="polite">
          Akun berhasil dibuat. Silakan login.
        </div>
      <?php endif; ?>

      <?php if ($errors): ?>
        <div class="auth-alert auth-alert--error" role="alert" aria-live="polite">
          <strong>Login belum berhasil.</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= e($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="auth-form" action="login.php" method="post" novalidate>
        <?= csrf_field() ?>

        <div class="input-group">
          <label for="email">Email</label>
          <input
            id="email"
            name="email"
            type="email"
            value="<?= e($old['email']) ?>"
            autocomplete="email"
            required
          />
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            autocomplete="current-password"
            required
          />
        </div>

        <button class="btn btn--primary btn--block" type="submit">Login</button>
      </form>

      <p class="auth-switch">
        Belum punya akun?
        <a href="register.php">Register</a>
      </p>
    </div>
  </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
