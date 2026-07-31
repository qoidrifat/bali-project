# BALI PROJECT  Cleanup, Refactor & Auto-Fix Report

Tanggal: 2026-06-09  
Root kerja instruksi: `C:\laragon\www\bali-project`  
Root kode aplikasi aktual: `C:\laragon\www\bali-project`

## 0. Execution Update - 2026-06-09

Pembaruan ini menandai status terbaru setelah laporan cleanup dijalankan ulang dari root aktif `C:\laragon\www\bali-project`.

Perubahan status terbaru:

- Folder nested `bali-project/` lama terdeteksi kosong dan tidak menjadi root aplikasi aktif.
- Dump canonical `bali.sql` sudah dipindahkan dari root publik aktif ke `storage/private/database/bali.sql`.
- Folder legacy `_archive/` sudah dipindahkan ke `storage/private/archive/`.
- File legacy `hasil.transport.php` sudah dipindahkan ke `storage/private/quarantine/root/hasil.transport.php`.
- Duplikat `garuda.png` sudah dipindahkan ke `storage/private/quarantine/root/garuda.png`.
- CSS legacy `styles/header.css` sudah dipindahkan ke `storage/private/quarantine/styles/header.css`.
- `storage/` dan `_project_cleanup_quarantine/` diblokir dari akses publik melalui `.htaccess`.
- Manifest cleanup terbaru berada di `storage/private/CLEANUP_MANIFEST.md`.
- Dokumentasi aktif (`README.md`, `DEPLOYMENT.md`, `docs/PROJECT_GUIDE.md`, `database/README.md`) sudah disinkronkan dengan struktur terbaru.

Catatan penting: `storage/private` masih berada di dalam folder project lokal agar mudah direstore dan diaudit. Untuk production, lokasi paling aman tetap di luar public root jika hosting mendukung struktur tersebut.

## 0.1 Structure Cleanup Update - 2026-06-09

Pembaruan struktur terbaru:

- Laporan audit besar dipindahkan dari root ke `docs/reports/`.
- Screenshot dokumentasi `bali-project.png` dipindahkan dari root ke `docs/images/bali-project.png`.
- Link screenshot dan daftar dokumen di `README.md` diperbarui.
- `docs/PROJECT_GUIDE.md` diperbarui agar mencerminkan `docs/images/` dan `docs/reports/`.
- `.gitignore` diperkuat untuk mengecualikan `storage/private/`, quarantine lama, log, `.env.*`, dan metadata OS.
- CSS tidak terpakai `styles/page.destination.css` dipindahkan ke `storage/private/quarantine/styles/page.destination.css`.
- Mojibake kecil di `partials/head.php` diperbaiki menjadi ASCII agar title dan meta description bersih.

Tujuan perubahan ini adalah membuat root project lebih clean tanpa mengubah URL route PHP lama.

Validasi setelah pembaruan struktur:

- `php -l` lulus untuk 43 file PHP aktif.
- Static CSS check lulus: tidak ada referensi `styles/*.css` yang hilang di PHP aktif.
- Static stale path check lulus: tidak ada referensi aktif ke root nested lama, URL nested lama, atau `../bali.sql`.
- HTTP smoke test lulus: `index.php`, `destination.php`, `detail.php?id=1`, `tiket.php`, `transport.php`, `login.php`, dan `register.php` semuanya HTTP 200.
- File leak test lulus: `storage/`, `storage/private/database/bali.sql`, `bali.sql`, `database/`, `config/database.php`, `includes/helpers.php`, dan `partials/head.php` semuanya HTTP 403.

## 1. Executive Summary

Project terdeteksi sebagai PHP native + MySQL/MariaDB dengan HTML, CSS, dan JavaScript custom. Tidak ditemukan struktur Laravel, CodeIgniter, Composer, npm, React, Vue, atau Vite.

Kondisi awal saat cleanup:

- Root aktif sudah berada langsung di `C:\laragon\www\bali-project`.
- Folder nested `bali-project` lama kosong dan bukan root aplikasi aktif.
- Worktree sudah berisi banyak perubahan dari pekerjaan sebelumnya, sehingga cleanup dilakukan konservatif.
- Banyak fitur sudah berjalan: shared partial, auth dasar, dashboard admin, CRUD destinasi, booking internal, review, security header, dan optimized images.
- Ada beberapa file legacy/duplikat yang aman untuk tidak menjadi bagian aktif project.

Cleanup yang dilakukan:

