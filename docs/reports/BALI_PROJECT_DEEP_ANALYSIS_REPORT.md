# BALI PROJECT - Deep Analysis Report

Tanggal audit: 2026-06-08  
Lokasi audit awal: `C:\laragon\www\bali-project`  
Lokasi kode yang terdeteksi saat laporan dibuat sudah berubah. Root aplikasi aktif terbaru: `C:\laragon\www\bali-project`.

Catatan penting: root yang diberikan berisi folder `bali-project` lagi. Jadi aplikasi aktual berada satu level lebih dalam. Laporan ini menganalisis folder kode aktual tersebut, dan nesting ini dicatat sebagai masalah struktur/deployment.

## 1. Ringkasan Eksekutif

Project ini adalah aplikasi web pariwisata Bali berbasis PHP native dengan MySQL/MariaDB. Tidak ditemukan Laravel, CodeIgniter, Composer, Vite, React, Vue, atau dependency manager frontend/backend. Struktur aplikasi masih monolitik: file `.php` langsung berisi HTML, query database, style inline, dan logic tampilan.

Secara umum project bisa dijalankan di Laragon/XAMPP jika Apache/PHP dan database `bali` tersedia. Namun kesiapan production masih rendah karena ada beberapa risiko utama: SQL injection di `detail.php`, credential database hardcoded, file dump SQL berada di web root, file `hasil.transport.php` berisi SQL mentah, form dan halaman hasil belum memvalidasi input dengan aman, serta struktur folder masih menyimpan salinan lama `pariwisataweb` yang berisi duplikasi dan link rusak.

Audit aman yang sudah dilakukan:

- `php --version`: PHP 8.2.12 tersedia.
- `composer --version`: Composer 2.8.8 tersedia, tetapi project tidak memakai `composer.json`.
- `npm --version`: npm 11.12.1 tersedia, tetapi project tidak memakai `package.json`.
- `php -l` ke semua file PHP utama dan folder `pariwisataweb`: tidak ada syntax error PHP.
- Pencarian struktur, query, credential, link asset, dan SQL dump dilakukan secara read-only.

Perintah yang tidak dijalankan:

- Tidak menjalankan migration, seed, db import, `composer update`, `npm install`, `npm update`, `git reset`, atau perintah destruktif.

## 2. Teknologi yang Terdeteksi

| Teknologi | Status | Bukti |
|---|---|---|
| PHP native | Ya | Banyak file langsung di root kode: `index.php`, `destination.php`, `detail.php`, `hasil.hotel.php`, `transport.php`, dll. |
| MySQL/MariaDB | Ya | `connection.php`, `bali.sql`, `database/bali.sql`, query `mysqli` dan `new mysqli`. |
| HTML/CSS/JS biasa | Ya | Folder `styles`, `assets/js/app.js`, banyak HTML langsung di file PHP. |
| Laravel | Tidak | Tidak ada `artisan`, `composer.json`, folder `app`, `routes`, `config`, `resources`, `database/migrations`. |
| CodeIgniter | Tidak | Tidak ada `application`, `system`, `app/Config`, atau struktur CI. |
| React/Vue/Vite | Tidak | Tidak ada `package.json`, `vite.config.*`, `src`, `node_modules`, komponen React/Vue. |
| Dependency manager | Tidak dipakai | `composer.json` dan `package.json` tidak ditemukan. |

Arsitektur ringkas:

- Halaman publik langsung di file PHP root.
- Koneksi database terpusat sebagian di `connection.php`, tetapi beberapa file membuat koneksi sendiri.
- Database disediakan dalam dump SQL, bukan migration.
- UI lama dan UI baru bercampur: sebagian halaman masih memakai navbar/style manual, sementara `index.php` sudah memakai partial modern `partials/head.php`, `partials/navbar.php`, dan `partials/footer.php`.

## 3. Struktur Folder Project

Struktur penting yang ditemukan:

| Path | Fungsi | Catatan |
|---|---|---|
| `bali-project/` | Root kode aktual | Berada di dalam folder `C:\laragon\www\bali-project`, sehingga path deployment menjadi membingungkan. |
| `*.php` di root kode | Halaman aplikasi utama | PHP native dengan HTML dan query langsung. |
| `connection.php` | Koneksi database global | Hardcoded ke `localhost`, user `root`, database `bali`. |
| `bali.sql` | Dump database utama yang paling lengkap | Berisi tabel booking hotel, mobil, pesawat, bus, destination, dll. |
| `database/bali.sql` | Dump database lain/lebih lama | Tidak sinkron dengan `bali.sql`; nama tabel berbeda. |
| `pariwisataweb/` | Salinan lama aplikasi | Banyak duplikasi, link kosong, link `.html` yang tidak ada. |
| `images/` | Asset gambar utama | Banyak file besar, beberapa duplikat dengan folder lama. |
| `styles/` | CSS utama | Campuran CSS lama dan token/component CSS baru. |
| `partials/` | Head/navbar/footer modern | Dipakai oleh `index.php`, belum dipakai merata di halaman lain. |
| `assets/js/app.js` | JavaScript global modern | Dipakai footer partial; belum semua halaman memakai partial. |

Masalah struktur utama:

- Catatan historis: sebelumnya ada nesting folder aplikasi. Status terbaru: root aktif sudah dirapikan ke `C:\laragon\www\bali-project`.
- Ada folder `pariwisataweb` yang tampaknya salinan lama dan tidak konsisten dengan root kode baru.
- Ada tiga dump SQL bernama `bali.sql`: root luar, root kode, dan `database/bali.sql`.
- File SQL dan gambar besar berada di web root, berpotensi ikut tersaji publik jika Apache mengarah ke folder ini.
- Banyak CSS dan navbar duplikat antar halaman.

## 4. Temuan Critical

| No | Masalah | Lokasi File | Dampak | Rekomendasi |
|---|---|---|---|---|
| 1 | SQL injection langsung dari parameter URL `id` | `detail.php:5-9`, `pariwisataweb/detail.php:5-9` | Penyerang dapat memanipulasi query `WHERE id_des = $id`, membaca data lain, menyebabkan error SQL, atau melakukan serangan lanjutan tergantung konfigurasi DB. | Cast `id` ke integer atau gunakan prepared statement. Validasi `isset($_GET['id'])`, handle data tidak ditemukan, dan jangan render query gagal. |
| 2 | File dump database berada di web root | `bali.sql`, `database/bali.sql`, root luar `bali.sql` | Jika Apache melayani file tersebut, struktur database dan data sample bisa diunduh publik. Ini membuka informasi tabel, relasi, dan URL booking. | Pindahkan dump ke folder non-public atau blok via `.htaccess`. Untuk production, jangan deploy dump SQL. |
| 3 | File `.php` berisi SQL mentah, bukan kode PHP valid aplikasi | `hasil.transport.php` | Jika URL diakses, isi SQL dapat tampil sebagai plaintext. Ini membocorkan struktur tabel dan data sample, serta menunjukkan file salah tempat. | Pindahkan ke folder database/archive non-public atau ubah menjadi halaman PHP valid jika memang masih dibutuhkan. |

## 5. Temuan High

