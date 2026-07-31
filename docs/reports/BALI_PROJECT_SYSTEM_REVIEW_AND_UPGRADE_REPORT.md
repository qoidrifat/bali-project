# BALI PROJECT  System Review & Upgrade Report

Tanggal audit: 2026-06-08  
Lokasi audit: `C:\laragon\www\bali-project`  
Lokasi kode aplikasi aktual terbaru: `C:\laragon\www\bali-project`

Catatan ruang lingkup:

- Audit dilakukan secara read-only terhadap kode utama.
- Tidak ada perubahan database, migration, delete, `git reset`, `git clean`, `composer update`, atau `npm update`.
- File yang dibuat hanya laporan ini.
- Credential yang ditemukan tidak ditampilkan sebagai secret penuh. Karena credential masih berupa konfigurasi lokal default, nilainya hanya disebut sebagai pola risiko.
- Root parent berisi folder aplikasi lagi. Jadi project aktual berada di subfolder `bali-project`.

## 1. Executive Summary

Bali Project adalah website wisata Bali berbasis PHP native dan MySQL/MariaDB. Project tidak menggunakan Laravel, CodeIgniter, Composer, npm build system, React, Vue, atau Vite. Arsitektur saat ini masih sederhana: file `.php` langsung berperan sebagai route, view, dan logic backend.

Kondisi project sudah lebih baik dibanding audit awal. Beberapa area penting sudah dimodernisasi:

- Halaman utama dan banyak halaman target sudah memakai `partials/head.php`, `partials/navbar.php`, dan `partials/footer.php`.
- `detail.php` sudah memakai validasi `id` dan prepared statement.
- Halaman hasil `hasil.bus.php`, `hasil.hotel.php`, `hasil.pesawat.php`, dan `hasil.mobil.php` sudah memakai validasi `$_GET` dan prepared statement.
- Ada design system custom melalui `styles/_tokens.css`, `_base.css`, `_components.css`, `_animations.css`, dan `_theme-sync.css`.
- Dark mode dan light mode sudah disinkronkan lebih luas.
- Folder legacy sudah diarahkan ke `_archive`, walaupun status git masih menunjukkan perpindahan/penghapusan lama belum final.
- File SQL sudah diblokir lewat `.htaccess`, tetapi tetap belum ideal untuk production jika masih berada di web root.

Secara umum project layak dilanjutkan sebagai project pembelajaran atau prototype wisata. Untuk menjadi sistem production, prioritas berikutnya adalah merapikan konfigurasi database, menghapus ketergantungan pada credential lokal, membuat struktur backend yang lebih rapi, memperbaiki file SQL mentah yang masih berada di web root, menstandarkan UI lama, dan menyiapkan dokumentasi deployment.

Ringkasan temuan terbaru:

| Level | Jumlah | Status Umum |
|---|---:|---|
| Critical | 0 | Tidak ditemukan critical aktif pada kode utama saat audit ini, tetapi ada risiko production jika proteksi `.htaccess` tidak berlaku. |
| High | 4 | Fokus pada credential DB, config, output HTML dari DB, dan ketergantungan ke Apache `.htaccess`. |
| Medium | 10 | Fokus pada struktur, error handling, no CSRF, data schema, placeholder link, dan maintainability. |
| Low | 8 | Fokus pada cleanup, asset, naming, duplikasi CSS, dan polish. |

## 2. Technology Stack Detected

| Teknologi | Status | Bukti File/Folder |
|---|---|---|
| PHP Native | Terdeteksi | `index.php`, `destination.php`, `detail.php`, `hasil.bus.php`, `hasil.hotel.php`, `hasil.pesawat.php`, `hasil.mobil.php` |
| MySQL/MariaDB | Terdeteksi | `connection.php`, `bali.sql`, query `mysqli_connect`, `new mysqli`, tabel SQL di `bali.sql` |
| HTML/CSS/JavaScript | Terdeteksi | HTML langsung di file PHP, folder `styles/`, `assets/js/app.js` |
| Custom CSS Design System | Terdeteksi | `styles/_tokens.css`, `_base.css`, `_components.css`, `_animations.css`, `_theme-sync.css` |
| Bootstrap | Tidak terdeteksi | Tidak ada import Bootstrap lokal/CDN yang dominan |
| Tailwind | Tidak terdeteksi | Tidak ada `tailwind.config.*`, build config, atau utility Tailwind |
| Laravel | Tidak terdeteksi | Tidak ada `artisan`, `composer.json`, `app/`, `routes/`, `resources/`, `config/` Laravel |
| CodeIgniter | Tidak terdeteksi | Tidak ada struktur `application/`, `system/`, `app/Config/` |
| Composer Dependency | Tidak dipakai | `composer.json` tidak ditemukan |
| NPM Dependency | Tidak dipakai | `package.json` tidak ditemukan |
| Database Migration | Tidak ada | Hanya dump SQL `bali.sql`, tidak ada folder migration |
| Authentication | Tidak ada | Tidak ditemukan login/register/session auth/role middleware |
| Routing | File-based | Setiap file `.php` menjadi endpoint langsung |

Hasil command aman:

- `php --version`: PHP 8.2.12 terdeteksi.
- `composer --version`: Composer tersedia di mesin, tetapi project tidak memakai Composer.
- `npm --version`: npm tersedia di mesin, tetapi project tidak memakai npm.
- `php -l`: 28 file PHP lulus syntax check.

## 3. Project Structure Review

Struktur aktual:

```text
C:\laragon\www\bali-project\
|-- BALI_PROJECT_DEEP_ANALYSIS_REPORT.md
|-- BALI_PROJECT_SYSTEM_REVIEW_AND_UPGRADE_REPORT.md
|-- bali-project\
    |-- .git\
    |-- .gitignore
    |-- .htaccess
    |-- *.php
    |-- bali.sql
    |-- README.md
    |-- assets\js\app.js
    |-- database\README.md
    |-- images\
    |-- images\optimized\
    |-- partials\
    |-- styles\
    |-- _archive\
```

Kelebihan struktur:

- Root aplikasi sudah punya `partials/` untuk shared head, navbar, footer.
- CSS modern mulai dipisahkan ke token, base, components, animations, dan theme sync.
- Asset JS global dipusatkan di `assets/js/app.js`.
- Dump canonical `bali.sql` sudah disebut di README.
- Folder legacy sudah tidak lagi berada sebagai `pariwisataweb` aktif; sudah ada `_archive`.
- `.htaccess` sudah memblokir direct download file SQL/database dan memblokir `hasil.transport.php`.

Kekurangan struktur:

- Catatan historis: sebelumnya ada nesting folder aplikasi. Status terbaru: root aktif sudah dirapikan ke `C:\laragon\www\bali-project`.
- File PHP root masih memuat HTML, query, validasi, dan script dalam satu file.
- Tidak ada folder `config/`, `controllers/`, `views/`, `services/`, atau `helpers/`.
- `connection.php` masih berada di web root.
- `bali.sql` masih berada di web root aplikasi. `.htaccess` membantu di Apache, tetapi tidak cukup aman untuk semua deployment.
- `_archive` masih berada di dalam web root aplikasi. Jika server tidak memblokirnya, file legacy tetap bisa terlihat.
- `hasil.transport.php` masih ada dan berisi SQL mentah, bukan halaman PHP. File ini sudah diblokir `.htaccess`, tetapi secara struktur tetap membingungkan.
- Ada banyak CSS page-specific lama dan CSS modern baru yang bercampur.
- Beberapa link placeholder `href="#"` masih ada di footer dan kartu transport lama.

Rekomendasi perapihan:

1. Gunakan `C:\laragon\www\bali-project` sebagai root kode aktif dan pertahankan URL lokal `http://localhost/bali-project/`.
2. Buat folder:
   - `config/` untuk koneksi/config non-public.
   - `includes/` atau `partials/` untuk komponen shared.
   - `pages/` atau tetap root file-based, tetapi dengan pola konsisten.
   - `storage/logs/` untuk log aplikasi bila diperlukan.
   - `database/dumps/` di luar public root untuk dump lokal.
3. Pindahkan `_archive` dan SQL dump ke luar document root saat production.
4. Hapus file SQL mentah `hasil.transport.php` setelah backup dan konfirmasi tidak dipakai.
5. Standarkan CSS halaman lama ke design token yang sudah dibuat.

## 4. Backend Review

Kondisi backend:

- Backend masih PHP native procedural.
- Routing memakai file langsung, contoh `destination.php`, `detail.php`, `transport.php`, `hasil.pesawat.php`.
- Tidak ada controller/model formal.
- Tidak ada middleware, auth, role permission, CSRF, atau session management.
- Query database memakai `mysqli`.
- Beberapa halaman sudah memakai prepared statement.
- Error handling sudah membaik di halaman hasil, tetapi belum konsisten di semua file.

Halaman penting:

| File | Fungsi | Status |
|---|---|---|
| `index.php` | Homepage, mengambil 3 destinasi dari DB dengan fallback | Masih memakai `@include_once` dan `@mysqli_query`, error disembunyikan |
| `destination.php` | List destinasi | Sudah ada error handling dasar untuk koneksi/query |
| `detail.php` | Detail destinasi | Sudah validasi `id`, prepared statement, dan kondisi data tidak ditemukan |
| `transport.php` | Form pencarian pesawat | UI modern, submit ke `hasil.pesawat.php` |
| `tiket.bus.php` | Form pencarian bus | UI modern, custom calendar |
| `booking.hotel.php` | Form booking hotel | UI modern, custom calendar |
| `sewa.mobil.php` | Form sewa mobil | UI modern, custom calendar |
| `hasil.bus.php` | Result bus | Validasi GET dan prepared statement |
| `hasil.hotel.php` | Result hotel | Validasi GET dan prepared statement |
| `hasil.pesawat.php` | Result pesawat | Validasi GET dan prepared statement |
| `hasil.mobil.php` | Result mobil | Validasi GET dan prepared statement |
| `contact.php` | Form contact demo | Validasi frontend, belum ada backend storage/send |
| `partials/booking_alternatives.php` | Komponen alternatif booking | Reusable, link eksternal dengan `noopener noreferrer` |

Potensi error backend:

- `connection.php` memakai credential lokal hardcoded dan tidak punya helper reusable untuk error handling.
- `index.php` masih memakai operator `@`, sehingga error koneksi/query dapat tersembunyi.
- Halaman result membuat koneksi database sendiri-sendiri, bukan lewat satu wrapper/helper.
- Output `detail.desc` dari database masih dirender sebagai HTML. Ini cocok untuk konten lama, tetapi riskan jika database bisa diedit oleh admin/user.
- `hasil.transport.php` bukan file PHP valid aplikasi; berisi SQL mentah dan seharusnya tidak menjadi route.
- Tidak ada validasi server-side untuk `contact.php` karena form masih demo.
- Tidak ada central 404/error page.

Rekomendasi backend:

1. Buat `config/database.php` atau `config.php` non-public untuk koneksi.
2. Buat helper `db()` untuk koneksi dan `safe_query_error()` untuk logging.
3. Buat helper validasi umum:
   - `validate_date($value)`
   - `validate_option($value, $allowed)`
   - `e($value)` untuk `htmlspecialchars`.
4. Pecah logic result ke function kecil agar tidak berulang.
5. Tambahkan halaman error/empty state reusable.
6. Jangan render HTML dari DB tanpa sanitasi whitelist.

## 5. Database Review

Database yang digunakan:

- Nama DB di kode: `bali`
- Dump canonical: `bali-project\bali.sql`
- Engine: MySQL/MariaDB

Tabel yang ditemukan di `bali.sql`:

| Tabel | Fungsi Saat Ini |
|---|---|
| `destination` | Data destinasi wisata utama |
| `detail` | Detail konten destinasi |
| `detail_image` | Gambar tambahan untuk detail destinasi |
| `destinations` | Kota/area tujuan untuk booking hotel/mobil |
| `from_city` | Kota asal untuk pesawat/bus |
| `to_city` | Kota tujuan untuk pesawat/bus |
| `hotel` | Master hotel |
| `bookings_hotel` | Data availability/link hotel |
| `car` | Master mobil/vendor rental |
| `bookings_mobil` | Data availability/link rental mobil |
| `pesawat` | Master maskapai/pesawat |
| `bookings_pesawat` | Data availability/link pesawat |
| `buses` | Master operator bus |
| `routes_bus` | Data availability/link rute bus |

Relasi yang sudah ada:

- `detail.id_des` ke `destination.id_des`
- `detail_image.id_detail` ke `detail.id_detail`
- `bookings_hotel.id_hotel` ke `hotel.id_hotel`
- `bookings_hotel.id_destinations` ke `destinations.id_destinations`
- `bookings_mobil.id_car` ke `car.id_car`
- `bookings_mobil.id_destinations` ke `destinations.id_destinations`
- `bookings_pesawat.from_id` ke `from_city.from_id`
- `bookings_pesawat.to_id` ke `to_city.to_id`
- `bookings_pesawat.id_pesawat` ke `pesawat.id_pesawat`
- `routes_bus.buses_id` ke `buses.buses_id`
- `routes_bus.from_id` ke `from_city.from_id`
- `routes_bus.to_id` ke `to_city.to_id`

Potensi mismatch/masalah:

- `hasil.transport.php` berisi SQL lama yang membuat `pesawat.name`, sedangkan kode memakai `pesawat.nama_pesawat`.
- `destinations.destinations` menyimpan nilai seperti `1-Surabaya`, kurang normalized. Lebih baik kolom `name`, `slug`, `type`, `province`.
- `rooms` di `bookings_mobil` dipakai untuk jumlah mobil. Nama kolom lebih tepat `car_count` atau `quantity`.
- Data booking saat ini sebenarnya lebih mirip data katalog/link availability statis, bukan booking transactional.
- Tidak ada tabel `users`, `bookings` transaction, `payments`, `reviews`, atau `contacts`.
- Tidak ada audit field seperti `created_at`, `updated_at`, `deleted_at`.
- Banyak link detail mengarah ke platform eksternal. Ini valid untuk prototype, tetapi kurang ideal jika ingin sistem booking sendiri.

Saran normalisasi:

- Pisahkan master data dan transaction:
  - `destinations` untuk wisata.
  - `cities` untuk kota asal/tujuan.
  - `providers` untuk hotel, maskapai, bus, rental.
  - `products` atau tabel spesifik `tickets`, `rooms`, `vehicles`.
  - `bookings` untuk transaksi user.
  - `booking_items` untuk detail item.
  - `payments` untuk status pembayaran.
- Tambahkan slug untuk SEO:
  - `destination.slug`
  - `articles.slug`
- Tambahkan index kombinasi:
  - `routes_bus(from_id, to_id, departure_date, return_date, jumlah_kursi)`
  - `bookings_pesawat(from_id, to_id, departure_date, return_date, jumlah_kursi)`
  - `bookings_hotel(id_destinations, check_in_date, check_out_date, rooms)`
  - `bookings_mobil(id_destinations, check_in_date, check_out_date, rooms)`

Saran tabel upgrade:

| Tabel | Fungsi |
|---|---|
| `users` | Akun user, admin, customer |
| `roles` | Role seperti admin, staff, customer |
| `destinations` | Master destinasi wisata yang lebih lengkap |
| `destination_categories` | Kategori pantai, budaya, alam, keluarga |
| `destination_galleries` | Galeri gambar destinasi |
| `tickets` | Tiket wisata/transport yang dapat dipesan |
| `bookings` | Header transaksi booking |
| `booking_details` | Item booking per tiket/paket |
| `payments` | Status pembayaran dan referensi gateway |
| `reviews` | Rating dan review user |
| `wishlists` | Destinasi favorit user |
| `promos` | Voucher/promo |
| `articles` | Blog/artikel wisata |
| `contacts` | Pesan dari contact form |
| `settings` | Konfigurasi website |

## 6. Frontend & UI/UX Review

Kondisi tampilan terbaru:

- Project sudah mengarah ke design system modern melalui token warna, base styles, components, animations, dan theme sync.
- Navbar modern sudah dipakai melalui partial shared.
- Halaman contact sudah lebih profesional, minimalis, dan responsive.
- Halaman transport, tiket bus, booking hotel, dan sewa mobil sudah memiliki calendar custom dan form modern.
- Halaman hasil sudah memiliki empty-state alternatif platform pemesanan.
- Dark mode dan light mode sudah tersedia dan lebih sinkron.

Kelebihan UI:

- Branding `BaliParadise` sudah konsisten di navbar/footer.
- Warna teal/tropical cocok dengan konteks wisata Bali.
- Layout form booking sudah lebih premium daripada versi awal.
- Ada micro-interaction, hover state, reveal animation, dan focus state.
- Mobile responsive sudah lebih diperhatikan di halaman yang baru disentuh.
- Footer shared sudah memberi struktur navigasi dan newsletter.

Kekurangan UI:

- Beberapa halaman statis seperti `about.php`, `visa.php`, dan `destination.php` masih lebih sederhana dibanding halaman contact/booking terbaru.
- CSS lama masih ada: `global.css`, `navbar.css`, `home.css`, `header.css`, serta inline CSS di beberapa `hasil.*.php`.
- Halaman hasil masih memakai style lama untuk container/table, meskipun empty-state baru sudah modern.
- Homepage sudah modern tetapi masih bisa dibuat lebih premium dengan image hierarchy, section storytelling, destination cards, dan CTA yang lebih jelas.
- Placeholder link `href="#"` masih ada di footer dan beberapa card.
- Beberapa gambar utama masih besar dan belum semua memakai WebP optimized.
- Font masih campuran Poppins, Inter, Plus Jakarta Sans. Perlu standardisasi.

Rekomendasi UI/UX modern:

- Gunakan satu arah design system:
  - Heading: Plus Jakarta Sans.
  - Body: Inter.
  - Accent: teal/tropical green plus sunset coral secukupnya.
- Modernisasi halaman hasil:
  - Card-based result layout.
  - Empty state visual yang reusable.
  - Summary search detail sebagai chips.
  - CTA kembali yang konsisten.
- Modernisasi destination:
  - Grid destinasi premium.
  - Filter kategori.
  - Search destinasi.
  - Detail card dengan gambar responsive.
- Modernisasi about:
  - Story section, mission, stats, trust signals.
- Modernisasi visa:
  - Stepper/timeline dan document checklist.
- Tambahkan SEO meta dinamis untuk detail destinasi.
- Pastikan dark mode dicek di desktop dan mobile untuk semua halaman.

Target visual:

- Profesional, minimalis, elegan, clean.
- Premium tropical, tidak terlalu ramai.
- Banyak whitespace, gambar berkualitas, typography kuat.
- Micro-interaction halus, bukan animasi berlebihan.
- Mobile-first untuk form booking.

## 7. Security Review

| Level | Masalah | Lokasi | Dampak | Rekomendasi |
|---|---|---|---|---|
| High | Credential database hardcoded dan memakai user lokal default | `connection.php`, `hasil.bus.php`, `hasil.hotel.php`, `hasil.pesawat.php`, `hasil.mobil.php` | Sulit deploy, risiko akses database terlalu luas jika konfigurasi terbawa production | Pindahkan ke config non-public atau `.env`, buat user DB khusus dengan privilege minimum |
| High | Dump SQL masih berada di web root aplikasi | `bali.sql` | `.htaccess` memblokir di Apache, tetapi tetap riskan jika server bukan Apache atau config override tidak aktif | Pindahkan dump ke luar public root saat production |
| High | File SQL mentah masih memakai ekstensi `.php` | `hasil.transport.php` | Jika `.htaccess` tidak aktif, isi SQL dapat tampil sebagai text route | Pindahkan ke archive/database non-public atau hapus setelah backup |
| High | HTML dari database dirender mentah | `detail.php`, kolom `detail.desc` | Potensi stored XSS jika konten DB bisa diedit oleh pihak tidak tepercaya | Gunakan sanitasi whitelist HTML atau simpan sebagai Markdown/text |
| Medium | Tidak ada CSRF protection untuk form | Semua form, terutama jika nanti contact/booking menyimpan data | Saat form mulai melakukan write action, rentan CSRF | Tambahkan session token CSRF untuk POST action |
| Medium | Contact form masih frontend demo | `contact.php` | User mengira pesan terkirim padahal tidak masuk server | Simpan ke tabel `contacts` atau tulis jelas sebagai demo |
| Medium | Error handling belum terpusat | Banyak file PHP | Error bisa tersembunyi atau tampil tidak konsisten | Buat helper error/logging dan matikan display error di production |
| Medium | Link eksternal dari DB belum semua memakai `rel` | Link detail hasil dari DB di `hasil.*.php` | Risiko reverse tabnabbing kecil pada `target="_blank"` | Tambahkan `rel="noopener noreferrer"` |
| Low | Placeholder social/footer links | `partials/footer.php` | UX kurang profesional, potensi broken navigation | Isi link asli atau ubah menjadi disabled/hidden |
| Low | Tidak ada security headers lengkap | `.htaccess`, `partials/head.php` | Proteksi browser belum optimal | Tambah `X-Content-Type-Options`, `Referrer-Policy`, CSP bertahap |

