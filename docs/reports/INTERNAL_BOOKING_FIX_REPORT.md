# Internal Booking Fix Report

## 1. Ringkasan Masalah

Halaman internal booking sebelumnya menampilkan pesan:

> Booking belum bisa dibuat. Tabel booking internal belum tersedia. Jalankan migration manual booking terlebih dahulu.

Masalah muncul saat user submit form booking internal karena sistem tidak menemukan tabel `bookings` dan `booking_details` yang dibutuhkan untuk menyimpan data booking.

## 2. Root Cause

Project ini adalah PHP native, bukan Laravel. Tidak ditemukan `artisan`, `composer.json`, folder `routes/`, `app/`, `resources/`, atau `database/migrations/`.

Akar masalah:

- Flow submit `booking/store.php` memanggil `booking_schema_ready()`.
- Helper `booking_schema_ready()` mengecek tabel `bookings` dan `booking_details`.
- Database lokal `bali` berhasil terkoneksi, tetapi kedua tabel tersebut belum ada.
- SQL manual `database/2026_06_09_create_internal_booking_tables.sql` sudah tersedia, tetapi belum diterapkan ke database.
- SQL manual sebelumnya memiliki foreign key `bookings.user_id` ke tabel `users`. Kolom `user_id` memang nullable, tetapi constraint ini membuat migration lebih rapuh jika auth schema belum lengkap di environment lain.

## 3. File yang Dianalisis

- `booking/index.php`
- `booking/store.php`
- `booking/confirmation.php`
- `booking/invoice.php`
- `booking/_helpers.php`
- `database/2026_06_09_create_internal_booking_tables.sql`
- `includes/auth.php`
- `includes/database.php`
- `config/database.php`
- `connection.php`
- `.htaccess`
- Struktur Laravel: `artisan`, `composer.json`, `routes/`, `app/`, `resources/`, `database/migrations/`

## 4. File yang Diubah

| No | File | Jenis Perubahan | Alasan |
|---:|---|---|---|
| 1 | `database/2026_06_09_create_internal_booking_tables.sql` | Menghapus foreign key wajib ke `users`, tetap mempertahankan `user_id` nullable dan index | Agar migration internal booking aman untuk guest booking dan environment yang auth schema-nya belum lengkap |
| 2 | `booking/_helpers.php` | Menambahkan pengecekan kolom wajib melalui `booking_schema_status()` dan `booking_table_columns()` | Agar sistem mendeteksi schema booking secara akurat, bukan hanya mengecek nama tabel |
| 3 | `INTERNAL_BOOKING_FIX_REPORT.md` | Laporan hasil perbaikan | Dokumentasi root cause, perubahan, testing, dan risiko tersisa |

## 5. Migration dan Database

Project tidak menggunakan Laravel migration. Perubahan database dilakukan menggunakan SQL manual non-destruktif:

- File SQL: `database/2026_06_09_create_internal_booking_tables.sql`
- Tabel utama: `bookings`
- Tabel detail: `booking_details`
- Command yang diterapkan setara dengan `CREATE TABLE IF NOT EXISTS`
- Tidak ada data lama yang dihapus.
- Tidak menjalankan `migrate:fresh`, `migrate:reset`, `db:wipe`, `git reset`, atau `git clean`.

Kolom `bookings`:

- `id`
- `booking_code`
- `public_token`
- `user_id`
- `customer_name`
- `customer_email`
- `customer_phone`
- `booking_status`
- `payment_status`
- `notes`
- `created_at`
- `updated_at`

Kolom `booking_details`:

- `id`
- `booking_id`
- `service_type`
- `service_name`
- `origin_label`
- `destination_label`
- `start_date`
- `end_date`
- `quantity`
- `unit_label`
- `unit_price`
- `subtotal`
- `created_at`

Status setelah perbaikan:

- `bookings`: tersedia
- `booking_details`: tersedia
- `booking_schema_status()`: `ready=yes`
- Row smoke test berhasil dibuat dengan kode booking `BP-260609-AD3871`

## 6. Route dan Controller

Karena project PHP native, route berbasis file langsung:

- GET halaman form: `booking/index.php`
- POST submit booking: `booking/store.php`
- GET konfirmasi: `booking/confirmation.php?id={id}&token={token}`
- GET invoice: `booking/invoice.php?id={id}&token={token}`

Alur submit:

1. User membuka `booking/index.php`.
2. Form mengirim POST ke `booking/store.php`.
3. `booking/store.php` memvalidasi CSRF.
4. `booking_validate_request()` memvalidasi field layanan, kota, tanggal, jumlah, data pemesan, dan catatan.
5. `db_connect()` membuka koneksi database.
6. `booking_schema_ready()` memastikan tabel dan kolom wajib tersedia.
7. `booking_create()` membuat row di `bookings` dan `booking_details` dalam transaction.
8. User diarahkan ke halaman konfirmasi dengan `id` dan `public_token`.

## 7. Model

Project tidak memakai model Laravel. Logic model-like berada di `booking/_helpers.php`.