| No | Masalah | Lokasi File | Dampak | Rekomendasi |
|---|---|---|---|---|
| 1 | Credential database hardcoded dan memakai root tanpa password | `connection.php:3`, `hasil.bus.php:120-126`, `hasil.hotel.php:121-127`, `hasil.mobil.php:67-73`, `hasil.pesawat.php:120-126` | Sulit dipindahkan ke production, risiko akses database terlalu luas, dan konfigurasi bocor di repo. | Buat `config.php` atau `.env` sederhana di luar web root; gunakan user DB khusus dengan privilege minimum. |
| 2 | Prepared statement di `hasil.hotel.php` tidak selalu bind parameter | `hasil.hotel.php:166-170` | Jika `check_out` kosong atau `rooms` kosong, `$stmt->execute()` dipanggil tanpa binding yang sesuai. Ini bisa memicu runtime error atau hasil pencarian gagal. | Tambahkan branch bind untuk semua kombinasi: hanya destination+check_in, dengan check_out, dengan rooms, dan keduanya. |
| 3 | Input `$_GET` dipakai tanpa `isset` dan validasi di halaman hasil | `hasil.bus.php`, `hasil.hotel.php`, `hasil.mobil.php`, `hasil.pesawat.php` | Akses halaman hasil langsung akan menghasilkan undefined index warning. Input kosong bisa menghasilkan query tidak valid atau UX buruk. | Validasi semua parameter wajib sebelum query. Jika tidak lengkap, redirect balik ke form atau tampilkan pesan validasi aman. |
| 4 | Output HTML dari database dirender mentah | `detail.php:91` via `$data["desc"]` | Jika isi database berubah atau disisipi script, halaman rentan XSS tersimpan. Saat ini dump memang berisi HTML deskripsi, jadi risiko tergantung siapa yang bisa mengubah DB. | Simpan konten sebagai teks/Markdown yang disanitasi, atau whitelist HTML dengan purifier sebelum render. |
| 5 | Link internal menuju file `.html` yang tidak ada | `tiket.bus.php:130-132`, `hasil.mobil.php:49-51`, `pariwisataweb/index.php:19-21,36` | Navigasi menghasilkan 404 dan user kehilangan alur. | Ganti ke `destination.php`, `about.php`, `contact.php`, atau hapus folder lama jika tidak dipakai. |
| 6 | Query database tidak mengecek kegagalan koneksi/query sebelum fetch | `destination.php:5,41`, `detail.php:6-9`, `pariwisataweb/*` | Jika database belum diimport atau tabel tidak ada, halaman bisa warning/fatal. | Tambahkan pengecekan koneksi dan query result. Tampilkan fallback atau error page yang aman. |

## 6. Temuan Medium

| No | Masalah | Lokasi File | Dampak | Rekomendasi |
|---|---|---|---|---|
| 1 | JavaScript mengakses elemen yang tidak ada | `booking.hotel.php:198-207` | `returnSwitch` dan `checkOutDateContainer` tidak ada di HTML, sehingga `returnSwitch.addEventListener` dapat memicu error JS dan menghentikan script. | Hapus blok toggle yang tidak dipakai atau tambahkan guard `if (returnSwitch && checkOutDateContainer)`. |
| 2 | `contact.php` hanya simulasi submit di browser | `contact.php:38-78` | Form tidak mengirim data ke backend dan input tidak punya atribut `name`, sehingga tidak ada data yang diproses. | Tambahkan `name`, endpoint backend, validasi server-side, dan proteksi spam sederhana. |
| 3 | `index.php` memakai error suppression `@` untuk koneksi/query DB | `index.php:11-13` | Error nyata disembunyikan, sulit debugging local/deployment. | Ganti dengan pengecekan eksplisit dan log error non-public. |
| 4 | README tidak sinkron dengan kondisi project | `README.md:76-88` | README menyuruh buat DB `db_pariwisata_bali`, sedangkan kode memakai DB `bali`; README menyuruh buka `/pariwisataweb/`, padahal root kode baru ada di folder utama. | Update README setelah struktur final diputuskan. |
| 5 | Dump database root dan `database/bali.sql` tidak sinkron | `bali.sql`, `database/bali.sql` | Tabel yang dibutuhkan kode berbeda: kode memakai `bookings_hotel`, `routes_bus`, `bookings_mobil`, `bookings_pesawat`; dump lama memakai `bookings` dan `routes`. Salah import akan membuat halaman hasil error table not found. | Jadikan satu dump canonical, kemungkinan `bali.sql` root kode, lalu arsipkan dump lama di luar production. |
| 6 | Tidak ada environment template | Tidak ada `.env.example` atau `config.example.php` | Developer baru tidak tahu nama DB, user, host, dan langkah setup yang benar. | Tambahkan file contoh konfigurasi tanpa secret asli. |
| 7 | Tidak ada mekanisme logging aplikasi | Semua file PHP | Error production bisa tampil ke user atau hilang tanpa jejak. | Set `display_errors=Off` di production, aktifkan `error_log`, dan buat handler error sederhana. |
| 8 | Navbar/layout tidak konsisten | Hampir semua halaman | `index.php` memakai partial modern, halaman lain masih navbar manual dan inline CSS. | Migrasikan bertahap ke `partials/head.php`, `partials/navbar.php`, `partials/footer.php`. |