- Tidak ada file yang dihapus permanen.
- 5 item dipindahkan ke `storage/private` agar tidak menjadi route aktif: dump SQL, archive legacy, `hasil.transport.php`, duplikat `garuda.png`, dan CSS legacy `styles/header.css`.
- `storage/.htaccess` dan `storage/private/.htaccess` ditambahkan sebagai proteksi tambahan.
- `.htaccess` root aplikasi diperbarui untuk memblokir `storage` dan `_project_cleanup_quarantine`.
- Dokumentasi `README.md`, `docs/PROJECT_GUIDE.md`, `DEPLOYMENT.md`, dan `database/README.md` disinkronkan dengan struktur terbaru.

Error yang berhasil diperbaiki:

- Risiko route/file legacy `hasil.transport.php` yang berisi SQL mentah dihilangkan dari root publik aktif dengan private quarantine dan proteksi `.htaccess`.
- Duplikat root `garuda.png` dipindahkan agar asset aktif lebih bersih.
- CSS legacy tidak terpakai `styles/header.css` dipindahkan agar struktur CSS lebih clean.

Error tersisa:

- Tidak ditemukan syntax error PHP pada file aktif.
- Tidak ditemukan file leak pada target sensitif yang diuji.
- Masih ada risiko residual: `storage/private` masih berada di dalam folder project lokal, walaupun sudah diblokir `.htaccess`. Untuk production, pindahkan ke luar public root bila hosting mendukung.

Rekomendasi lanjut:

1. Pindahkan `storage/private` ke luar public root saat deployment production bila hosting mendukung.
2. Lanjutkan refactor bertahap agar query dan helper lebih konsisten.
3. Rapikan CSS lama dan kurangi duplikasi import.
4. Lakukan audit UI responsive dan security ulang sebelum publish.

## 2. Technology Stack Detected

| Teknologi | Status | Bukti |
|---|---|---|
| PHP Native | Terdeteksi | `index.php`, `destination.php`, `detail.php`, `hasil.*.php`, `admin/`, `booking/` |
| MySQL/MariaDB | Terdeteksi | `bali.sql`, `connection.php`, `includes/database.php`, query `mysqli` |
| HTML/CSS/JS custom | Terdeteksi | `styles/`, `assets/js/app.js`, markup langsung di file PHP |
| Apache/Laragon-like | Terdeteksi | `.htaccess`, struktur local path Laragon |
| Laravel | Tidak terdeteksi | Tidak ada `artisan`, `app/`, `routes/`, `resources/` Laravel |
| CodeIgniter | Tidak terdeteksi | Tidak ada struktur `application/`, `system/`, atau `app/Config` |
| Composer | Tidak dipakai | `composer.json` tidak ada |
| npm/Vite/React/Vue | Tidak dipakai | `package.json`, `vite.config.*`, dan source frontend framework tidak ada |

Command aman:

| Command | Hasil |
|---|---|
| `php --version` | PHP 8.2.12 |
| `composer --version` | Composer 2.8.8 tersedia di mesin, project tidak memakai Composer |
| `npm --version` | npm 11.12.1 tersedia di mesin, project tidak memakai npm |
| Cek file penting | `database/`, `config/`, dan `index.php` ada; `composer.json`, `package.json`, `artisan`, `routes/`, `app/`, `resources/`, `public/`, `.env.example` tidak ada |

## 3. Initial Project Structure

Root project aktif:

```text
C:\laragon\www\bali-project
|-- admin\
|-- assets\
|-- booking\
|-- config\
|-- database\
|-- docs\
|-- images\
|-- includes\
|-- partials\
|-- storage\
|-- styles\
|-- index.php
|-- destination.php
|-- detail.php
|-- .htaccess
|-- README.md
|-- DEPLOYMENT.md
|-- BALI_PROJECT_DEEP_ANALYSIS_REPORT.md
|-- BALI_PROJECT_SYSTEM_REVIEW_AND_UPGRADE_REPORT.md
```

Root kode aplikasi sebelum cleanup awal sempat dicatat sebagai subfolder lama. Status terbaru: root aktif adalah folder yang sama dengan root project di atas.

```text
C:\laragon\www\bali-project
|-- .git/
|-- admin/
|-- assets/
|-- booking/
|-- config/
|-- database/
|-- docs/
|-- images/
|-- includes/
|-- partials/
|-- styles/
|-- storage/private/
|-- .htaccess
|-- README.md
|-- DEPLOYMENT.md
|-- config.example.php
|-- index.php
|-- destination.php
|-- detail.php
|-- hasil.*.php
```

