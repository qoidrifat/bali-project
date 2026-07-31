<?php
require_once __DIR__ . '/includes/auth.php';

auth_start_session();
auth_require_login('login.php');

$connection = db_connect();
$errors = [];
$success = null;
$schemaReady = $connection && auth_profile_schema_ready($connection);
$sessionUser = auth_user();
$user = null;

if (!$connection) {
    $errors[] = 'Sistem profil belum tersedia. Silakan coba lagi nanti.';
} elseif (!$schemaReady) {
    $errors[] = 'Tabel profil belum lengkap. Jalankan SQL profil manual terlebih dahulu.';
} else {
    $user = auth_find_user_by_id($connection, $sessionUser['id'] ?? 0);

    if (!$user) {
        $errors[] = 'Profil akun tidak ditemukan. Silakan login ulang.';
    }
}

$profileValues = [
    'name' => $user['name'] ?? ($sessionUser['name'] ?? ''),
    'email' => $user['email'] ?? ($sessionUser['email'] ?? ''),
    'phone' => $user['phone'] ?? '',
    'city' => $user['city'] ?? '',
    'country' => $user['country'] ?? '',
    'birth_date' => $user['birth_date'] ?? '',
    'preferred_contact' => $user['preferred_contact'] ?? 'email',
    'travel_style' => $user['travel_style'] ?? '',
    'bio' => $user['bio'] ?? '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $connection && $schemaReady && $user) {
    $action = $_POST['profile_action'] ?? '';

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
    } elseif ($action === 'profile') {
        $profileValues = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'city' => trim($_POST['city'] ?? ''),
            'country' => trim($_POST['country'] ?? ''),
            'birth_date' => trim($_POST['birth_date'] ?? ''),
            'preferred_contact' => trim($_POST['preferred_contact'] ?? ''),
            'travel_style' => trim($_POST['travel_style'] ?? ''),
            'bio' => trim($_POST['bio'] ?? ''),
        ];

        $allowedContacts = ['email', 'phone', 'whatsapp'];
        $allowedStyles = ['', 'relax', 'adventure', 'culture', 'family', 'business'];

        if ($profileValues['name'] === '' || strlen($profileValues['name']) < 2 || strlen($profileValues['name']) > 120) {
            $errors[] = 'Nama wajib diisi antara 2 sampai 120 karakter.';
        }

        if (!filter_var($profileValues['email'], FILTER_VALIDATE_EMAIL) || strlen($profileValues['email']) > 190) {
            $errors[] = 'Email tidak valid.';
        } else {
            $existing = auth_find_user_by_email($connection, $profileValues['email']);

            if ($existing && (int) $existing['id'] !== (int) $user['id']) {
                $errors[] = 'Email sudah digunakan akun lain.';
            }
        }

        if ($profileValues['phone'] !== '' && !preg_match('/^[0-9+()\-\s]{8,30}$/', $profileValues['phone'])) {
            $errors[] = 'Nomor telepon wajib 8-30 karakter angka/simbol telepon.';
        }

        if ($profileValues['city'] !== '' && strlen($profileValues['city']) > 120) {
            $errors[] = 'Kota maksimal 120 karakter.';
        }

        if ($profileValues['country'] !== '' && strlen($profileValues['country']) > 120) {
            $errors[] = 'Negara maksimal 120 karakter.';
        }

        if ($profileValues['birth_date'] !== '' && !is_valid_date_value($profileValues['birth_date'])) {
            $errors[] = 'Tanggal lahir tidak valid.';
        }

        if (!in_array($profileValues['preferred_contact'], $allowedContacts, true)) {
            $errors[] = 'Pilih preferensi kontak yang valid.';
        }

        if (!in_array($profileValues['travel_style'], $allowedStyles, true)) {
            $errors[] = 'Pilih gaya perjalanan yang valid.';
        }

        if (strlen($profileValues['bio']) > 500) {
            $errors[] = 'Bio maksimal 500 karakter.';
        }

        if (!$errors && auth_update_profile($connection, $user['id'], $profileValues)) {
            $user = auth_find_user_by_id($connection, $user['id']);
            auth_login($user);
            $sessionUser = auth_user();
            $profileValues = [
                'name' => $user['name'] ?? '',
                'email' => $user['email'] ?? '',
                'phone' => $user['phone'] ?? '',
                'city' => $user['city'] ?? '',
                'country' => $user['country'] ?? '',
                'birth_date' => $user['birth_date'] ?? '',
                'preferred_contact' => $user['preferred_contact'] ?? 'email',
                'travel_style' => $user['travel_style'] ?? '',
                'bio' => $user['bio'] ?? '',
            ];
            $success = 'Profil berhasil diperbarui.';
        } elseif (!$errors) {
            $errors[] = 'Profil belum bisa diperbarui. Silakan coba lagi nanti.';
        }
    } elseif ($action === 'password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $newPasswordConfirmation = $_POST['new_password_confirmation'] ?? '';

        if ($currentPassword === '' || !password_verify($currentPassword, $user['password_hash'] ?? '')) {
            $errors[] = 'Password saat ini tidak sesuai.';
        }

        if (strlen($newPassword) < 8) {
            $errors[] = 'Password baru minimal 8 karakter.';
        }

        if ($newPassword !== $newPasswordConfirmation) {
            $errors[] = 'Konfirmasi password baru tidak sama.';
        }

        if (!$errors && auth_update_password($connection, $user['id'], $newPassword)) {
            $success = 'Password berhasil diperbarui.';
        } elseif (!$errors) {
            $errors[] = 'Password belum bisa diperbarui. Silakan coba lagi nanti.';
        }
    }
}