## 7. Temuan Low / Cleanup

| No | Masalah | Lokasi File | Dampak | Rekomendasi |
|---|---|---|---|---|
| 1 | Banyak file duplikat di `pariwisataweb` | `pariwisataweb/*` | Membingungkan maintenance dan bisa salah deploy. | Tentukan apakah folder ini legacy. Jika iya, pindahkan ke archive non-public atau hapus setelah backup dan review. |
| 2 | Gambar sangat besar | `images/gwk_1.jpeg` 12.7 MB, `images/tiket/hotel.jpeg` 12.3 MB, `images/pnd2.jpg` 5.4 MB | Loading lambat, bandwidth besar, UX mobile buruk. | Kompres gambar, buat ukuran responsive, gunakan WebP/AVIF untuk production. |
| 3 | Style inline berulang | `detail.php`, `hasil.*.php`, form booking | CSS sulit dirawat dan tidak konsisten. | Pindahkan style berulang ke file CSS shared. |
| 4 | Link placeholder | `partials/footer.php`, `transport.php` | Tombol sosial, careers, press, dan detail transport tidak berfungsi. | Isi URL nyata atau sembunyikan sampai tersedia. |
| 5 | `header.php` memakai `<div href=...>` bukan anchor | `header.php:11-17` | Tidak clickable dan semantic HTML salah. | Jika masih dipakai, ganti ke `<a href="...">`. Jika tidak dipakai, arsipkan. |
| 6 | Encoding teks tampak rusak | `README.md`, `partials/head.php`, `index.php` | Karakter seperti dash/bullet tampil sebagai mojibake di beberapa output. | Pastikan file disimpan UTF-8 dan perbaiki teks yang sudah rusak. |
| 7 | Tidak ada test otomatis | Seluruh project | Regression mudah lolos. | Untuk PHP native, mulai dari checklist smoke test manual atau script kecil untuk cek link/file. |

## 8. Analisis Route, Controller, View, dan Asset

Project ini tidak memiliki router formal. Routing dilakukan langsung oleh Apache/PHP berdasarkan nama file:

- `index.php`: halaman utama. Mengambil 3 destination dari database, fallback ke data statis jika gagal.
- `destination.php`: list destination dari tabel `destination`, link ke `detail.php?id=...`.
- `detail.php`: detail destinasi dari join `detail` dan `destination`, lalu gambar dari `detail_image`.
- `tiket.php`: halaman pilihan tiket bus/hotel/rental.
- `tiket.bus.php`: form pencarian bus, submit ke `hasil.bus.php`.
- `booking.hotel.php`: form pencarian hotel, submit ke `hasil.hotel.php`.
- `sewa.mobil.php`: form pencarian rental mobil, submit ke `hasil.mobil.php`.
- `transport.php`: form pencarian pesawat, submit ke `hasil.pesawat.php`.
- `visa.php`, `about.php`, `contact.php`: halaman informasi statis/semi-statis.
- `hasil.transport.php`: bukan route valid; isinya SQL mentah.

Potensi error route/view/asset:

- Tidak ada file `destination.html`, `about.html`, `contact.html`, tetapi beberapa halaman masih menautkan ke sana.
- Folder `pariwisataweb` punya banyak link kosong `href=""`.
- `contact.php` punya `action=""`, input tanpa `name`, dan hanya menampilkan sukses via JavaScript.
- `booking.hotel.php` punya JavaScript untuk elemen `return-switch` dan `check-out-date-container`, tetapi elemen itu tidak ada.
- `partials` baru hanya dipakai di `index.php`, sehingga desain tidak konsisten antar halaman.
- `assets/js/app.js` baru dipanggil via `partials/footer.php`; halaman yang tidak memakai footer partial tidak mendapat fitur JS global.

Hasil cek syntax:

- Semua file `.php` yang diperiksa lulus `php -l`.
- Catatan: `hasil.transport.php` tetap lulus karena tidak punya tag `<?php`; PHP memperlakukannya sebagai output teks biasa, bukan logic aplikasi.

## 9. Analisis Database

Koneksi database:

- `connection.php` memakai host `localhost`, user `root`, password kosong, database `bali`.
- Beberapa halaman hasil tidak memakai `connection.php`, melainkan membuat koneksi `new mysqli(...)` sendiri.

Tabel yang dibutuhkan kode berdasarkan query:

| Tabel | Dipakai oleh | Kolom penting |
|---|---|---|
| `destination` | `index.php`, `destination.php`, `detail.php` | `id_des`, `nama_des`, `gambar` |
| `detail` | `detail.php` | `id_detail`, `id_des`, `desc` |
| `detail_image` | `detail.php` | `id_img`, `id_detail`, `gambar` |
| `hotel` | `hasil.hotel.php` | `id_hotel`, `name` |
| `bookings_hotel` | `hasil.hotel.php` | `id_bookings`, `id_destinations`, `check_in_date`, `check_out_date`, `rooms`, `id_hotel`, `detail` |
| `car` | `hasil.mobil.php` | `id_car`, `vendor`, `name` |
| `bookings_mobil` | `hasil.mobil.php` | `id_bm`, `id_car`, `id_destinations`, `check_in_date`, `check_out_date`, `rooms`, `detail` |
| `buses` | `hasil.bus.php` | `buses_id`, `operator` |
| `routes_bus` | `hasil.bus.php` | `routes_id`, `from_id`, `to_id`, `departure_date`, `return_date`, `jumlah_kursi`, `buses_id`, `detail` |
| `pesawat` | `hasil.pesawat.php` | `id_pesawat`, `nama_pesawat` |
| `bookings_pesawat` | `hasil.pesawat.php` | `id_bp`, `from_id`, `to_id`, `id_pesawat`, `departure_date`, `return_date`, `jumlah_kursi`, `detail` |
| `from_city` | relational schema | `from_id`, `city` |
| `to_city` | relational schema | `to_id`, `city` |
| `destinations` | relational schema hotel/mobil | `id_destinations`, `destinations` |

Mismatch penting:

- `bali.sql` root kode berisi tabel yang cocok dengan halaman hasil: `bookings_hotel`, `bookings_mobil`, `bookings_pesawat`, `routes_bus`, `pesawat`.
- `database/bali.sql` memakai nama lama `bookings` dan `routes`, sehingga jika file ini yang diimport, `hasil.hotel.php`, `hasil.bus.php`, `hasil.pesawat.php`, dan `hasil.mobil.php` berpotensi error `table not found`.
- README menyebut database `db_pariwisata_bali`, tetapi kode meminta database `bali`.
- `hasil.transport.php` mencoba membuat tabel `pesawat` dengan kolom `name`, sementara kode `hasil.pesawat.php` membaca `pesawat.nama_pesawat`. Ini mismatch skema.

Risiko query:

- `detail.php` memakai query raw dengan `id` dari URL.
- Halaman hasil sudah memakai prepared statement, tetapi validasi input dan binding belum lengkap.
- Tidak ada pengecekan `$stmt === false` setelah `prepare`.
- Tidak ada indeks eksplisit selain primary/foreign key pada kombinasi filter tanggal dan rute. Query pencarian bisa melambat jika data membesar.

