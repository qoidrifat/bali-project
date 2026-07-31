<?php
require_once __DIR__ . '/../includes/auth.php';

if (!function_exists('require_admin')) {
    function require_admin($redirect = '../login.php', $baseHref = '../')
    {
        auth_start_session();

        if (!auth_check()) {
            header('Location: ' . $redirect);
            exit;
        }

        if (!auth_has_role('admin')) {
            http_response_code(403);

            $page_title = 'Akses Admin Ditolak';
            $page_desc = 'Halaman ini hanya untuk administrator Bali Paradise.';
            $page_css = 'styles/auth.css';
            $active = '';
            $base_href = $baseHref;
            include __DIR__ . '/../partials/head.php';
            include __DIR__ . '/../partials/navbar.php';
            ?>
            <main class="auth-page auth-page--compact">
              <section class="auth-card auth-card--center" aria-labelledby="admin-denied-title">
                <div class="auth-card__head">
                  <span class="auth-eyebrow">Admin Area</span>
                  <h1 id="admin-denied-title">Akses tidak diizinkan.</h1>
                  <p>Dashboard admin hanya tersedia untuk akun dengan role <strong>admin</strong>.</p>
                </div>
                <p class="auth-switch">
                  <a href="index.php">Kembali ke homepage</a>
                </p>
              </section>
            </main>
            <?php
            include __DIR__ . '/../partials/footer.php';
            exit;
        }

        return auth_user();
    }
}