Catatan Critical:

- Tidak ditemukan SQL injection aktif pada kode utama yang sudah diaudit. `detail.php` dan halaman hasil sudah memakai prepared statement.
- Risiko dapat naik menjadi Critical bila `.htaccess` tidak aktif dan file SQL/SQL mentah tetap bisa diakses publik.

## 8. Performance Review

Potensi bottleneck:

- Gambar besar masih ada:
  - `images/gwk_1.jpeg` sekitar 12.7 MB.
  - `images/tiket/hotel.jpeg` sekitar 12.3 MB.
  - `images/pnd2.jpg` sekitar 5.4 MB.
  - `images/pnd1.jpg` sekitar 3.8 MB.
  - `images/gwk.jpeg` sekitar 3.5 MB.
- CSS cukup banyak dan bercampur antara CSS lama dan modern:
  - `_components.css` sekitar 20 KB.
  - `transport.css` sekitar 14 KB.
  - `tiket.bus.css` sekitar 12 KB.
  - `booking.hotel.css` sekitar 11 KB.
  - `contact.css` sekitar 10 KB.
- Beberapa halaman memuat CDN Flatpickr langsung dari external source.
- Tidak ada bundling/minification asset.
- Tidak ada cache-control policy yang jelas.
- Query database masih sederhana, tetapi belum ada index kombinasi untuk filter tanggal/rute bila data membesar.
- Tidak ada lazy loading universal untuk semua gambar bermakna, walaupun JS punya lazy-load support terbatas.
- Folder `_archive` dan gambar duplikat menambah ukuran deploy bila ikut production.

Saran optimasi aman:

1. Pakai gambar optimized WebP/AVIF untuk hero dan cards.
2. Tambahkan dimensi gambar (`width`, `height`, `aspect-ratio`) untuk mencegah layout shift.
3. Minify CSS/JS manual saat production atau gunakan script build ringan jika project mulai besar.
4. Gabungkan style lama bertahap ke token/components agar CSS tidak duplikatif.
5. Tambahkan index database untuk query result.
6. Tambahkan `.htaccess` cache-control untuk image/CSS/JS.
7. Jangan deploy `_archive`, SQL dump, atau gambar original besar ke production.

## 9. System Upgrade Recommendations

| No | Upgrade | Kategori | Prioritas | Dampak | Estimasi Kompleksitas |
|---:|---|---|---|---|---|
| 1 | Pindahkan credential DB ke config non-public | Security | Must Have | Mengurangi risiko bocor config production | Low |
| 2 | Pindahkan SQL dump dan `_archive` ke luar public root | Security | Must Have | Mengurangi risiko kebocoran data/skema | Low |
| 3 | Hapus/arsipkan `hasil.transport.php` dari route publik | Security | Must Have | Menghilangkan file SQL mentah di web route | Low |
| 4 | Tambahkan helper koneksi dan error logging | Core System | Must Have | Error handling konsisten dan mudah debug | Medium |
| 5 | Hilangkan operator `@` di `index.php` | Core System | Must Have | Error tidak tersembunyi | Low |
| 6 | Tambahkan `rel="noopener noreferrer"` untuk link eksternal hasil DB | Security | Should Have | Mengurangi risiko tabnabbing | Low |
| 7 | Sanitasi HTML detail destinasi | Security | Should Have | Mengurangi risiko stored XSS | Medium |
| 8 | Refactor halaman result ke komponen/card modern | UI/UX | Should Have | Tampilan lebih profesional | Medium |
| 9 | Redesign homepage premium | UI/UX | Should Have | Dampak visual terbesar untuk user | Medium |
| 10 | Tambahkan search/filter destinasi | Feature | Should Have | UX eksplorasi lebih kuat | Medium |
| 11 | Tambahkan contact backend | Feature | Should Have | Form benar-benar berguna | Medium |
| 12 | Kompres gambar besar dan pakai WebP | Performance | Must Have | Loading lebih cepat | Low |
| 13 | Tambahkan database index kombinasi | Database | Should Have | Query pencarian lebih siap skala | Medium |
| 14 | Buat login/register | Feature | Should Have | Pondasi fitur user dan booking | Medium |
| 15 | Buat dashboard admin | Feature | Should Have | Konten dapat dikelola tanpa edit SQL | High |
| 16 | Buat sistem booking transaction | Feature | Should Have | Project berubah dari katalog ke aplikasi bisnis | High |
| 17 | Tambahkan SEO meta, sitemap, robots.txt | Deployment | Should Have | Lebih siap publish | Low |
| 18 | Tambahkan security headers | Security | Should Have | Hardening browser dasar | Low |
| 19 | Tambahkan README deployment production | Deployment | Must Have | Developer baru lebih mudah deploy | Low |
| 20 | Tambahkan smoke test script | Core System | Nice to Have | Regression lebih mudah dicek | Medium |

## 10. Recommended Feature Upgrade

Fitur yang paling cocok untuk Bali Project:

1. Login/register user
   - User dapat menyimpan booking, wishlist, dan review.

2. Dashboard admin
   - Admin dapat mengelola destinasi, gambar, tiket, hotel, mobil, bus, promo, dan artikel.

3. CRUD destinasi wisata
   - Destinasi tidak lagi bergantung pada edit SQL manual.

4. Search dan filter destinasi
   - Filter berdasarkan kategori, lokasi, harga, durasi, family friendly, populer.

5. Detail destinasi premium
   - Galeri, highlight, fasilitas, lokasi, jam buka, harga tiket, FAQ, review.

6. Sistem booking tiket wisata
   - User memilih tanggal, jumlah tiket, detail pemesan, dan status booking.