File/folder besar yang dicatat:

| File | Ukuran | Catatan |
|---|---:|---|
| `images/gwk_1.jpeg` | 12.13 MB | Original besar; optimized WebP tersedia |
| `images/tiket/hotel.jpeg` | 11.77 MB | Original besar; optimized WebP tersedia |
| `images/pnd2.jpg` | 5.16 MB | Original besar; optimized WebP tersedia |
| `images/pnd1.jpg` | 3.63 MB | Original besar; optimized WebP tersedia |
| `images/gwk.jpeg` | 3.35 MB | Original besar; optimized WebP tersedia |
| `bali-project.png` | 2.33 MB | Screenshot README, dipertahankan |
| `storage/private/archive/pariwisataweb-legacy/images/*` | bervariasi | Arsip legacy, dipindahkan ke private storage dan diblokir |

## 4. Cleanup Analysis

| No | File/Folder | Status | Alasan | Tindakan |
|---:|---|---|---|---|
| 1 | `hasil.transport.php` | Moved to Private Quarantine | File `.php` berisi SQL mentah, bukan route aplikasi aktif; sudah diblokir `.htaccess`; tidak ada referensi aktif selain dokumentasi/proteksi | Dipindahkan ke `storage/private/quarantine/root/hasil.transport.php` |
| 2 | `garuda.png` | Moved to Private Quarantine | Duplikat identik dari `images/garuda.png`; hash sama; asset aktif memakai folder `images/` | Dipindahkan ke `storage/private/quarantine/root/garuda.png` |
| 3 | `styles/header.css` | Moved to Private Quarantine | CSS navbar legacy tidak ditemukan direferensikan oleh PHP/CSS/JS aktif | Dipindahkan ke `storage/private/quarantine/styles/header.css` |
| 4 | `bali.sql` | Moved to Private Storage | Dump canonical untuk local setup; tidak perlu menjadi file root publik | Dipindahkan ke `storage/private/database/bali.sql` |
| 5 | `_archive/` | Moved to Private Storage | Arsip legacy masih berguna sebagai referensi, tetapi tidak perlu menjadi folder publik | Dipindahkan ke `storage/private/archive/` |
| 6 | `images/*` original besar | Kept | Original/fallback asset; beberapa nilai gambar berasal dari database | Dipertahankan |
| 7 | `database/*.sql` | Kept | SQL migration manual fitur auth/admin/booking/review | Dipertahankan |
| 8 | `docs/`, `DEPLOYMENT.md`, `README.md` | Kept | Dokumentasi aktif project | Dipertahankan |
| 9 | `pariwisataweb` lama di status git | Kept/Manual Review Needed | Status git menunjukkan deleted dari pekerjaan sebelumnya; folder aktif sudah tidak ada di working tree, arsipnya ada di `storage/private/archive` | Tidak disentuh |

## 5. Deleted Files

Tidak ada file yang cukup aman untuk dihapus permanen.

## 6. Quarantined Files

| No | File | Lokasi Asli | Lokasi Baru | Alasan | Cara Restore |
|---:|---|---|---|---|---|
| 1 | `hasil.transport.php` | `hasil.transport.php` | `storage/private/quarantine/root/hasil.transport.php` | Legacy SQL mentah dengan ekstensi PHP, bukan route aktif | Pindahkan kembali ke `hasil.transport.php` |
| 2 | `garuda.png` | `garuda.png` | `storage/private/quarantine/root/garuda.png` | Duplikat identik dari `images/garuda.png` | Pindahkan kembali ke `garuda.png` |
| 3 | `header.css` | `styles/header.css` | `storage/private/quarantine/styles/header.css` | CSS header legacy tidak dipanggil | Pindahkan kembali ke `styles/header.css` |
| 4 | `bali.sql` | `bali.sql` | `storage/private/database/bali.sql` | Dump canonical tidak perlu berada di root publik aktif | Pindahkan kembali ke `bali.sql` hanya untuk restore lokal sementara |
| 5 | `_archive/` | `_archive/` | `storage/private/archive/` | Arsip legacy tidak perlu berada di root publik aktif | Pindahkan kembali ke `_archive/` hanya untuk restore lokal sementara |

Manifest quarantine:

```text
storage/private/CLEANUP_MANIFEST.md
```

Proteksi quarantine:

```text
storage/private/.htaccess
```

## 7. Structure Refactor

Perubahan struktur folder:

```text
bali-project/
|-- storage/
|   |-- .htaccess
|   |-- private/
|       |-- .htaccess
|       |-- CLEANUP_MANIFEST.md
|       |-- database/
|       |   |-- bali.sql
|       |-- archive/
|       |-- quarantine/
|           |-- root/
|           |   |-- garuda.png
|           |   |-- hasil.transport.php
|           |-- styles/
|               |-- header.css
```

Referensi yang diperbarui:

- `.htaccess` root aplikasi menambahkan rule:

```apache
RewriteRule ^storage(?:/|$) - [F,L]
RewriteRule ^_project_cleanup_quarantine(?:/|$) - [F,L]
```

- `README.md`, `docs/PROJECT_GUIDE.md`, `DEPLOYMENT.md`, dan `database/README.md` disinkronkan agar menyebut dump SQL, archive, dan file legacy berada di `storage/private`.

Alasan perubahan:

- Mengurangi file aktif di root aplikasi.
- Mengurangi risiko dump SQL dan archive legacy menjadi route publik aktif.
- Memisahkan file legacy/duplikat dari struktur utama.
- Tetap menjaga kemampuan restore.
- Menjaga agar quarantine tidak menjadi route publik.

Dampak terhadap project:

- Halaman utama dan fitur publik tetap perlu lolos smoke test setelah perubahan ini.
- File legacy, dump SQL, dan archive kini tidak lagi berada di root aktif.
- Tidak ada referensi aktif yang rusak dari pemindahan ini.

## 8. Auto-Fix Error

| No | Error | Lokasi | Penyebab | Fix yang Dilakukan | Status |
|---:|---|---|---|---|---|
| 1 | File SQL mentah memakai ekstensi `.php` dan berada di root publik | `hasil.transport.php` | File legacy/eksperimen lama berisi SQL, bukan halaman aplikasi | Dipindahkan ke quarantine dan rule `.htaccess` tetap dipertahankan | Fixed |
| 2 | Duplikat asset root | `garuda.png` | File identik dengan `images/garuda.png`, root asset tidak direferensikan aktif | Dipindahkan ke quarantine | Fixed |
| 3 | CSS legacy tidak dipanggil | `styles/header.css` | Navbar lama sudah digantikan partial/shared style | Dipindahkan ke quarantine | Fixed |
| 4 | Folder private/quarantine berpotensi public jika dibuat tanpa proteksi | `storage/`, `storage/private/` | Folder private masih berada di dalam folder project lokal | Ditambahkan `.htaccess` folder dan rule root `.htaccess` untuk memblokir `storage` | Fixed |
| 5 | Dokumentasi menyebut legacy route lama | `README.md`, `docs/PROJECT_GUIDE.md`, `DEPLOYMENT.md` | Status file berubah menjadi quarantine | Dokumentasi disinkronkan | Fixed |
| 6 | Error suppression pada query homepage | `index.php` | `@include_once` dan `@mysqli_query` menyembunyikan masalah koneksi/query | Diganti `require_once`, query normal, `mysqli_free_result`, dan `error_log` jika query gagal | Fixed |

## 9. Validation Result

| Validasi | Hasil |
|---|---|
| `php --version` | PHP 8.2.12 |
| `composer --version` | Composer 2.8.8 tersedia, project tidak memakai `composer.json` |
| `npm --version` | npm 11.12.1 tersedia, project tidak memakai `package.json` |
| `composer validate` | Tidak dijalankan karena tidak ada `composer.json` |
| `npm run build` | Tidak dijalankan karena tidak ada `package.json` |
| Laravel artisan command | Tidak dijalankan karena bukan Laravel dan tidak ada `artisan` |
| `php -l` file PHP aktif | 43 file PHP aktif lolos, tidak ada syntax error |
| HTTP smoke test | `index.php`, `destination.php`, `detail.php?id=1`, `tiket.php`, `transport.php`, `login.php`, `register.php` semua HTTP 200 |
| File leak test | `storage/`, `storage/private/database/bali.sql`, `bali.sql`, `_archive/`, `database/`, `config/database.php`, `includes/helpers.php`, `partials/head.php` semua HTTP 403 |
| Static stale path check | Tidak ada referensi aktif ke path nested lama atau dump SQL root lama di dokumen aktif |
| Suppressed mysqli/include check | Tidak ada `@include`, `@require`, atau `@mysqli` di PHP aktif |

Catatan scan asset:

- Scan statis sempat menandai beberapa referensi dari `partials/head.php` dan subfolder admin/booking sebagai missing.
- Setelah review manual, itu false positive karena halaman subfolder memakai `$base_href` (`../` atau `../../`) sebelum include partial.

