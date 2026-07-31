# Invoice & Profile Dropdown UI Fix Report

## 1. Ringkasan Perubahan

Perubahan difokuskan pada header/navbar/topbar agar akses Invoice dan Profile berada di sisi kanan dekat tombol dark-mode, dengan ukuran tombol yang proporsional dan dropdown terpisah.

Implementasi dilakukan untuk:

- User/public layout melalui `partials/navbar.php`.
- Admin dashboard layout melalui `admin/index.php`.
- Behavior dropdown global melalui `assets/js/app.js`.
- Style responsive dan dark-mode melalui `styles/_components.css` dan `styles/admin.css`.
- Dokumentasi ringkas melalui `README.md`.

## 2. Root Cause / UI Issue

Kondisi awal:

- Invoice dan Profile sebelumnya berada dalam pola menu akun yang kurang terpisah.
- Pada layout admin, dark-mode masih berada di sidebar dan admin topbar belum memiliki akses Invoice/Profile yang sejajar.
- Dropdown perlu dibuat lebih konsisten, compact, dan kompatibel dengan light/dark mode.

Masalah UI/UX yang diperbaiki:

- Akses Invoice dan Profile dipindahkan ke action area kanan dekat dark-mode.
- Invoice dan Profile dipisah menjadi dua tombol agar lebih jelas dan mudah dipindai.
- Dropdown dibuat modern, minimalis, memiliki shadow halus, radius konsisten, hover state, animasi, click outside, dan Escape close.
- Mobile admin diberi override agar dropdown tidak memakai posisi fixed milik navbar publik.

## 3. File yang Dianalisis

- `partials/navbar.php`
- `partials/head.php`
- `admin/index.php`
- `admin/partials/nav.php`
- `styles/_components.css`
- `styles/admin.css`
- `assets/js/app.js`
- `includes/auth.php`
- `profile.php`
- `logout.php`
- `booking/history.php`
- `booking/history-count.php`
- `booking/index.php`
- `README.md`

Project ini PHP native, bukan Laravel. File Laravel seperti `routes/web.php`, `resources/views`, `app/Http/Controllers`, dan `artisan` tidak tersedia.

## 4. File yang Diubah

| No | File | Perubahan | Alasan |
|---:|---|---|---|
| 1 | `partials/navbar.php` | Membuat tombol Invoice dan Profile terpisah di kanan dekat dark-mode untuk user/login publik. | Memenuhi kebutuhan topbar user yang compact dan profesional. |
| 2 | `assets/js/app.js` | Menambahkan handler generic `[data-topbar-menu]`, click outside, Escape close, aria-expanded, dan sinkron badge invoice. | Dropdown Invoice/Profile bisa berjalan konsisten tanpa merusak hamburger/dark-mode. |
| 3 | `styles/_components.css` | Menambahkan/merapikan style tombol topbar, dropdown, badge, responsive, hover, dan dark-mode. | Menyamakan ukuran tombol dengan dark-mode dan membuat dropdown modern minimalis. |
| 4 | `admin/index.php` | Menambahkan action cluster admin: Dark Mode, Invoice, Profile dropdown, Admin link, dan Sign Out POST. | Role admin juga mendapat UI topbar yang sama dan tetap menjaga akses admin. |
| 5 | `admin/partials/nav.php` | Mengganti area sidebar logout/theme lama menjadi meta panel non-interaktif. | Dark-mode dan logout dipindahkan ke topbar admin agar konsisten. |
| 6 | `styles/admin.css` | Menambahkan layout action admin topbar, membersihkan selector lama, dan override responsive dropdown admin. | Mencegah layout mobile admin berantakan dan mengurangi CSS mati. |
| 7 | `README.md` | Memperbarui catatan navbar menjadi separate Invoice/Profile dropdown. | Dokumentasi mengikuti implementasi terbaru. |

## 5. Layout Admin

Implementasi admin:

- `admin/index.php` sekarang memiliki action cluster di sisi kanan topbar.
- Urutan tombol admin: Dark Mode, Invoice, Profile.
- Tombol Invoice memakai route:
  - `booking/history.php`
  - `booking/index.php`
  - `booking/history-count.php` untuk badge sinkron.
- Tombol Profile menampilkan:
  - Nama admin.
  - Email admin.
  - Role admin.
  - Link `profile.php`.
  - Link `admin/index.php`.
  - Sign Out via form POST ke `logout.php` dengan CSRF.
- Sidebar admin tidak lagi menampung tombol dark-mode/logout utama.

## 6. Layout User

Implementasi user:

- `partials/navbar.php` menampilkan tombol Invoice dan Profile hanya saat user sudah login.
- Guest tidak melihat menu Invoice/Profile.
- User biasa melihat:
  - Invoice dropdown.
  - Profile dropdown.
  - Sign Out.
- User biasa tidak mendapat link `admin/index.php`.
- Badge invoice tersinkron melalui `booking/history-count.php` dan hanya membaca data akun aktif.

## 7. Dropdown Design

Dropdown dibuat dengan karakter:

- Compact, clean, dan tidak terlalu lebar.
- Background memakai token `var(--card-bg)` sehingga mengikuti light/dark mode.
- Border memakai token `var(--border)`.
- Shadow memakai token project.
- Radius modern 22px untuk popup dan radius lebih kecil untuk item internal.
- Hover state halus.
- Animasi menggunakan `opacity` dan `transform`.
- Posisi align kanan pada desktop.
- Responsive mobile tetap aman:
  - Navbar publik memakai posisi fixed yang tidak keluar layar.
  - Admin topbar mobile memakai override agar dropdown turun dari action cluster.