7. Payment status dan invoice
   - Mulai dari status manual: pending, paid, cancelled.

8. Review dan rating
   - Meningkatkan trust dan engagement.

9. Wishlist destinasi
   - User dapat menyimpan tempat yang ingin dikunjungi.

10. Contact form backend
    - Pesan masuk disimpan ke database dan bisa dilihat admin.

11. Artikel/blog wisata
    - Mendukung SEO dan konten perjalanan.

12. Multi-language Indonesia/English
    - Cocok untuk wisata Bali.

13. Promo management
    - Admin dapat membuat promo tiket/paket.

14. Notifikasi email/WhatsApp
    - Tahap awal bisa berupa template manual, lalu integrasi API.

## 11. Recommended Modern UI Direction

Warna:

- Base light: putih hangat atau off-white bersih.
- Base dark: navy-charcoal, bukan hitam pekat.
- Primary: tropical teal.
- Secondary: sea green.
- Accent: sunset coral/orange secukupnya untuk CTA/label promo.
- Hindari palette satu warna saja. Gunakan netral kuat agar premium.

Font:

- Heading: Plus Jakarta Sans.
- Body: Inter.
- Angka/data: tabular numbers via CSS.
- Hindari terlalu banyak font seperti Poppins + Inter + Plus Jakarta bersamaan.

Layout:

- Container max-width 1180-1280 px.
- Mobile-first.
- Section full-width dengan inner constrained content.
- Card radius 16-24 px untuk page marketing, 12-16 px untuk form/result.
- Hindari card di dalam card berlebihan.

Komponen:

- Navbar sticky glass ringan.
- Button primary teal gradient/subtle solid.
- Button secondary outline/ghost.
- Input dengan focus ring jelas.
- Empty state reusable.
- Result cards modern.
- Destination cards dengan image ratio konsisten.
- Stepper untuk visa/booking.
- Toast untuk feedback.

Animasi:

- Smooth reveal yang tidak mengganggu layout.
- Hover card `translateY(-2px sampai -4px)`.
- Focus/active state jelas.
- Respect `prefers-reduced-motion`.

Mobile responsive:

- Form satu kolom.
- CTA full-width.
- Navbar compact.
- Hero tidak terlalu tinggi.
- Text tidak menabrak gambar.
- Calendar custom tetap mudah disentuh.

Kesan visual:

- Premium tropical Bali.
- Clean, profesional, elegan.
- Lebih banyak gambar nyata berkualitas dan whitespace.
- Tidak terlalu dekoratif.

## 12. Upgrade Roadmap

### Phase 1 Stabilization

Tujuan:

- Membuat project stabil, aman untuk local development, dan tidak membocorkan file sensitif.

Task utama:

- Pindahkan config database dari hardcoded ke file config non-public.
- Hilangkan `@include_once` dan `@mysqli_query` di `index.php`.
- Tambahkan helper koneksi dan logging.
- Pindahkan SQL dump dan `_archive` ke luar public root untuk production.
- Tangani `hasil.transport.php`.
- Tambahkan `rel="noopener noreferrer"` pada semua link eksternal.
- Pastikan semua halaman memakai partial shared.

File/folder terlibat:

- `connection.php`
- `index.php`
- `hasil.*.php`
- `detail.php`
- `.htaccess`
- `README.md`
- `_archive`
- `bali.sql`

Risiko:

- Path include berubah.
- Local setup developer lain perlu update.
- Jika config salah, halaman DB gagal.

Output:

- Struktur dasar lebih aman.
- Error lebih jelas.
- Tidak ada file sensitif aktif di public root production.

### Phase 2 UI Refresh

Tujuan:

- Menyamakan seluruh halaman dengan style profesional modern minimalis.

Task utama:

- Redesign homepage.
- Redesign `destination.php`, `detail.php`, `about.php`, `visa.php`.
- Redesign halaman hasil menjadi result cards.
- Bersihkan inline CSS di `hasil.*.php`.
- Standarkan typography dan spacing.
- Optimasi dark/light mode.

File/folder terlibat:

- `index.php`
- `destination.php`
- `detail.php`
- `about.php`
- `visa.php`
- `hasil.*.php`
- `styles/`
- `partials/`

Risiko:

- UI berubah terlalu banyak dan merusak fungsi lama.
- CSS override saling tabrakan.

Output:

- UI konsisten, responsive, premium.
- CSS lebih mudah dirawat.

### Phase 3 Admin System

Tujuan:

- Membuat admin dapat mengelola konten tanpa edit database manual.

Task utama:

- Buat login/register admin.
- Buat session auth sederhana.
- Buat dashboard admin.
- CRUD destinasi.
- CRUD galeri destinasi.
- CRUD tiket/hotel/mobil/bus dasar.
- Validasi input dan upload image.

File/folder terlibat:

- `admin/`
- `auth/`
- `config/`
- `database/`
- `partials/`
- `uploads/` atau `storage/`

Risiko:

- Security auth lemah jika dibuat tergesa-gesa.
- Upload file rentan jika validasi buruk.

Output:

- Admin panel dasar.
- Konten bisa dikelola dari UI.

### Phase 4 Booking System

Tujuan:

- Mengubah project dari katalog/link eksternal menjadi sistem booking internal dasar.

Task utama:

- Buat tabel `bookings`, `booking_details`, `payments`.
- Form booking tiket wisata.
- Status booking: pending, confirmed, cancelled.
- Invoice HTML.
- Dashboard user untuk melihat booking.
- Admin approval/status.

File/folder terlibat:

- `booking/`
- `admin/bookings/`
- `database/`
- `partials/`
- `styles/`

Risiko:

- Data transaksi harus konsisten.
- Perlu validasi tanggal/jumlah/stok.
- Payment gateway butuh security ekstra.

Output:

- Booking internal MVP.
- Invoice dan status booking.

### Phase 5 Production Readiness

Tujuan:

- Menyiapkan project untuk deployment yang aman dan cepat.

Task utama:

- Setup `.env` atau config production.
- Matikan display errors.
- HTTPS/domain.
- Security headers.
- Cache-control static assets.
- Kompres gambar.
- Sitemap dan robots.txt.
- Backup database.
- Dokumentasi deploy.

File/folder terlibat:

- `.htaccess`
- `config/`
- `README.md`
- `robots.txt`
- `sitemap.xml`
- `styles/`
- `images/`

Risiko:

- Server production berbeda dari Laragon/XAMPP.
- `.htaccess` tidak berlaku di Nginx.

Output:

- Project siap publish dengan risiko lebih rendah.

## 13. Priority Checklist