## 10. Analisis Keamanan

Risiko Critical:

- SQL injection di `detail.php` dan salinan `pariwisataweb/detail.php`.
- Dump SQL dapat terbuka dari web root.
- `hasil.transport.php` membocorkan SQL jika diakses.

Risiko High:

- Credential DB hardcoded, user `root`, password kosong.
- Output HTML dari DB dirender mentah di halaman detail.
- Input GET belum divalidasi server-side secara konsisten.
- Error koneksi memakai `die("Connection failed: ...")`, yang dapat membocorkan detail runtime.

Risiko Medium:

- Tidak ada CSRF token, walaupun sebagian besar form memakai GET dan belum melakukan perubahan data.
- Contact form tidak punya backend; jika nanti ditambahkan, perlu validasi, rate limit, sanitasi, dan CSRF.
- Link eksternal dari DB dibuka `target="_blank"` tanpa `rel="noopener noreferrer"`.
- Tidak ada pembatasan direct access untuk file non-halaman seperti SQL dump.

Rekomendasi keamanan bertahap:

1. Fix SQL injection `detail.php` dulu.
2. Pindahkan/blokir semua dump SQL dari web root.
3. Pindahkan credential ke config non-public atau `.env` sederhana.
4. Tambahkan validasi input dan safe error handling di semua halaman hasil.
5. Sanitasi output HTML dari DB atau ubah model konten menjadi teks aman.
6. Matikan `display_errors` di production dan gunakan log.

## 11. Analisis UI/UX dan Frontend

Kondisi UI:

- `index.php` sudah jauh lebih modern: memakai partial, design tokens, responsive navbar, dark/light theme, reveal animation, toast, dan layout yang lebih rapi.
- Halaman lain masih memakai struktur lama, inline CSS, navbar berbeda, dan style yang tidak konsisten.
- Banyak halaman form memakai gambar besar sebagai background/hero, tetapi gambar belum dioptimasi.
- Beberapa halaman hasil memakai tabel sederhana yang kemungkinan tidak nyaman di mobile.

Masalah UI/UX:

- Navigasi rusak di beberapa halaman karena link `.html` tidak tersedia.
- Contact form menunjukkan sukses palsu tanpa benar-benar mengirim pesan.
- Booking hotel punya script error karena elemen toggle tidak ada.
- Banyak label/form tidak memiliki validasi required HTML yang konsisten.
- Result page tidak memberi instruksi jika parameter kosong atau data tidak ditemukan karena input tidak valid.
- Bahasa campur Indonesia/Inggris di UI: `View Details`, `Kembali`, `Find Flights`, `Talk to Us`.

Rekomendasi modernisasi aman:

- Jadikan `partials/head.php`, `partials/navbar.php`, dan `partials/footer.php` sebagai layout shared untuk semua halaman.
- Buat CSS page-level, bukan inline style.
- Perbaiki semua link internal ke `.php`.
- Ubah tabel result menjadi card/table responsive di mobile.
- Tambahkan pesan validasi yang jelas di form.
- Optimasi gambar besar dan tambahkan lazy loading di halaman non-index.

## 12. Analisis Performa

Bottleneck utama:

- Gambar besar: beberapa asset di atas 10 MB akan sangat lambat di mobile.
- Query belum punya indeks pencarian komposit untuk filter rute/tanggal.
- CSS banyak dan berulang; sebagian halaman memuat style inline besar.
- Tidak ada cache header/static asset strategy.
- Google Fonts dan CDN eksternal dipakai tanpa fallback deployment offline yang jelas.

Saran optimasi:

- Kompres `images/gwk_1.jpeg`, `images/tiket/hotel.jpeg`, `images/pnd2.jpg`, `images/pnd1.jpg`, dan asset besar lain.
- Buat versi WebP/AVIF dan gunakan ukuran gambar sesuai konteks.
- Tambahkan indeks DB untuk:
  - `bookings_hotel(id_destinations, check_in_date, check_out_date, rooms)`
  - `bookings_mobil(id_destinations, check_in_date, check_out_date, rooms)`
  - `bookings_pesawat(from_id, to_id, departure_date, return_date)`
  - `routes_bus(from_id, to_id, departure_date, return_date)`
- Satukan navbar/style agar browser cache lebih efektif.
- Untuk production, aktifkan gzip/brotli di web server.

## 13. Checklist Perbaikan Prioritas

- [x] Critical dulu: Ubah `detail.php` dan `pariwisataweb/detail.php` agar memakai prepared statement atau integer cast aman. Selesai: kedua file sudah validasi `id`, memakai prepared statement, dan menangani kondisi kosong/invalid/query gagal/data tidak ditemukan.
- [x] Critical dulu: Pindahkan/blokir `bali.sql`, `database/bali.sql`, dan root luar `bali.sql` dari public web root. Selesai: dump lama `database/bali.sql` sudah diarsipkan ke `_archive/database/bali.legacy.sql`; dump canonical `bali.sql` tetap ada untuk setup lokal dan akses langsung ke file `.sql`, `.sqlite`, `.sqlite3`, dan `.db` sudah diblokir melalui `.htaccess`.
- [x] Critical dulu: Tangani `hasil.transport.php` agar tidak menyajikan SQL mentah sebagai halaman publik. Selesai: akses langsung ke `hasil.transport.php` sudah diblokir melalui `.htaccess`.
- [ ] High: Pindahkan credential database dari kode ke config non-public, dan gunakan user DB non-root.
- [x] High: Fix binding parameter di `hasil.hotel.php` untuk semua kombinasi input. Selesai: binding parameter sudah lengkap untuk `check_out` opsional dan jumlah kamar.
- [x] High: Tambahkan validasi `isset`, tipe data, required field, dan error handling di semua `hasil.*.php`. Selesai untuk `hasil.hotel.php`, `hasil.mobil.php`, `hasil.bus.php`, dan `hasil.pesawat.php`; error koneksi database dicatat ke `error_log` tanpa menampilkan detail teknis ke user.
- [x] High: Perbaiki semua link `.html` yang tidak ada menjadi `.php`. Selesai: pemeriksaan statis untuk `destination.html`, `about.html`, dan `contact.html` sudah kosong.
- [x] High: Tambahkan pengecekan query/koneksi di `destination.php` dan `detail.php`. Selesai: `detail.php` dan `destination.php` sudah memiliki validasi/error handling eksplisit, pesan user-friendly, dan logging internal untuk query gagal.
- [x] Medium: Tentukan dump SQL canonical; kemungkinan `bali.sql` root kode, lalu arsipkan `database/bali.sql`. Selesai: `bali.sql` root kode ditetapkan sebagai canonical di README; dump lama dipindahkan ke `_archive/database/bali.legacy.sql`; `database/README.md` ditambahkan sebagai catatan.
- [x] Medium: Update README agar database name, path URL, dan langkah install sesuai kondisi real. Selesai: README diperbarui untuk PHP native, database `bali`, URL Laragon/XAMPP, server PHP lokal, dan status maintenance.
- [x] Medium: Perbaiki JavaScript error di `booking.hotel.php`. Selesai: listener untuk elemen yang tidak ada (`return-switch` dan `check-out-date-container`) sudah dihapus.
- [x] Medium: Buat contact form yang benar atau jelaskan bahwa form hanya demo. Selesai: form diberi `name` field dan catatan eksplisit bahwa form masih demo front-end; pesan sukses juga tidak lagi menyatakan pesan benar-benar terkirim.
- [x] Medium: Migrasikan halaman lama ke shared partial layout secara bertahap. Selesai untuk halaman target: `destination.php`, `about.php`, `contact.php`, `visa.php`, `tiket.php`, `transport.php`, `booking.hotel.php`, `tiket.bus.php`, dan `sewa.mobil.php` sudah memakai `partials/head.php`, `partials/navbar.php`, dan `partials/footer.php`.
- [x] Low: Kompres gambar besar dan siapkan format modern. Selesai: varian WebP dibuat di `images/optimized/` untuk gambar terbesar, dan beberapa referensi gambar berat di halaman booking/menu sudah diarahkan ke WebP.
- [x] Low: Bersihkan/arsipkan folder `pariwisataweb` jika sudah tidak dipakai. Selesai: folder legacy dipindahkan ke `_archive/pariwisataweb-legacy` dan archive dilindungi `.htaccess`.
- [x] Low: Kurangi inline CSS dan duplikasi navbar. Selesai bertahap: halaman aktif utama, booking, hasil pencarian, dan detail sudah memakai shared partial navbar/footer; `header.php` legacy dipindahkan ke `_archive/header-legacy.php`. Residual: beberapa inline style halaman booking/result masih dipertahankan untuk menjaga visual parity dan bisa diekstrak ke CSS pada pass cleanup berikutnya.

