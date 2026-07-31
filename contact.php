<?php
require_once __DIR__ . '/includes/auth.php';
auth_start_session();

$contact_status = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $contact_status = verify_csrf_token($_POST['csrf_token'] ?? '')
    ? 'Demo terkirim. Pesan belum dikirim ke server.'
    : 'Sesi form tidak valid. Muat ulang halaman lalu coba lagi.';
}

$page_title = 'Contact';
$page_desc  = 'Hubungi Bali Paradise untuk pertanyaan wisata, tiket, dan perjalanan.';
$page_css   = 'styles/contact.css';
$active     = 'contact';
include 'partials/head.php';
include 'partials/navbar.php';
?>

  <main class="contact-page">
    <section class="contact-hero" aria-labelledby="contact-title">
      <div class="contact-hero__media" aria-hidden="true"></div>

      <div class="contact-hero__content" data-reveal="fade">
        <span class="contact-eyebrow">Bali Paradise support</span>
        <h1 id="contact-title">Rencanakan perjalanan Bali dengan lebih tenang.</h1>
        <p>
          Kirim pertanyaan tentang destinasi, tiket, transportasi, atau kebutuhan perjalanan lain.
          Tim kami akan membantu menyiapkan arahan yang jelas sebelum Anda berangkat.
        </p>

        <div class="contact-quick">
          <article>
            <span>Jam layanan</span>
            <strong>08.00 - 21.00 WITA</strong>
          </article>
          <article>
            <span>Respon rata-rata</span>
            <strong>Kurang dari 2 jam</strong>
          </article>
        </div>
      </div>

      <aside class="contact-card contact-card--info">
        <div class="contact-card__header">
          <span class="contact-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.78 19.78 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.78 19.78 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.12.91.33 1.8.62 2.65a2 2 0 0 1-.45 2.11L8.09 9.67a16 16 0 0 0 6.24 6.24l1.19-1.19a2 2 0 0 1 2.11-.45c.85.29 1.74.5 2.65.62A2 2 0 0 1 22 16.92z"/>
            </svg>
          </span>
          <div>
            <span>Kontak utama</span>
            <strong>Trip assistance</strong>
          </div>
        </div>

        <ul class="contact-list">
          <li>
            <span>Email</span>
            <a href="mailto:hello@baliparadise.test">hello@baliparadise.test</a>
          </li>
          <li>
            <span>Telepon</span>
            <a href="tel:+6281238471928">+62 812 3847 1928</a>
          </li>
          <li>
            <span>Lokasi</span>
            <strong>Denpasar, Bali</strong>
          </li>
        </ul>
      </aside>
    </section>

    <section class="contact-shell" aria-label="Contact form">
      <div class="contact-form-panel">
        <div class="form-heading">
          <span class="contact-eyebrow">Kirim pesan</span>
          <h2>Beritahu kebutuhan perjalanan Anda.</h2>
          <p>Form ini masih mode demo, jadi pesan tidak dikirim ke server.</p>
        </div>

        <form id="contactForm" class="contact-form" action="" method="post" novalidate>
          <?= csrf_field() ?>
          <div class="form-grid">
            <div class="input-group">
              <label for="contactName">Nama</label>
              <input id="contactName" type="text" name="username" placeholder="Nama lengkap" autocomplete="name" required />
              <small class="field-error" data-error-for="contactName"></small>
            </div>

            <div class="input-group">
              <label for="contactEmail">Email</label>
              <input id="contactEmail" type="email" name="email" placeholder="nama@email.com" autocomplete="email" required />
              <small class="field-error" data-error-for="contactEmail"></small>
            </div>
          </div>

          <div class="input-group">
            <label for="contactTopic">Topik</label>
            <select id="contactTopic" name="topic" required>
              <option value="">Pilih topik</option>
              <option value="destination">Destinasi</option>
              <option value="ticket">Tiket</option>
              <option value="transport">Transportasi</option>
              <option value="hotel">Hotel</option>
              <option value="other">Lainnya</option>
            </select>
            <small class="field-error" data-error-for="contactTopic"></small>
          </div>

          <div class="input-group">
            <label for="contactMessage">Pesan</label>
            <textarea id="contactMessage" rows="6" name="message" placeholder="Tulis detail pertanyaan atau rencana perjalanan Anda" required></textarea>
            <small class="field-error" data-error-for="contactMessage"></small>
          </div>

          <button class="contact-submit" type="submit">
            <span>Kirim demo</span>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
              <path d="m22 2-7 20-4-9-9-4Z"/>
              <path d="M22 2 11 13"/>
            </svg>
          </button>

          <div class="success-message <?= $contact_status ? 'is-visible' : '' ?>" id="successMessage" role="status" aria-live="polite">
            <?= e($contact_status ?: 'Demo terkirim. Pesan belum dikirim ke server.') ?>
          </div>
        </form>
      </div>

      <div class="contact-side">
        <article class="contact-card">
          <span class="contact-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M21 15a4 4 0 0 1-4 4H7l-4 4V7a4 4 0 0 1 4-4h10a4 4 0 0 1 4 4z"/>
            </svg>
          </span>
          <h3>Konsultasi perjalanan</h3>
          <p>Pilih rute, estimasi waktu, dan opsi transportasi yang sesuai dengan jadwal Anda.</p>
        </article>

        <article class="contact-card">
          <span class="contact-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
              <path d="M9 11l3 3L22 4"/>
              <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
            </svg>
          </span>
          <h3>Informasi pemesanan</h3>
          <p>Cek detail tiket, hotel, atau sewa mobil sebelum melanjutkan proses booking.</p>
        </article>
      </div>
    </section>
  </main>

  <script>
    (function () {
      var form = document.getElementById("contactForm");
      var successMessage = document.getElementById("successMessage");

      function setError(field, message) {
        var error = document.querySelector('[data-error-for="' + field.id + '"]');
        field.classList.toggle("is-invalid", Boolean(message));
        field.setAttribute("aria-invalid", message ? "true" : "false");
        if (error) {
          error.textContent = message || "";
        }
      }

      function validateField(field) {
        var value = field.value.trim();
        var message = "";

        if (!value) {
          message = "Bagian ini wajib diisi.";
        } else if (field.type === "email" && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
          message = "Masukkan email yang valid.";
        } else if (field.id === "contactMessage" && value.length < 12) {
          message = "Pesan minimal 12 karakter.";
        }

        setError(field, message);
        return !message;
      }

      form.addEventListener("input", function (event) {
        if (event.target.matches("input, select, textarea")) {
          validateField(event.target);
        }
      });

      form.addEventListener("submit", function (event) {
        event.preventDefault();
        var fields = Array.prototype.slice.call(form.querySelectorAll("input, select, textarea"));
        var isValid = fields.every(validateField);

        if (!isValid) {
          var firstInvalid = form.querySelector(".is-invalid");
          if (firstInvalid) {
            firstInvalid.focus();
          }
          successMessage.classList.remove("is-visible");
          return;
        }

        successMessage.classList.add("is-visible");

        setTimeout(function () {
          form.reset();
          fields.forEach(function (field) {
            setError(field, "");
          });
          successMessage.classList.remove("is-visible");
        }, 3200);
      });
    })();
  </script>
<?php include 'partials/footer.php'; ?>