- [ ] Critical: Tidak ada critical aktif saat audit ini, tetapi validasi ulang akses file SQL di server production wajib dilakukan.
- [ ] High: Pindahkan credential DB dari hardcoded local config.
- [ ] High: Pindahkan `bali.sql`, `_archive`, dan file SQL mentah ke luar public root saat production.
- [ ] High: Sanitasi output HTML dari database pada halaman detail.
- [ ] High: Hapus ketergantungan keamanan pada `.htaccess` saja.
- [ ] Medium: Buat helper koneksi, validasi, logging, dan escaping.
- [ ] Medium: Hilangkan error suppression `@` di `index.php`.
- [ ] Medium: Tambahkan `rel="noopener noreferrer"` pada link eksternal hasil DB.
- [ ] Medium: Buat backend contact form atau tulis jelas sebagai demo.
- [ ] Medium: Tambahkan CSRF jika mulai ada POST write action.
- [ ] Medium: Rapikan `hasil.transport.php`.
- [ ] Medium: Tambahkan index kombinasi database untuk query result.
- [ ] Low: Bersihkan placeholder link footer.
- [ ] Low: Kompres gambar besar dan gunakan WebP/AVIF.
- [ ] Low: Kurangi CSS duplikat dan inline CSS.
- [ ] Low: Rapikan naming kolom seperti `rooms` pada rental mobil.
- [ ] UI/UX: Redesign homepage dan halaman result secara konsisten.
- [ ] UI/UX: Modernisasi `about.php`, `visa.php`, `destination.php`, dan `detail.php`.
- [ ] Feature: Tambahkan login/register dan dashboard admin.
- [ ] Feature: Tambahkan CRUD destinasi.
- [ ] Feature: Tambahkan booking internal MVP.
- [ ] Deployment: Buat production checklist, sitemap, robots.txt, security headers, dan backup plan.

## 14. Detailed Follow-up Codex Prompts

### 1. Prompt Fix Critical Error

```text
Analisis dan perbaiki hanya error/risk paling kritikal pada project PHP native Bali Project.

Tujuan:
- Pastikan tidak ada SQL injection aktif.
- Pastikan file SQL/dump/arsip tidak dapat diakses publik.
- Pastikan file yang berisi SQL mentah tidak menjadi route publik.

Batasan:
- Jangan mengubah database.
- Jangan menghapus file tanpa izin.
- Jangan menjalankan migration, db wipe, git reset, git clean.
- Jangan redesign UI.

File yang perlu dianalisis:
- detail.php
- destination.php
- hasil.bus.php
- hasil.hotel.php
- hasil.pesawat.php
- hasil.mobil.php
- hasil.transport.php
- connection.php
- .htaccess
- bali.sql
- _archive/

Langkah kerja:
1. Cek semua input dari $_GET dan $_POST.
2. Cek semua query raw.
3. Cek apakah dump SQL dan hasil.transport.php terlindungi.
4. Jika perlu, tambahkan proteksi .htaccess yang aman.
5. Jika ada file route berisi SQL mentah, jangan hapus; jelaskan opsi archive.

Aturan keamanan:
- Jangan tampilkan credential penuh.
- Jangan bocorkan detail DB ke user.
- Gunakan prepared statement.
- Gunakan pesan error user-friendly.

Output yang harus dibuat:
- Patch minimal.
- Ringkasan file yang diubah.
- Daftar residual risk.

Perintah testing aman:
- php -l detail.php
- php -l hasil.bus.php
- php -l hasil.hotel.php
- php -l hasil.pesawat.php
- php -l hasil.mobil.php
- curl/Invoke-WebRequest untuk memastikan SQL file tidak bisa diakses jika server aktif.

Format laporan hasil:
- Perubahan
- Verifikasi
- Risiko tersisa
```

### 2. Prompt Refactor Struktur Project

```text
Refactor struktur dasar project PHP native Bali Project secara bertahap tanpa mengubah behavior.

Tujuan:
- Membuat struktur lebih profesional dan mudah dirawat.
- Memusatkan koneksi database, helper escaping, helper validasi, dan partial shared.

Batasan:
- Jangan mengubah database.
- Jangan menghapus file tanpa izin.
- Jangan mengubah UI besar.
- Pertahankan URL halaman lama.

File yang perlu dianalisis:
- connection.php
- partials/
- index.php
- destination.php
- detail.php
- hasil.*.php
- README.md
- .htaccess

Langkah kerja:
1. Buat rencana refactor kecil.
2. Buat helper config/koneksi yang tetap kompatibel.
3. Migrasikan satu halaman dulu sebagai contoh.
4. Jalankan php -l.
5. Update README jika path/config berubah.

Aturan keamanan:
- Jangan commit credential production.
- Jangan tampilkan error DB mentah ke user.

Output yang harus dibuat:
- Struktur folder baru.
- Patch kecil.
- Catatan backward compatibility.

Perintah testing aman:
- php -l semua file yang diubah
- HTTP smoke test halaman utama

Format laporan hasil:
- Struktur sebelum/sesudah
- File diubah
- Verifikasi
- Risiko tersisa
```

### 3. Prompt Upgrade UI/UX Homepage

```text
Upgrade UI/UX homepage Bali Project menjadi profesional, modern, minimalis, elegan, dan premium tropical.

Tujuan:
- Membuat homepage terlihat seperti website wisata premium.
- Tetap mempertahankan fungsi database destinasi.

Batasan:
- Jangan mengubah query database.
- Jangan menghapus konten utama.
- Jangan memakai framework baru.
- Gunakan CSS custom yang sudah ada.

File yang perlu dianalisis:
- index.php
- partials/head.php
- partials/navbar.php
- partials/footer.php
- styles/_tokens.css
- styles/_base.css
- styles/_components.css
- styles/page.home.css
- assets/js/app.js
- images/

Langkah kerja:
1. Audit layout homepage.
2. Buat section hero premium dengan image nyata.
3. Upgrade destination cards.
4. Tambahkan CTA jelas ke destination/tiket/transport.
5. Pastikan dark/light mode.
6. Cek desktop dan mobile.

Aturan keamanan:
- Escape output database dengan htmlspecialchars.
- Jangan menambah external script tidak perlu.

Output yang harus dibuat:
- UI homepage baru.
- CSS scoped.
- Screenshot/verifikasi visual.

Perintah testing aman:
- php -l index.php
- HTTP smoke test index.php
- Screenshot desktop dan mobile jika memungkinkan.

Format laporan hasil:
- Perubahan visual
- File diubah
- Verifikasi
- Catatan responsive
```

### 4. Prompt Membuat Sistem Login/Register