$page_title = 'Pengaturan Profil';
$page_desc = 'Kelola profil, preferensi akun, password, dan sign-out Bali Paradise.';
$page_css = 'styles/auth.css';
$active = 'profile';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/navbar.php';
?>

<main class="auth-page profile-page">
  <section class="profile-shell" aria-labelledby="profile-title">
    <aside class="profile-summary-card">
      <span class="auth-eyebrow">Account Settings</span>
      <h1 id="profile-title">Pengaturan profil.</h1>
      <p>Kelola identitas akun, preferensi perjalanan, keamanan password, dan sesi login Anda.</p>

      <div class="profile-avatar" aria-hidden="true">
        <?= e(strtoupper(substr($profileValues['name'] ?: 'U', 0, 1))) ?>
      </div>

      <div class="profile-summary-list">
        <span>
          <small>Nama</small>
          <strong><?= e($profileValues['name'] ?: '-') ?></strong>
        </span>
        <span>
          <small>Email</small>
          <strong><?= e($profileValues['email'] ?: '-') ?></strong>
        </span>
        <span>
          <small>Role</small>
          <strong><?= e($user['role_label'] ?? $sessionUser['role'] ?? 'User') ?></strong>
        </span>
      </div>

      <form class="profile-signout-form" action="logout.php" method="post">
        <?= csrf_field() ?>
        <button class="btn btn--outline btn--block" type="submit">Sign Out</button>
      </form>
    </aside>

    <section class="profile-content">
      <?php if ($success): ?>
        <div class="auth-alert auth-alert--success" role="status"><?= e($success) ?></div>
      <?php endif; ?>

      <?php if ($errors): ?>
        <div class="auth-alert auth-alert--error" role="alert">
          <strong>Pengaturan belum bisa disimpan.</strong>
          <ul>
            <?php foreach ($errors as $error): ?>
              <li><?= e($error) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <article class="auth-card profile-card">
        <div class="auth-card__head">
          <span class="auth-eyebrow">Profile Detail</span>
          <h2>Data pribadi dan preferensi.</h2>
          <p>Informasi ini membantu invoice dan booking internal tetap rapi.</p>
        </div>

        <form class="auth-form profile-form" action="profile.php" method="post" novalidate>
          <?= csrf_field() ?>
          <input type="hidden" name="profile_action" value="profile" />

          <div class="profile-form-grid">
            <div class="input-group">
              <label for="name">Nama lengkap</label>
              <input id="name" name="name" type="text" value="<?= e($profileValues['name']) ?>" minlength="2" maxlength="120" required />
            </div>

            <div class="input-group">
              <label for="email">Email login</label>
              <input id="email" name="email" type="email" value="<?= e($profileValues['email']) ?>" maxlength="190" required />
            </div>

            <div class="input-group">
              <label for="phone">Nomor telepon / WhatsApp</label>
              <input id="phone" name="phone" type="tel" value="<?= e($profileValues['phone']) ?>" maxlength="30" />
            </div>

            <div class="input-group">
              <label for="birth_date">Tanggal lahir</label>
              <input id="birth_date" name="birth_date" type="date" value="<?= e($profileValues['birth_date']) ?>" />
            </div>

            <div class="input-group">
              <label for="city">Kota</label>
              <input id="city" name="city" type="text" value="<?= e($profileValues['city']) ?>" maxlength="120" />
            </div>

            <div class="input-group">
              <label for="country">Negara</label>
              <input id="country" name="country" type="text" value="<?= e($profileValues['country']) ?>" maxlength="120" />
            </div>

            <div class="input-group">
              <label for="preferred_contact">Preferensi kontak</label>
              <select id="preferred_contact" name="preferred_contact">
                <option value="email" <?= $profileValues['preferred_contact'] === 'email' ? 'selected' : '' ?>>Email</option>
                <option value="phone" <?= $profileValues['preferred_contact'] === 'phone' ? 'selected' : '' ?>>Telepon</option>
                <option value="whatsapp" <?= $profileValues['preferred_contact'] === 'whatsapp' ? 'selected' : '' ?>>WhatsApp</option>
              </select>
            </div>

            <div class="input-group">
              <label for="travel_style">Gaya perjalanan</label>
              <select id="travel_style" name="travel_style">
                <option value="" <?= $profileValues['travel_style'] === '' ? 'selected' : '' ?>>Belum ditentukan</option>
                <option value="relax" <?= $profileValues['travel_style'] === 'relax' ? 'selected' : '' ?>>Relax</option>
                <option value="adventure" <?= $profileValues['travel_style'] === 'adventure' ? 'selected' : '' ?>>Adventure</option>
                <option value="culture" <?= $profileValues['travel_style'] === 'culture' ? 'selected' : '' ?>>Culture</option>
                <option value="family" <?= $profileValues['travel_style'] === 'family' ? 'selected' : '' ?>>Family</option>
                <option value="business" <?= $profileValues['travel_style'] === 'business' ? 'selected' : '' ?>>Business</option>
              </select>
            </div>
          </div>

          <div class="input-group">
            <label for="bio">Bio singkat</label>
            <textarea id="bio" name="bio" rows="4" maxlength="500"><?= e($profileValues['bio']) ?></textarea>
          </div>

          <button class="btn btn--primary btn--block" type="submit">Simpan Profil</button>
        </form>
      </article>

      <article class="auth-card profile-card">
        <div class="auth-card__head">
          <span class="auth-eyebrow">Security</span>
          <h2>Ubah password.</h2>
          <p>Gunakan password baru minimal 8 karakter.</p>
        </div>

        <form class="auth-form profile-form" action="profile.php" method="post" novalidate>
          <?= csrf_field() ?>
          <input type="hidden" name="profile_action" value="password" />

          <div class="profile-form-grid">
            <div class="input-group">
              <label for="current_password">Password saat ini</label>
              <input id="current_password" name="current_password" type="password" autocomplete="current-password" required />
            </div>

            <div class="input-group">
              <label for="new_password">Password baru</label>
              <input id="new_password" name="new_password" type="password" autocomplete="new-password" minlength="8" required />
            </div>

            <div class="input-group">
              <label for="new_password_confirmation">Konfirmasi password baru</label>
              <input id="new_password_confirmation" name="new_password_confirmation" type="password" autocomplete="new-password" minlength="8" required />
            </div>
          </div>

          <button class="btn btn--outline btn--block" type="submit">Update Password</button>
        </form>
      </article>
    </section>
  </section>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
