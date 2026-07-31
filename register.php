<?php
require_once __DIR__ . '/includes/auth.php';

auth_start_session();

if (auth_check()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$old = [
    'name' => '',
    'email' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['name'] = trim($_POST['name'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirmation = $_POST['password_confirmation'] ?? '';

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
    }

    if ($old['name'] === '' || strlen($old['name']) < 2 || strlen($old['name']) > 120) {
        $errors[] = 'Nama wajib diisi antara 2 sampai 120 karakter.';
    }

    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || strlen($old['email']) > 190) {
        $errors[] = 'Email tidak valid.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password minimal 8 karakter.';
    }

    if ($password !== $passwordConfirmation) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    if (!$errors) {
        $connection = db_connect();

        if (!$connection) {
            $errors[] = 'Sistem akun belum tersedia. Silakan coba lagi nanti.';
        } elseif (!auth_schema_ready($connection)) {
            $errors[] = 'Sistem akun belum siap. Jalankan migration auth manual terlebih dahulu.';
        } elseif (auth_find_user_by_email($connection, $old['email'])) {
            $errors[] = 'Email sudah terdaftar. Silakan login.';
        } elseif (auth_register_user($connection, $old['name'], $old['email'], $password)) {
            $user = auth_find_user_by_email($connection, $old['email']);

            if ($user) {
                auth_login($user);
                header('Location: index.php');
                exit;
            }

            $errors[] = 'Akun berhasil dibuat, tetapi sesi login belum bisa dibuat. Silakan login manual.';
        } else {
            $errors[] = 'Akun belum bisa dibuat. Pastikan migration auth sudah dijalankan dan coba lagi.';
        }
    }
}

$page_title = 'Register';
$page_desc = 'Buat akun Bali Paradise untuk menyimpan akses perjalanan Anda.';
$page_css = 'styles/auth.css';
$active = '';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';
?>

<main class="auth-page">
  <section class="auth-shell" aria-labelledby="register-title">
    <div class="auth-panel auth-panel--intro">
      <span class="auth-eyebrow">Bali Paradise Account</span>
      <h1 id="register-title">Buat akun perjalanan Anda.</h1>
      <p>
        Simpan akses untuk booking, tiket, transportasi, dan rencana liburan Bali
        dalam satu akun yang aman.
      </p>
      <div class="auth-highlights" aria-label="Keunggulan akun">
        <span>Secure session</span>
        <span>Password terenkripsi</span>
        <span>CSRF protected</span>
      </div>
    </div>

    <div class="auth-card">
      <div class="auth-card__head">
        <h2>Register</h2>
        <p>Gunakan email aktif dan password minimal 8 karakter.</p>
      </div>

      <?php if ($errors): ?>
        <div class="auth-alert auth-alert--error" role="alert" aria-live="polite">
          <strong>Periksa kembali data Anda.</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= e($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <form class="auth-form" action="register.php" method="post" novalidate>
        <?= csrf_field() ?>

        <div class="input-group">
          <label for="name">Nama lengkap</label>
          <input
            id="name"
            name="name"
            type="text"
            value="<?= e($old['name']) ?>"
            autocomplete="name"
            minlength="2"
            maxlength="120"
            required
          />
        </div>

        <div class="input-group">
          <label for="email">Email</label>
          <input
            id="email"
            name="email"
            type="email"
            value="<?= e($old['email']) ?>"
            autocomplete="email"
            maxlength="190"
            required
          />
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input
            id="password"
            name="password"
            type="password"
            autocomplete="new-password"
            minlength="8"
            required
          />
        </div>

        <div class="input-group">
          <label for="password_confirmation">Konfirmasi password</label>
          <input
            id="password_confirmation"
            name="password_confirmation"
            type="password"
            autocomplete="new-password"
            minlength="8"
            required
          />
        </div>

        <button class="btn btn--primary btn--block" type="submit">Buat Akun</button>
      </form>

      <p class="auth-switch">
        Sudah punya akun?
        <a href="login.php">Login</a>
      </p>
    </div>
  </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