```text
Buat sistem login/register dasar untuk Bali Project PHP native.

Tujuan:
- User dapat register, login, logout.
- Password tersimpan dengan password_hash.
- Session aman untuk halaman user/admin.

Batasan:
- Jangan memakai framework.
- Jangan mengubah tabel lama tanpa rencana SQL jelas.
- Jangan menjalankan SQL ke database tanpa izin.
- Buat file SQL migration manual jika perlu.

File yang perlu dianalisis:
- connection.php atau config database
- partials/
- styles/
- README.md

Langkah kerja:
1. Rancang tabel users dan roles jika perlu.
2. Buat SQL migration manual.
3. Buat auth helper.
4. Buat register.php, login.php, logout.php.
5. Tambahkan validasi server-side.
6. Tambahkan CSRF token.
7. Tambahkan UI modern minimalis.

Aturan keamanan:
- Password wajib password_hash.
- Login wajib password_verify.
- Regenerate session id setelah login.
- Jangan tampilkan detail error database.

Output yang harus dibuat:
- File PHP auth.
- SQL migration manual.
- CSS jika diperlukan.
- Dokumentasi setup.

Perintah testing aman:
- php -l file auth
- Test register/login/logout manual di local.

Format laporan hasil:
- Tabel baru
- File baru
- Cara testing
- Risiko tersisa
```

### 5. Prompt Membuat Dashboard Admin

```text
Buat dashboard admin dasar untuk Bali Project.

Tujuan:
- Admin dapat melihat ringkasan destinasi, booking, contact message, dan data master.

Batasan:
- Harus memakai auth/session.
- Jangan membuat destructive action dulu.
- Jangan mengubah database tanpa SQL migration manual.

File yang perlu dianalisis:
- auth helper
- partials/
- styles/
- destination.php
- bali.sql

Langkah kerja:
1. Rancang folder admin/.
2. Buat middleware sederhana require_admin.
3. Buat admin/index.php dashboard.
4. Tambahkan cards statistik.
5. Tambahkan navigasi admin.
6. Tambahkan layout responsive.

Aturan keamanan:
- Cek session role admin.
- Escape semua output.
- Tidak boleh expose error SQL.

Output yang harus dibuat:
- admin/index.php
- admin partial/layout jika perlu
- CSS admin
- Catatan SQL yang diperlukan

Perintah testing aman:
- php -l admin/*.php
- HTTP smoke test halaman admin

Format laporan hasil:
- Fitur dashboard
- File dibuat
- Verifikasi
- Next step CRUD
```

### 6. Prompt Membuat CRUD Destinasi Wisata

```text
Buat CRUD destinasi wisata untuk admin Bali Project.

Tujuan:
- Admin dapat create, read, update, delete destinasi secara aman.

Batasan:
- Delete harus soft-delete atau konfirmasi dulu.
- Jangan menghapus data nyata tanpa izin.
- Upload gambar harus tervalidasi.

File yang perlu dianalisis:
- destination.php
- detail.php
- bali.sql
- admin/
- images/

Langkah kerja:
1. Audit tabel destination/detail/detail_image.
2. Rancang form admin.
3. Buat list destinasi admin.
4. Buat create/edit dengan validasi.
5. Buat upload image aman.
6. Buat delete/disable dengan konfirmasi.

Aturan keamanan:
- Validasi file extension dan MIME.
- Batasi ukuran upload.
- Simpan nama file aman.
- CSRF wajib untuk POST.

Output yang harus dibuat:
- admin/destinations/*.php
- SQL migration manual bila perlu
- CSS admin
- Dokumentasi penggunaan

Perintah testing aman:
- php -l admin/destinations/*.php
- Test create/edit tanpa menghapus data lama

Format laporan hasil:
- Fitur CRUD
- Validasi
- File diubah
- Verifikasi
```

### 7. Prompt Membuat Sistem Booking Tiket

```text
Buat sistem booking tiket internal dasar untuk Bali Project.

Tujuan:
- User dapat memilih tiket, tanggal, jumlah, mengisi data pemesan, dan mendapat status booking.

Batasan:
- Jangan integrasi payment gateway dulu.
- Jangan mengubah data lama tanpa migration manual.
- Status pembayaran manual dulu.

File yang perlu dianalisis:
- tiket.php
- tiket.bus.php
- booking.hotel.php
- sewa.mobil.php
- transport.php
- hasil.*.php
- bali.sql

Langkah kerja:
1. Rancang tabel bookings dan booking_details.
2. Buat SQL migration manual.
3. Buat form booking POST.
4. Buat validasi stok/jumlah.
5. Buat halaman confirmation.
6. Buat invoice HTML sederhana.

Aturan keamanan:
- CSRF wajib.
- Validasi semua input server-side.
- Escape output invoice.

Output yang harus dibuat:
- booking/*.php
- SQL migration manual
- Invoice view
- Dokumentasi flow

Perintah testing aman:
- php -l booking/*.php
- Test booking local dengan data dummy

Format laporan hasil:
- Flow booking
- Tabel baru
- File dibuat
- Verifikasi
```

### 8. Prompt Membuat Sistem Review dan Rating

```text
Buat sistem review dan rating destinasi untuk Bali Project.

Tujuan:
- User login dapat memberi rating dan review pada destinasi.
- Review tampil di detail destinasi.

Batasan:
- Harus butuh login.
- Jangan tampilkan review tanpa sanitasi.
- Jangan membuat moderasi kompleks dulu.

File yang perlu dianalisis:
- detail.php
- auth helper
- bali.sql
- styles/detail.css

Langkah kerja:
1. Rancang tabel reviews.
2. Buat SQL migration manual.
3. Tambahkan form review di detail destinasi.
4. Tambahkan validasi rating 1-5.
5. Tampilkan list review.
6. Tambahkan average rating.

Aturan keamanan:
- CSRF wajib.
- Review text harus di-escape.
- Batasi panjang review.

Output yang harus dibuat:
- SQL migration manual
- Update detail.php
- CSS review component

Perintah testing aman:
- php -l detail.php
- Test submit review local

Format laporan hasil:
- Tabel review
- UI review
- Verifikasi
- Risiko spam/moderasi
```

### 9. Prompt Optimasi Security

```text
Lakukan hardening security untuk Bali Project PHP native.

Tujuan:
- Mengurangi risiko SQL injection, XSS, CSRF, credential leak, dan file leak.

Batasan:
- Jangan mengubah database tanpa migration manual.
- Jangan menghapus file tanpa izin.
- Jangan merusak flow lama.

File yang perlu dianalisis:
- semua *.php
- .htaccess
- connection.php
- partials/
- bali.sql
- _archive/

Langkah kerja:
1. Audit input/output.
2. Tambahkan helper e().
3. Tambahkan CSRF untuk POST.
4. Tambahkan security headers.
5. Pindahkan config sensitif.
6. Tambahkan rel noopener pada external links.
7. Validasi akses file non-public.

Aturan keamanan:
- Jangan tampilkan secret.
- Jangan tampilkan detail DB ke user.
- Hindari perubahan besar tanpa test.

Output yang harus dibuat:
- Patch security.
- Checklist mitigasi.
- Residual risk.

Perintah testing aman:
- php -l semua file diubah
- HTTP smoke test halaman utama dan form

Format laporan hasil:
- Temuan
- Perbaikan
- Verifikasi
- Risiko tersisa
```