Kesimpulan validasi:

- Project lolos validasi dasar pasca-cleanup.
- Tidak ada error PHP aktif yang ditemukan.
- Proteksi `.htaccess` untuk `storage`, route legacy, dan file sensitif bekerja di Laragon/Apache lokal.

## 10. Remaining Issues

| No | Masalah | Lokasi | Dampak | Rekomendasi Fix |
|---:|---|---|---|---|
| 1 | SQL dump masih berada di dalam folder project lokal | `storage/private/database/bali.sql` | Aman di Apache karena `storage/` diblokir, tetapi riskan jika server tidak membaca `.htaccess` | Pindahkan ke luar public root saat production bila hosting mendukung |
| 2 | Arsip legacy masih berada di dalam folder project lokal | `storage/private/archive/` | Aman di Apache karena `storage/` diblokir, tetapi tetap menambah ukuran project | Pindahkan ke luar public root atau hapus setelah backup final |
| 3 | Original image besar masih ada | `images/` | Ukuran project besar | Tetapkan policy asset: keep source di luar public root atau compress batch |
| 4 | Banyak CSS page-specific dan import lama | `styles/` | Maintainability menurun | Refactor CSS bertahap ke token/component/page structure |
| 5 | File PHP masih mencampur query, validasi, dan view | Root `.php`, `admin/`, `booking/` | Sulit dirawat saat fitur bertambah | Buat helper/service per fitur secara bertahap |
| 6 | SQL migration manual belum otomatis | `database/*.sql` | Developer baru perlu langkah manual | Pertahankan manual, tetapi tambah checklist urutan eksekusi |
| 7 | Worktree banyak perubahan historis | Git status | Sulit membedakan cleanup ini dengan perubahan sebelumnya | Review diff sebelum commit dan pisahkan commit per tema |

## 11. UI/UX Professional Review

Kondisi saat ini:

- Homepage, transport, tiket, booking, dan contact sudah bergerak ke style modern minimalis.
- Design token dan theme sync sudah tersedia.
- Light/dark mode sudah lebih konsisten.
- Masih ada CSS lama seperti `global.css`, `navbar.css`, `home.css`, dan page-specific CSS yang belum sepenuhnya menyatu dengan sistem baru.
- Beberapa halaman result masih memiliki inline/legacy visual pattern.

Rekomendasi modernisasi:

1. Warna utama:
   - Teal/turquoise sebagai primary.
   - Sunset/coral sebagai accent.
   - Neutral off-white/ink untuk background dan text.
2. Font:
   - Pertahankan Inter + Plus Jakarta Sans.
3. Layout homepage:
   - Pertahankan hero premium, CTA jelas, destination cards, dan trip flow.
4. Navbar:
   - Konsisten sticky/compact, active state jelas, dan mobile menu rapi.
5. Card destinasi:
   - Gunakan aspect-ratio stabil, overlay minimal, dan text hierarchy singkat.
6. Form:
   - Gunakan input height konsisten, label jelas, spacing 12-16px, error state standar.
7. Animasi:
   - Pertahankan micro-interaction ringan; hindari animasi berlebihan.
8. Responsive:
   - Audit 360px, 768px, 1024px, dan desktop wide.
9. CSS:
   - Kurangi `@import` lama dan pindahkan style reusable ke `_components.css`.

UI kecil yang dilakukan pada fase ini:

- Tidak ada redesign baru; cleanup difokuskan pada struktur dan file legacy.

## 12. Recommended Modern Minimalist Structure

Struktur final yang direkomendasikan untuk PHP native ini:

```text
bali-project/
|-- admin/
|-- assets/
|   |-- js/
|-- auth/                         # optional future: login/register/logout
|-- booking/
|-- config/
|-- database/
|   |-- migrations-manual/
|   |-- README.md
|-- docs/
|-- includes/
|   |-- auth.php
|   |-- database.php
|   |-- helpers.php
|-- partials/
|   |-- head.php
|   |-- navbar.php
|   |-- footer.php
|-- public-assets/
|   |-- images/
|   |-- optimized/
|-- styles/
|   |-- _tokens.css
|   |-- _base.css
|   |-- _components.css
|   |-- pages/
|-- storage/
|   |-- logs/
|-- uploads/
|-- index.php
|-- destination.php
|-- detail.php
|-- README.md
|-- DEPLOYMENT.md
```

Catatan:

- Jangan memindahkan besar-besaran sekarang karena URL lama harus dipertahankan.
- Struktur di atas sebaiknya dilakukan bertahap dengan update referensi dan smoke test.

## 13. Upgrade Recommendations

| No | Upgrade | Prioritas | Dampak | Kompleksitas |
|---:|---|---|---|---|
| 1 | Pindahkan SQL dump dan archive ke luar public root production | Must Have | Mengurangi risiko file leak | Low |
| 2 | Buat central bootstrap untuk config, session, security header | Must Have | Konsistensi backend dan security | Medium |
| 3 | Refactor query ke helper/service per fitur | Should Have | Maintainability naik | Medium |
| 4 | Rapikan CSS ke struktur token/base/components/pages | Should Have | UI lebih konsisten | Medium |
| 5 | Tambahkan error page reusable | Should Have | UX dan debugging lebih baik | Low |
| 6 | Tambahkan pagination/filter admin | Should Have | Admin lebih usable | Medium |
| 7 | Tambahkan logging aplikasi | Should Have | Debugging production lebih aman | Medium |
| 8 | Tambahkan sitemap/robots final setelah domain ada | Nice to Have | SEO production lebih siap | Low |
| 9 | Tambahkan cache busting asset | Nice to Have | Deployment static lebih aman | Low |
| 10 | Buat test checklist/manual QA per route | Should Have | Mengurangi regression | Low |

## 14. Next Step Roadmap

### Phase 1  Stabilization

- Tujuan: membuat project stabil dan aman dikembangkan.
- Task:
  - Audit ulang semua form POST.
  - Hilangkan operator `@` tersisa.
  - Standarkan error handling.
  - Pindahkan file non-public ke luar public root untuk production.
- Risiko:
  - Perubahan helper bisa mempengaruhi banyak halaman.
- Output:
  - Backend lebih konsisten dan file leak risk lebih rendah.

### Phase 2  UI Modernization

- Tujuan: membuat tampilan konsisten modern minimalis.
- Task:
  - Konsolidasikan CSS lama.
  - Audit responsive semua halaman utama.
  - Standarkan button, card, input, alert, empty state.
- Risiko:
  - Perubahan CSS global bisa mempengaruhi halaman lama.
- Output:
  - UI lebih profesional dan maintainable.

### Phase 3  Admin Dashboard

- Tujuan: membuat admin lebih operasional.
- Task:
  - Tambahkan CRUD master data.
  - Tambahkan list booking/contact/review.
  - Tambahkan filter dan pagination.
- Risiko:
  - Butuh schema DB lebih rapi.
- Output:
  - Admin panel yang lebih siap dipakai.

### Phase 4  Booking/Ticket System

- Tujuan: mengubah booking dari prototype menjadi flow transaksi internal.
- Task:
  - Rapikan tabel bookings dan booking_details.
  - Tambahkan status booking/payment.
  - Tambahkan invoice lebih rapi.
- Risiko:
  - Perlu keputusan bisnis dan schema matang.
- Output:
  - Booking flow lebih jelas dan bisa dikembangkan ke payment.

### Phase 5  Security & Deployment

- Tujuan: siap public hosting.
- Task:
  - Final file placement.
  - HTTPS, backup, logging, error page.
  - Security review ulang.
  - Sitemap/robots final.
- Risiko:
  - Shared hosting bisa berbeda dukungan `.htaccess`.
- Output:
  - Checklist deployment production lebih solid.

## 15. Detailed Follow-up Codex Prompts

### 1. Prompt memperbaiki error tersisa

```text
Tujuan:
- Audit dan perbaiki error tersisa Bali Project PHP native tanpa redesign.

Scope:
- Fokus pada error runtime, warning PHP, broken include, broken asset, dan form yang gagal.

File yang perlu dianalisis:
- Semua *.php aktif
- includes/
- partials/
- .htaccess
- docs/PROJECT_GUIDE.md

Langkah kerja:
1. Jalankan php -l semua file PHP aktif.
2. Jalankan HTTP smoke test route utama.
3. Cek log/error output.
4. Perbaiki hanya error yang root cause-nya jelas.
5. Jangan mengubah database.

Batasan keamanan:
- Jangan tampilkan credential.
- Jangan hapus file.
- Jangan menjalankan SQL production.

Output:
- Patch minimal.
- Ringkasan error fixed dan residual.

Testing:
- php -l semua file diubah.
- HTTP smoke test route utama.

Laporan:
- Tulis perubahan, verifikasi, dan risiko tersisa.
```

### 2. Prompt modernisasi UI homepage