- Dropdown tertutup saat klik luar atau menekan Escape.

Screenshot verifikasi:

- `storage/tmp-preview/topbar-invoice-desktop.png`
- `storage/tmp-preview/topbar-profile-desktop.png`
- `storage/tmp-preview/topbar-profile-mobile.png`
- `storage/tmp-preview/admin-topbar-invoice-desktop.png`
- `storage/tmp-preview/admin-topbar-profile-desktop.png`
- `storage/tmp-preview/admin-topbar-profile-mobile-v2.png`

## 8. Route dan Permission

Route yang digunakan:

- Invoice history: `booking/history.php`
- Invoice count sync: `booking/history-count.php`
- New booking: `booking/index.php`
- Profile: `profile.php`
- Admin dashboard: `admin/index.php`
- Logout: `logout.php` via POST + CSRF.

Permission yang dijaga:

- Guest tidak melihat menu Invoice/Profile.
- User login melihat Invoice/Profile tetapi tidak melihat link admin.
- Admin melihat Invoice/Profile dan link Admin.
- Akses admin tetap dikontrol oleh `require_admin()` di halaman admin.
- Logout tetap memakai POST dan CSRF, bukan GET.

Tidak ada route palsu atau link dummy `#` yang ditambahkan.

## 9. Error yang Diperbaiki

| No | Error | Penyebab | Fix | Status |
|---:|---|---|---|---|
| 1 | Invoice/Profile masih terasa sebagai satu menu akun gabungan. | Struktur awal belum memisahkan intent Invoice dan Profile. | Dipisah menjadi dua tombol dan dua dropdown. | Fixed |
| 2 | Layout admin belum mengikuti topbar kanan dekat dark-mode. | Admin memakai layout khusus, bukan `partials/navbar.php`. | Menambahkan action cluster di `admin/index.php`. | Fixed |
| 3 | Dropdown admin mobile memakai posisi fixed navbar publik. | CSS global `.nav__topbar-dropdown` mobile ditujukan untuk navbar publik. | Menambahkan override khusus `.admin-topbar__actions` di `styles/admin.css`. | Fixed |
| 4 | Selector CSS admin lama tidak lagi relevan. | Theme/logout sidebar dipindahkan ke topbar. | Membersihkan selector `admin-user`, `admin-sidebar__logout`, dan `admin-theme`. | Fixed |

## 10. Validation Result

Validasi yang dijalankan:

- `php -l` untuk semua file PHP di project: Passed.
- `php -l admin/index.php`: Passed.
- `php -l admin/partials/nav.php`: Passed.
- `php -l partials/navbar.php`: Passed.
- `node --check assets/js/app.js`: Passed.
- HTTP smoke test guest `/index.php`: 200, tidak melihat `data-topbar-menu`.
- HTTP smoke test user `/index.php`: melihat Invoice/Profile, tidak melihat link admin.
- HTTP smoke test user `booking/history-count.php`: JSON OK, count tersinkron.
- HTTP smoke test admin `/index.php`: melihat Invoice/Profile dan link admin.
- HTTP smoke test admin `/admin/index.php`: 200, topbar admin berisi Invoice/Profile.
- Edge headless screenshot desktop/mobile: berhasil dibuat.

Validasi yang tidak dijalankan karena tidak relevan:

- `php artisan route:list`: tidak ada Laravel/artisan.
- `php artisan view:clear`: tidak ada Laravel/artisan.
- `php artisan cache:clear`: tidak ada Laravel/artisan.
- `php artisan config:clear`: tidak ada Laravel/artisan.
- `npm run build`: tidak ada `package.json`.

Catatan: Edge headless menampilkan warning internal browser seperti `vbs_encoder`/sync, tetapi screenshot tetap berhasil dibuat dan warning tersebut bukan error aplikasi.

## 11. Remaining Issues

- Tidak ada issue blocking pada perubahan topbar Invoice/Profile.
- Perlu uji manual klik di browser real untuk memastikan rasa interaksi sesuai preferensi pengguna.
- Worktree project masih berisi banyak perubahan lama di luar scope laporan ini; perubahan tersebut tidak disentuh.

## 12. Manual Testing Checklist

- [ ] Login sebagai admin.
- [ ] Cek tombol Invoice di kanan dekat dark-mode.
- [ ] Klik tombol Invoice.
- [ ] Dropdown Invoice muncul.
- [ ] Klik link Invoice.
- [ ] Klik luar dropdown, dropdown tertutup.
- [ ] Klik tombol Profile.
- [ ] Dropdown Profile muncul.
- [ ] Klik link Profile.
- [ ] Logout tetap berjalan.
- [ ] Aktifkan dark-mode.
- [ ] Dropdown tetap terbaca.
- [ ] Login sebagai user.
- [ ] Ulangi test Invoice dan Profile.
- [ ] Cek responsive mobile.

## 13. Final Status

Fixed.

Invoice dan Profile sudah dipindahkan menjadi tombol terpisah di sisi kanan dekat dark-mode untuk user dan admin. Dropdown sudah memiliki behavior modern, responsive, dark-mode compatible, dan validasi PHP/JS/HTTP smoke test sudah lolos.