### 10. Prompt Optimasi Performance

```text
Optimasi performance Bali Project secara aman.

Tujuan:
- Mengurangi ukuran asset, mempercepat load, dan menyiapkan caching static.

Batasan:
- Jangan hapus gambar original tanpa izin.
- Jangan menambah build tool berat kecuali diminta.
- Jangan mengubah desain besar.

File yang perlu dianalisis:
- images/
- images/optimized/
- styles/
- assets/js/app.js
- .htaccess
- index.php
- destination.php
- detail.php

Langkah kerja:
1. Audit file gambar terbesar.
2. Pastikan halaman memakai image optimized bila tersedia.
3. Tambahkan lazy loading.
4. Tambahkan width/height/aspect-ratio.
5. Rekomendasikan cache-control.
6. Audit CSS duplikat.

Aturan keamanan:
- Jangan download asset eksternal sembarangan.
- Jangan menghapus original.

Output yang harus dibuat:
- Patch aman untuk image usage.
- Rekomendasi kompresi.
- Checklist performance.

Perintah testing aman:
- php -l file diubah
- HTTP smoke test
- Screenshot visual desktop/mobile jika memungkinkan

Format laporan hasil:
- Asset terbesar
- Perubahan
- Estimasi dampak
- Verifikasi
```

### 11. Prompt Persiapan Deployment Production

```text
Siapkan checklist dan patch ringan agar Bali Project siap deployment production.

Tujuan:
- Project aman dipublish di hosting Apache/Laragon-like atau shared hosting.

Batasan:
- Jangan deploy sungguhan.
- Jangan mengubah database production.
- Jangan hardcode credential production.

File yang perlu dianalisis:
- README.md
- .htaccess
- connection.php
- partials/head.php
- bali.sql
- _archive/
- images/

Langkah kerja:
1. Buat production checklist.
2. Tambahkan config example.
3. Tambahkan robots.txt dan sitemap draft jika diminta.
4. Tambahkan security/cache headers di .htaccess.
5. Dokumentasikan cara import DB.
6. Dokumentasikan file yang tidak boleh ikut public root.

Aturan keamanan:
- Tidak boleh commit secret.
- SQL dump jangan dipublish.
- Error display harus off di production.

Output yang harus dibuat:
- README update.
- config.example.php bila perlu.
- DEPLOYMENT.md bila diminta.

Perintah testing aman:
- php -l file diubah
- HTTP smoke test local

Format laporan hasil:
- Checklist deployment
- File diubah
- Cara rollback
- Risiko tersisa
```

### 12. Prompt Membuat Dokumentasi Project

```text
Buat dokumentasi lengkap untuk Bali Project.

Tujuan:
- Developer baru dapat menjalankan, memahami struktur, dan melanjutkan pengembangan.

Batasan:
- Jangan mengubah kode aplikasi kecuali dokumen.
- Jangan tampilkan credential asli.

File yang perlu dianalisis:
- README.md
- BALI_PROJECT_SYSTEM_REVIEW_AND_UPGRADE_REPORT.md
- bali.sql
- .htaccess
- semua file PHP utama

Langkah kerja:
1. Audit README saat ini.
2. Tulis cara install local.
3. Tulis struktur folder.
4. Tulis database setup.
5. Tulis cara menjalankan server PHP built-in.
6. Tulis route/page list.
7. Tulis checklist troubleshooting.
8. Tulis roadmap singkat.

Aturan keamanan:
- Sensor credential.
- Jelaskan bahwa SQL dump tidak boleh dipublish production.

Output yang harus dibuat:
- README.md update atau docs/PROJECT_GUIDE.md.
- Checklist setup.
- Troubleshooting.

Perintah testing aman:
- Tidak wajib, karena dokumen.
- Jika ada contoh command, pastikan command aman.

Format laporan hasil:
- Dokumen dibuat/diubah
- Bagian utama
- Catatan asumsi
```

## 15. Final Recommendation

Project ini layak dilanjutkan. Untuk ukuran PHP native prototype, pondasinya sudah cukup jelas: ada halaman destinasi, detail, transport, tiket, hotel, mobil, form contact, database dump, dan design system yang mulai terbentuk.

Prioritas pertama:

1. Stabilization dan security hardening:
   - Pindahkan credential DB dari hardcoded.
   - Pindahkan SQL dump dan `_archive` ke luar public root saat production.
   - Rapikan `hasil.transport.php`.
   - Hilangkan error suppression di `index.php`.
   - Tambahkan helper koneksi/error handling.

Upgrade paling berdampak:

- Redesign homepage dan halaman result menggunakan design system terbaru. Ini akan langsung meningkatkan kesan profesional, modern, minimalis, dan premium.
- Setelah itu, dashboard admin dan CRUD destinasi akan membuat project terasa seperti sistem nyata, bukan hanya halaman statis dengan SQL dump.

Risiko terbesar jika tidak diperbaiki:

- Deployment production yang salah dapat membocorkan dump SQL, file archive, atau konfigurasi database.
- Credential hardcoded dan struktur public root yang masih bercampur akan menyulitkan hosting yang aman.
- Tanpa admin/dashboard, data tetap bergantung pada edit SQL manual sehingga sulit dikembangkan.
- Tanpa sanitasi HTML dari database, fitur admin konten di masa depan dapat membuka risiko stored XSS.

Rekomendasi akhir:

- Lanjutkan project ini dengan roadmap bertahap.
- Jangan langsung migrasi ke framework jika targetnya masih pembelajaran PHP native.
- Jika targetnya production komersial, pertimbangkan migrasi terencana ke Laravel setelah Phase 1 dan Phase 2 selesai, karena auth, CSRF, validation, migration, dan admin akan jauh lebih aman dan rapi.
- Untuk langkah terdekat, jalankan Phase 1 Stabilization dulu sebelum menambah fitur besar.

Ringkasan terminal yang diminta:

```text
Teknologi terdeteksi:
- PHP Native
- MySQL/MariaDB
- HTML/CSS/JavaScript
- Custom CSS design system
- Tidak ada Laravel/CodeIgniter/Composer/npm build system

Jumlah temuan:
- Critical: 0
- High: 4
- Medium: 10
- Low: 8

Upgrade paling direkomendasikan:
- Phase 1 Stabilization: config database non-public, archive/dump di luar public root, helper koneksi/error handling, dan cleanup file SQL mentah.
- Phase 2 UI Refresh: redesign homepage dan halaman result agar konsisten dengan design system modern.

Lokasi file laporan:
C:\laragon\www\bali-project\BALI_PROJECT_SYSTEM_REVIEW_AND_UPGRADE_REPORT.md
```