## 14. Rekomendasi Prompt Lanjutan untuk Fix

Prompt 1 - Fix keamanan Critical tanpa ubah struktur besar:

```text
Perbaiki hanya temuan Critical di BALI_PROJECT_DEEP_ANALYSIS_REPORT.md untuk project PHP native ini.
Fokus:
1. Amankan detail.php dari SQL injection dengan validasi id dan prepared statement.
2. Tangani kondisi id kosong, id invalid, query gagal, dan data tidak ditemukan.
3. Jangan mengubah UI besar.
4. Jangan hapus file SQL dulu; cukup tambahkan proteksi .htaccess jika aman dan jelaskan efeknya.
5. Jangan menjalankan migration atau mengubah database.
Setelah edit, jalankan php -l pada file yang diubah dan beri ringkasan perubahan.
```

Prompt 2 - Fix halaman hasil booking:

```text
Lanjutkan dari laporan BALI_PROJECT_DEEP_ANALYSIS_REPORT.md.
Perbaiki validasi dan binding parameter di hasil.hotel.php, hasil.mobil.php, hasil.bus.php, dan hasil.pesawat.php.
Aturan:
- Jangan ubah database.
- Gunakan prepared statement untuk semua query.
- Validasi semua $_GET dengan pesan user-friendly.
- Jangan tampilkan detail koneksi database ke user.
- Jalankan php -l pada file yang diubah.
```

Prompt 3 - Rapikan struktur dan link:

```text
Perbaiki link internal rusak yang ditemukan di BALI_PROJECT_DEEP_ANALYSIS_REPORT.md.
Fokus:
- Ganti destination.html, about.html, contact.html menjadi file .php yang tersedia.
- Jangan hapus folder pariwisataweb dulu.
- Jangan ubah desain besar.
- Jalankan pemeriksaan statis untuk memastikan tidak ada link internal .html yang hilang.
```

Prompt 4 - Modernisasi UI bertahap:

```text
Modernisasi UI project PHP native ini secara bertahap.
Gunakan partials/head.php, partials/navbar.php, dan partials/footer.php pada halaman destination, about, contact, visa, tiket, transport, dan halaman booking.
Jangan ubah query database dulu.
Pertahankan fungsi lama.
Setelah edit, cek php -l dan buka halaman utama secara lokal bila memungkinkan.
```

Prompt 5 - Deployment readiness:

```text
Buat project ini siap dijalankan di Laragon/XAMPP dan production sederhana.
Fokus:
- Buat config.example.php atau .env.example tanpa secret asli.
- Update README sesuai database "bali" dan folder aktual.
- Tambahkan checklist import database dan URL lokal yang benar.
- Jangan mengubah database dan jangan menjalankan migration.
```