```text
Tujuan:
- Modernisasi homepage menjadi premium tropical, clean, minimalis.

Scope:
- index.php
- styles/page.home.css
- styles/_tokens.css
- assets/js/app.js
- images/optimized/

Langkah kerja:
1. Audit layout homepage.
2. Pertahankan query database.
3. Rapikan hero, CTA, destination cards, dan responsive.
4. Pastikan light/dark mode.
5. Jangan menambah framework.

Batasan keamanan:
- Escape output DB.
- Jangan mengambil asset eksternal sembarangan.

Output:
- Patch UI homepage.
- Catatan visual before/after.

Testing:
- php -l index.php.
- HTTP smoke test.
- Screenshot desktop/mobile jika tool tersedia.

Laporan:
- Perubahan visual, file diubah, verifikasi, catatan responsive.
```

### 3. Prompt merapikan CSS dan JavaScript

```text
Tujuan:
- Rapikan struktur CSS/JS tanpa mengubah tampilan besar.

Scope:
- styles/
- assets/js/app.js
- partials/head.php

Langkah kerja:
1. Audit CSS yang dipakai.
2. Identifikasi duplikasi token, button, card, form.
3. Pindahkan style reusable ke _components.css.
4. Jangan hapus CSS jika belum terbukti aman.
5. Jalankan smoke test visual.

Batasan keamanan:
- Jangan memindahkan asset tanpa update path.

Output:
- Patch CSS kecil bertahap.
- Daftar CSS yang perlu review manual.

Testing:
- HTTP smoke test halaman utama.

Laporan:
- CSS yang dirapikan, risiko cascade, dan next step.
```

### 4. Prompt membuat layout template profesional

```text
Tujuan:
- Membuat layout template PHP native yang reusable.

Scope:
- partials/head.php
- partials/navbar.php
- partials/footer.php
- includes/helpers.php

Langkah kerja:
1. Audit partial yang ada.
2. Buat helper page meta jika perlu.
3. Pastikan base href subfolder tetap aman.
4. Jangan ubah semua halaman sekaligus jika berisiko.

Batasan keamanan:
- Escape meta/title.
- Jangan bocorkan path server.

Output:
- Partial lebih reusable.
- Contoh migrasi satu halaman.

Testing:
- php -l file diubah.
- HTTP smoke test root dan subfolder.

Laporan:
- Pola layout baru dan backward compatibility.
```

### 5. Prompt membuat dashboard admin

```text
Tujuan:
- Lanjutkan dashboard admin agar lebih informatif.

Scope:
- admin/
- includes/auth.php
- styles/admin.css
- database SQL manual jika perlu

Langkah kerja:
1. Audit dashboard saat ini.
2. Tambahkan statistik read-only.
3. Tambahkan link ke modul destinasi/booking/contact/review.
4. Jangan buat destructive action.

Batasan keamanan:
- Wajib require admin.
- Escape output.
- Jangan tampilkan SQL error mentah.

Output:
- Dashboard admin lebih lengkap.

Testing:
- php -l admin/*.php.
- Smoke test admin setelah login.

Laporan:
- Fitur dashboard dan next CRUD.
```

### 6. Prompt membuat CRUD destinasi wisata

```text
Tujuan:
- Lengkapi CRUD destinasi wisata admin.

Scope:
- admin/destinations/
- detail.php
- destination.php
- images/uploads/destinations/
- database SQL manual

Langkah kerja:
1. Audit schema destinasi.
2. Pastikan create/edit/delete memakai CSRF.
3. Validasi upload image.
4. Gunakan soft-delete/disable.

Batasan keamanan:
- Jangan hard-delete data nyata.
- Validasi MIME, extension, size.

Output:
- CRUD destinasi lengkap.

Testing:
- php -l admin/destinations/*.php.
- Test create/edit/disable local.

Laporan:
- Fitur, validasi, file diubah, risiko.
```

### 7. Prompt membuat CRUD tiket

```text
Tujuan:
- Membuat CRUD tiket/transport admin.

Scope:
- tabel buses, routes_bus, pesawat, bookings_pesawat, hotel, car
- admin/tickets/ atau admin/master/
- database SQL manual jika perlu

Langkah kerja:
1. Audit schema tiket saat ini.
2. Rancang modul CRUD per jenis layanan.
3. Gunakan prepared statement dan CSRF.
4. Jangan menghapus data tanpa soft-delete.

Batasan keamanan:
- Admin only.
- Escape semua output.

Output:
- Modul CRUD tiket awal.

Testing:
- php -l file admin baru.
- Test create/edit local.

Laporan:
- Struktur modul dan schema gap.
```