Fungsi utama:

- `booking_service_catalog()`
- `booking_city_options()`
- `booking_validate_request()`
- `booking_schema_status()`
- `booking_schema_ready()`
- `booking_create()`
- `booking_find_by_token()`
- `booking_format_money()`

Proteksi yang tersedia:

- Prepared statement untuk insert booking.
- Prepared statement untuk insert detail booking.
- Prepared statement untuk lookup booking by token.
- Transaction untuk memastikan `bookings` dan `booking_details` konsisten.
- `public_token` 64 karakter untuk akses konfirmasi/invoice.

## 8. Validasi Form

Validasi yang aktif:

- `service_type`: wajib cocok dengan catalog internal (`bus`, `flight`, `hotel`, `car`)
- `origin_label`: wajib valid untuk layanan yang butuh kota asal
- `destination_label`: wajib valid dari daftar kota
- `start_date`: wajib format tanggal valid
- `end_date`: opsional, harus valid dan tidak boleh sebelum `start_date`
- `quantity`: wajib angka, minimal 1, maksimal sesuai layanan
- `customer_name`: wajib, maksimal 120 karakter
- `customer_email`: wajib email valid, maksimal 190 karakter
- `customer_phone`: wajib pola nomor telepon 8-30 karakter
- `notes`: opsional, maksimal 1000 karakter
- `csrf_token`: wajib valid

## 9. Error yang Diperbaiki

| No | Error | Penyebab | Fix | Status |
|---:|---|---|---|---|
| 1 | `Tabel booking internal belum tersedia` | Tabel `bookings` dan `booking_details` belum ada di database lokal | Menerapkan SQL manual `CREATE TABLE IF NOT EXISTS` secara aman | Fixed |
| 2 | SQL manual berpotensi gagal di environment tanpa auth schema lengkap | Foreign key `bookings.user_id` wajib mereferensi `users` | Constraint FK dihapus, `user_id` tetap nullable dan indexed | Fixed |
| 3 | Schema check terlalu dangkal | Helper hanya cek tabel, belum cek kolom wajib | Menambahkan `booking_schema_status()` untuk cek tabel dan kolom wajib | Fixed |

## 10. Hasil Testing

Command dan hasil:

- `php --version`: PHP 8.2.12
- `php artisan --version`: tidak dijalankan karena `artisan` tidak ada; project PHP native
- `php artisan migrate:status`: tidak berlaku karena project PHP native
- `php artisan route:list`: tidak berlaku karena project PHP native
- `composer validate`: tidak berlaku karena `composer.json` tidak ada
- `php -l booking/_helpers.php`: OK
- `php -l booking/store.php`: OK
- `php -l booking/index.php`: OK
- `php -l booking/confirmation.php`: OK
- `php -l booking/invoice.php`: OK

HTTP smoke test:

- GET `http://localhost/bali-project/booking/index.php`: `200`
- POST `http://localhost/bali-project/booking/store.php`: `302` ke `confirmation.php?id=1&token=...`
- GET halaman konfirmasi: `200`, memuat teks `Booking Anda berhasil dibuat.`
- GET halaman invoice: `200`, memuat invoice dan kode booking

Database validation:

- `booking_schema_status()`: `ready=yes`
- Row smoke test:
  - `id`: `1`
  - `booking_code`: `BP-260609-AD3871`
  - `customer_email`: `smoke.booking@example.test`

## 11. Sisa Risiko

- Row smoke test sengaja tidak dihapus karena instruksi membatasi penghapusan data database.
- Sistem auth belum sepenuhnya tervalidasi; database memiliki tabel `users`, tetapi tabel `roles` tidak ditemukan pada pengecekan awal.
- Belum ada admin UI untuk melihat dan mengelola booking internal.
- Belum ada payment gateway; status pembayaran masih manual.
- Tidak ada automated test suite karena project PHP native sederhana.
- Jika hosting production memakai database berbeda, SQL manual tetap perlu diterapkan di database production secara hati-hati.

## 12. Cara Menjalankan Manual

1. Pastikan MySQL aktif.
2. Pastikan konfigurasi database di `config/database.php` atau environment variable berikut sudah benar:
   - `BALI_DB_HOST`
   - `BALI_DB_USER`
   - `BALI_DB_PASSWORD`
   - `BALI_DB_NAME`
3. Jika tabel booking belum ada, jalankan SQL manual:
   - `database/2026_06_09_create_internal_booking_tables.sql`
4. Jalankan project via Laragon atau Apache lokal.
5. Buka:
   - `http://localhost/bali-project/booking/index.php`
6. Isi form booking.
7. Submit form.
8. Pastikan diarahkan ke halaman konfirmasi.
9. Pastikan data masuk ke tabel:
   - `bookings`
   - `booking_details`

## 13. Final Status

Fixed.

Fitur internal booking sekarang dapat membuat data booking dengan normal di database lokal. Schema booking sudah tersedia, helper schema lebih akurat, submit form berhasil, halaman konfirmasi berhasil, dan invoice berhasil dibuka.
