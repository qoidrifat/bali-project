# Bali Project Deployment Checklist

Panduan ini untuk publish Bali Project PHP native ke Apache/Laragon-like atau shared hosting. Jangan deploy langsung dari checklist ini tanpa backup database dan review credential.

## 1. Checklist Production

- [ ] Gunakan PHP 8.x dengan extension `mysqli` aktif.
- [ ] Aktifkan Apache `mod_rewrite`, `mod_headers`, `mod_expires`, dan `mod_deflate` jika tersedia.
- [ ] Set `display_errors=Off` dan `log_errors=On` di hosting.
- [ ] Buat database production baru, jangan memakai database development.
- [ ] Buat user database khusus production dengan permission minimum yang dibutuhkan aplikasi.
- [ ] Set environment variable `BALI_DB_HOST`, `BALI_DB_USER`, `BALI_DB_PASSWORD`, dan `BALI_DB_NAME`.
- [ ] Import `storage/private/database/bali.sql` sekali ke database kosong, setelah backup dan review isi file.
- [ ] Jalankan SQL tambahan di `database/*.sql` hanya jika fitur terkait benar-benar dipakai.
- [ ] Jangan publish dump SQL, file backup, `.env`, log, `storage/private`, dan archive sebagai route publik.
- [ ] Pastikan `.htaccess` ikut ter-upload ke public root.
- [ ] Pastikan folder upload seperti `images/uploads/` tidak mengizinkan eksekusi PHP.
- [ ] Test halaman utama, destinasi, detail, tiket, login/register, dan admin setelah upload.
- [ ] Aktifkan HTTPS dari panel hosting.
- [ ] Setelah domain final tersedia, buat `robots.txt` dan `sitemap.xml` dengan URL domain yang benar.

## 2. File Yang Boleh Masuk Public Root

Jika shared hosting hanya menyediakan satu public folder, upload isi folder project ini ke public root, tetapi pastikan `.htaccess` aktif.

File/folder route publik:

```text
*.php halaman utama
admin/
assets/
booking/
images/
partials/      # diblokir dari akses langsung oleh .htaccess
styles/
```

File/folder yang tidak boleh menjadi route publik:

```text
database/
storage/private/
config/
includes/
*.md
*.log
*.bak
*.backup
.env
```

Catatan: `.htaccess` saat ini memblokir akses langsung ke dump SQL, markdown, `storage`, `config`, `includes`, `database`, dan `partials`. Tetap lebih aman jika file non-public dipindahkan ke luar public root saat hosting mendukung struktur tersebut.

## 3. Konfigurasi Database

Kode membaca konfigurasi dari environment variable melalui `config/database.php`.

```text
BALI_DB_HOST=localhost
BALI_DB_USER=your_database_user
BALI_DB_PASSWORD=your_database_password
BALI_DB_NAME=your_database_name
```

Jangan menulis credential production ke `connection.php`, `config/database.php`, README, atau file lain di repository.

## 4. Cara Import Database

Lewat phpMyAdmin:

1. Buka panel hosting atau phpMyAdmin.
2. Buat database production.
3. Pilih database tersebut.
4. Buka tab Import.
5. Upload `storage/private/database/bali.sql`.
6. Jalankan import.
7. Review tabel yang terbentuk.

Lewat CLI MySQL jika hosting menyediakan akses shell:

```sh
mysql -u USER_DATABASE -p NAMA_DATABASE < storage/private/database/bali.sql
```

SQL fitur tambahan bersifat manual:

```text
database/2026_06_09_create_auth_tables.sql
database/2026_06_09_create_contact_messages_table.sql
database/2026_06_09_create_internal_booking_tables.sql
database/2026_06_09_create_reviews_table.sql
database/2026_06_09_add_destination_admin_columns.sql
database/2026_06_09_promote_admin_user.sql
```

Jalankan hanya setelah backup database dan setelah fitur terkait dibutuhkan.

## 5. Security Header dan Cache

`.htaccess` menambahkan:

- `Options -Indexes`
- Proteksi file `.sql`, `.env`, `.md`, `.log`, backup, config example, dan hidden file.
- Blok akses langsung ke `storage`, `_archive`, `config`, `includes`, `database`, `partials`, dan `hasil.transport.php`.
- Header `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, dan `X-Permitted-Cross-Domain-Policies`.
- Cache static asset untuk gambar, CSS, JS, dan font.
- Deflate compression untuk HTML/CSS/JS/JSON/SVG.
- `display_errors Off` dan `log_errors On` jika server memakai `mod_php`.

Jika hosting memakai PHP-FPM/CGI, setting `display_errors` harus diatur dari panel hosting atau file PHP ini.

## 6. Robots dan Sitemap

Jangan membuat sitemap final sebelum domain production diketahui.

Contoh `robots.txt` setelah domain siap:

```text
User-agent: *
Allow: /
Sitemap: https://domain-anda.com/sitemap.xml
```

Contoh URL utama untuk `sitemap.xml`:

```text
https://domain-anda.com/
https://domain-anda.com/destination.php
https://domain-anda.com/about.php
https://domain-anda.com/contact.php
https://domain-anda.com/visa.php
https://domain-anda.com/transport.php
https://domain-anda.com/tiket.php
```

## 7. Verifikasi Setelah Upload

```sh
php -l index.php
php -l destination.php
php -l detail.php
php -l connection.php
php -l includes/database.php
php -l partials/head.php
php -l config.example.php
```

Smoke test browser:

```text
/index.php
/destination.php
/detail.php?id=1
/tiket.php
/transport.php
/login.php
/register.php
```

File leak test:

```text
/bali.sql              harus 403 atau 404
/storage/              harus 403
/storage/private/database/bali.sql harus 403
/_archive/             harus 403 atau 404
/database/             harus 403
/config/database.php   harus 403
/includes/helpers.php  harus 403
/partials/head.php     harus 403
/DEPLOYMENT.md         harus 403
```

## 8. Cara Rollback

1. Simpan backup file sebelum upload.
2. Simpan export database sebelum import SQL.
3. Jika upload file bermasalah, restore folder project dari backup hosting.
4. Jika import SQL bermasalah, restore database dari backup.
5. Jika `.htaccess` menyebabkan error 500, rename sementara `.htaccess` menjadi `.htaccess.disabled`, lalu aktifkan ulang rule satu per satu.

## 9. Risiko Tersisa

- Dump SQL sudah dipindahkan ke `storage/private/database/bali.sql` dan diblokir `.htaccess`; idealnya tetap jangan ikut public root production jika hosting memungkinkan struktur private di luar web root.
- Archive legacy sudah dipindahkan ke `storage/private/archive` dan diblokir `.htaccess`; idealnya tetap dipisahkan dari public root production.
- Beberapa gambar original besar masih ada untuk fallback/source; halaman sudah diarahkan ke optimized WebP jika tersedia.
- Belum ada central bootstrap untuk semua setting PHP runtime; beberapa setting production tetap bergantung pada konfigurasi hosting.