### 8. Prompt membuat sistem booking

```text
Tujuan:
- Mematangkan sistem booking internal.

Scope:
- booking/
- database/2026_06_09_create_internal_booking_tables.sql
- admin booking list

Langkah kerja:
1. Audit flow booking saat ini.
2. Rapikan validasi dan status.
3. Tambahkan admin list booking read-only.
4. Jangan integrasi payment gateway dulu.

Batasan keamanan:
- CSRF wajib.
- Token invoice tidak boleh mudah ditebak.

Output:
- Booking flow lebih stabil.

Testing:
- php -l booking/*.php.
- Test booking dummy local.

Laporan:
- Flow, file diubah, verifikasi, risiko payment manual.
```

### 9. Prompt membuat login/register

```text
Tujuan:
- Audit dan lengkapi auth dasar.

Scope:
- register.php
- login.php
- logout.php
- includes/auth.php
- database auth SQL

Langkah kerja:
1. Audit session dan CSRF.
2. Pastikan password_hash/password_verify.
3. Tambahkan rate-limit sederhana jika memungkinkan.
4. Pastikan pesan error user-friendly.

Batasan keamanan:
- Jangan tampilkan detail DB.
- Jangan commit secret.

Output:
- Auth lebih aman.

Testing:
- php -l auth files.
- Test register/login/logout.

Laporan:
- Auth checklist dan residual risk.
```

### 10. Prompt optimasi keamanan

```text
Tujuan:
- Hardening security Bali Project.

Scope:
- semua PHP aktif
- .htaccess
- includes/
- config/

Langkah kerja:
1. Audit input/output.
2. Tambah escaping jika kurang.
3. Pastikan CSRF untuk POST.
4. Audit file leak.
5. Audit upload.

Batasan keamanan:
- Jangan ubah database production.
- Jangan tampilkan credential.

Output:
- Patch security minimal.

Testing:
- php -l file diubah.
- HTTP smoke dan file leak test.

Laporan:
- Temuan, mitigasi, residual risk.
```

### 11. Prompt optimasi performa

```text
Tujuan:
- Optimasi performa tanpa build tool berat.

Scope:
- images/
- images/optimized/
- styles/
- .htaccess
- halaman utama

Langkah kerja:
1. Audit file terbesar.
2. Gunakan WebP optimized bila tersedia.
3. Tambahkan lazy loading/dimensi gambar.
4. Audit cache header.

Batasan keamanan:
- Jangan hapus original tanpa izin.

Output:
- Patch image usage dan checklist kompresi.

Testing:
- php -l file PHP diubah.
- HTTP smoke test.

Laporan:
- Asset terbesar, dampak, verifikasi.
```

### 12. Prompt persiapan deployment

```text
Tujuan:
- Siapkan project untuk deployment shared hosting Apache.

Scope:
- README.md
- DEPLOYMENT.md
- .htaccess
- config.example.php
- database notes

Langkah kerja:
1. Audit file non-public.
2. Pastikan .htaccess memblokir file sensitif.
3. Dokumentasikan env var DB.
4. Dokumentasikan import DB dan rollback.
5. Jangan deploy sungguhan.

Batasan keamanan:
- Jangan hardcode credential.
- SQL dump jangan dipublish.

Output:
- Checklist deployment.

Testing:
- php -l config example.
- HTTP smoke dan file leak test.

Laporan:
- File diubah, rollback, risiko tersisa.
```

## 16. Final Notes

Project sudah lebih rapi pada level file aktif karena file legacy/duplikat yang paling jelas sudah dipisahkan ke `storage/private`, bukan dihapus permanen. Perubahan paling penting adalah mengeluarkan `hasil.transport.php`, `bali.sql`, dan `_archive` dari root aktif, serta menambahkan proteksi `storage`.

Risiko terbesar yang masih ada:

- File non-public di `storage/private` masih berada di dalam folder project lokal. `.htaccess` ditujukan untuk memblokirnya di Apache, tetapi hosting non-Apache atau server yang mengabaikan `.htaccess` tetap berisiko.

Langkah berikutnya yang paling direkomendasikan:

1. Review dan commit perubahan per tema.
2. Pindahkan file non-public ke luar public root sebelum production.
3. Lanjutkan refactor CSS dan helper backend secara bertahap.
4. Jalankan QA visual desktop/mobile untuk halaman utama, booking, hasil pencarian, auth, dan admin.
