<?php
require_once __DIR__ . '/../includes/auth.php';

if (!function_exists('booking_service_catalog')) {
    function booking_service_catalog()
    {
        return [
            'bus' => [
                'label' => 'Tiket Bus',
                'service_name' => 'Bus Antar Kota ke Bali',
                'unit_label' => 'kursi',
                'unit_price' => 175000,
                'max_quantity' => 4,
                'requires_origin' => true,
            ],
            'flight' => [
                'label' => 'Tiket Pesawat',
                'service_name' => 'Penerbangan Ekonomi ke Bali',
                'unit_label' => 'penumpang',
                'unit_price' => 750000,
                'max_quantity' => 6,
                'requires_origin' => true,
            ],
            'hotel' => [
                'label' => 'Booking Hotel',
                'service_name' => 'Hotel Partner Bali Paradise',
                'unit_label' => 'kamar',
                'unit_price' => 450000,
                'max_quantity' => 5,
                'requires_origin' => false,
            ],
            'car' => [
                'label' => 'Sewa Mobil',
                'service_name' => 'Rental Mobil Harian',
                'unit_label' => 'mobil',
                'unit_price' => 350000,
                'max_quantity' => 3,
                'requires_origin' => false,
            ],
        ];
    }
}

if (!function_exists('booking_city_options')) {
    function booking_city_options()
    {
        return [
            'Surabaya' => 'Surabaya',
            'Denpasar' => 'Denpasar',
            'Jakarta' => 'Jakarta',
            'Yogyakarta' => 'Yogyakarta',
            'Bandung' => 'Bandung',
        ];
    }
}

if (!function_exists('booking_table_exists')) {
    function booking_table_exists($connection, $table)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', (string) $table)) {
            return false;
        }

        $tableName = mysqli_real_escape_string($connection, $table);
        $result = mysqli_query($connection, "SHOW TABLES LIKE '{$tableName}'");

        if (!$result) {
            error_log('Booking table check failed: ' . mysqli_error($connection));
            return false;
        }

        $exists = mysqli_num_rows($result) > 0;
        mysqli_free_result($result);

        return $exists;
    }
}

if (!function_exists('booking_table_columns')) {
    function booking_table_columns($connection, $table)
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', (string) $table)) {
            return [];
        }

        $tableName = mysqli_real_escape_string($connection, $table);
        $result = mysqli_query($connection, "SHOW COLUMNS FROM `{$tableName}`");

        if (!$result) {
            error_log('Booking column check failed for ' . $table . ': ' . mysqli_error($connection));
            return [];
        }

        $columns = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $columns[] = $row['Field'];
        }

        mysqli_free_result($result);

        return $columns;
    }
}

if (!function_exists('booking_schema_status')) {
    function booking_schema_status($connection)
    {
        $required = [
            'bookings' => [
                'id',
                'booking_code',
                'public_token',
                'user_id',
                'customer_name',
                'customer_email',
                'customer_phone',
                'booking_status',
                'payment_status',
                'notes',
                'created_at',
                'updated_at',
            ],
            'booking_details' => [
                'id',
                'booking_id',
                'service_type',
                'service_name',
                'origin_label',
                'destination_label',
                'start_date',
                'end_date',
                'quantity',
                'unit_label',
                'unit_price',
                'subtotal',
                'created_at',
            ],
        ];

        $missingTables = [];
        $missingColumns = [];

        foreach ($required as $table => $columns) {
            if (!booking_table_exists($connection, $table)) {
                $missingTables[] = $table;
                continue;
            }

            $existingColumns = booking_table_columns($connection, $table);
            $diff = array_values(array_diff($columns, $existingColumns));

            if ($diff) {
                $missingColumns[$table] = $diff;
            }
        }

        return [
            'ready' => empty($missingTables) && empty($missingColumns),
            'missing_tables' => $missingTables,
            'missing_columns' => $missingColumns,
        ];
    }
}

if (!function_exists('booking_schema_ready')) {
    function booking_schema_ready($connection)
    {
        $status = booking_schema_status($connection);

        return $status['ready'];
    }
}

if (!function_exists('booking_default_values')) {
    function booking_default_values()
    {
        $user = auth_check() ? auth_user() : null;

        return [
            'service_type' => 'bus',
            'origin_label' => 'Surabaya',
            'destination_label' => 'Denpasar',
            'start_date' => '',
            'end_date' => '',
            'quantity' => '1',
            'customer_name' => $user['name'] ?? '',
            'customer_email' => $user['email'] ?? '',
            'customer_phone' => '',
            'notes' => '',
        ];
    }
}

if (!function_exists('booking_validate_request')) {
    function booking_validate_request(array $input)
    {
        $errors = [];
        $catalog = booking_service_catalog();
        $cities = booking_city_options();
        $serviceType = $input['service_type'] ?? '';
        $service = $catalog[$serviceType] ?? null;

        if (!$service) {
            $errors[] = 'Pilih jenis tiket yang valid.';
        }

        $origin = trim($input['origin_label'] ?? '');
        $destination = trim($input['destination_label'] ?? '');
        $startDate = trim($input['start_date'] ?? '');
        $endDate = trim($input['end_date'] ?? '');
        $quantityRaw = trim((string) ($input['quantity'] ?? ''));
        $customerName = trim($input['customer_name'] ?? '');
        $customerEmail = trim($input['customer_email'] ?? '');
        $customerPhone = trim($input['customer_phone'] ?? '');
        $notes = trim($input['notes'] ?? '');

        if ($service && !empty($service['requires_origin']) && !isset($cities[$origin])) {
            $errors[] = 'Pilih kota asal yang valid.';
        }

        if (!isset($cities[$destination])) {
            $errors[] = 'Pilih kota tujuan yang valid.';
        }

        if ($service && !empty($service['requires_origin']) && $origin !== '' && $origin === $destination) {
            $errors[] = 'Kota asal dan tujuan tidak boleh sama.';
        }

        if (!is_valid_date_value($startDate)) {
            $errors[] = 'Tanggal mulai wajib diisi dengan format yang valid.';
        }

        if ($endDate !== '' && !is_valid_date_value($endDate)) {
            $errors[] = 'Tanggal selesai tidak valid.';
        }

        if ($startDate !== '' && $endDate !== '' && is_valid_date_value($startDate) && is_valid_date_value($endDate) && $endDate < $startDate) {
            $errors[] = 'Tanggal selesai tidak boleh sebelum tanggal mulai.';
        }

        if (!ctype_digit($quantityRaw)) {
            $errors[] = 'Jumlah wajib berupa angka.';
            $quantity = 0;
        } else {
            $quantity = (int) $quantityRaw;
            $maxQuantity = $service['max_quantity'] ?? 1;

            if ($quantity < 1 || $quantity > $maxQuantity) {
                $errors[] = 'Jumlah melebihi stok dasar untuk layanan ini. Maksimal ' . $maxQuantity . ' ' . ($service['unit_label'] ?? 'item') . '.';
            }
        }

        if ($customerName === '' || strlen($customerName) > 120) {
            $errors[] = 'Nama pemesan wajib diisi maksimal 120 karakter.';
        }

        if (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL) || strlen($customerEmail) > 190) {
            $errors[] = 'Email pemesan tidak valid.';
        }

        if (!preg_match('/^[0-9+()\-\s]{8,30}$/', $customerPhone)) {
            $errors[] = 'Nomor telepon wajib 8-30 karakter angka/simbol telepon.';
        }

        if (strlen($notes) > 1000) {
            $errors[] = 'Catatan maksimal 1000 karakter.';
        }

        return [
            'errors' => $errors,
            'data' => [
                'service_type' => $serviceType,
                'service_name' => $service['service_name'] ?? '',
                'origin_label' => $service && !empty($service['requires_origin']) ? $origin : null,
                'destination_label' => $destination,
                'start_date' => $startDate,
                'end_date' => $endDate !== '' ? $endDate : null,
                'quantity' => $quantity ?? 0,
                'unit_label' => $service['unit_label'] ?? 'item',
                'unit_price' => $service['unit_price'] ?? 0,
                'subtotal' => ($quantity ?? 0) * ($service['unit_price'] ?? 0),
                'customer_name' => $customerName,
                'customer_email' => $customerEmail,
                'customer_phone' => $customerPhone,
                'notes' => $notes,
            ],
        ];
    }
}

if (!function_exists('booking_make_code')) {
    function booking_make_code()
    {
        return 'BP-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3)));
    }
}

if (!function_exists('booking_create')) {
    function booking_create($connection, array $data)
    {
        $user = auth_check() ? auth_user() : null;
        $userId = $user ? (int) $user['id'] : null;
        $bookingCode = booking_make_code();
        $publicToken = bin2hex(random_bytes(32));

        mysqli_begin_transaction($connection);

        $stmt = mysqli_prepare(
            $connection,
            'INSERT INTO bookings (booking_code, public_token, user_id, customer_name, customer_email, customer_phone, notes) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            error_log('Booking insert prepare failed: ' . mysqli_error($connection));
            mysqli_rollback($connection);
            return null;
        }

        $customerName = $data['customer_name'];
        $customerEmail = $data['customer_email'];
        $customerPhone = $data['customer_phone'];
        $notes = $data['notes'];

        mysqli_stmt_bind_param(
            $stmt,
            'ssissss',
            $bookingCode,
            $publicToken,
            $userId,
            $customerName,
            $customerEmail,
            $customerPhone,
            $notes
        );

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Booking insert failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            mysqli_rollback($connection);
            return null;
        }

        $bookingId = (int) mysqli_insert_id($connection);
        mysqli_stmt_close($stmt);

        $detailStmt = mysqli_prepare(
            $connection,
            'INSERT INTO booking_details (booking_id, service_type, service_name, origin_label, destination_label, start_date, end_date, quantity, unit_label, unit_price, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        if (!$detailStmt) {
            error_log('Booking detail insert prepare failed: ' . mysqli_error($connection));
            mysqli_rollback($connection);
            return null;
        }

        $serviceType = $data['service_type'];
        $serviceName = $data['service_name'];
        $originLabel = $data['origin_label'];
        $destinationLabel = $data['destination_label'];
        $startDate = $data['start_date'];
        $endDate = $data['end_date'];
        $quantity = $data['quantity'];
        $unitLabel = $data['unit_label'];
        $unitPrice = $data['unit_price'];
        $subtotal = $data['subtotal'];

        mysqli_stmt_bind_param(
            $detailStmt,
            'issssssisdd',
            $bookingId,
            $serviceType,
            $serviceName,
            $originLabel,
            $destinationLabel,
            $startDate,
            $endDate,
            $quantity,
            $unitLabel,
            $unitPrice,
            $subtotal
        );

        if (!mysqli_stmt_execute($detailStmt)) {
            error_log('Booking detail insert failed: ' . mysqli_stmt_error($detailStmt));
            mysqli_stmt_close($detailStmt);
            mysqli_rollback($connection);
            return null;
        }

        mysqli_stmt_close($detailStmt);
        mysqli_commit($connection);

        return [
            'id' => $bookingId,
            'booking_code' => $bookingCode,
            'public_token' => $publicToken,
        ];
    }
}

if (!function_exists('booking_find_by_token')) {
    function booking_find_by_token($connection, $id, $token)
    {
        if (!is_valid_positive_id($id) || !is_string($token) || !preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }

        $bookingId = (int) $id;
        $sql = "SELECT b.*, d.service_type, d.service_name, d.origin_label, d.destination_label, d.start_date, d.end_date,
                       d.quantity, d.unit_label, d.unit_price, d.subtotal
                FROM bookings b
                JOIN booking_details d ON d.booking_id = b.id
                WHERE b.id = ? AND b.public_token = ?
                LIMIT 1";
        $stmt = mysqli_prepare($connection, $sql);

        if (!$stmt) {
            error_log('Booking lookup prepare failed: ' . mysqli_error($connection));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'is', $bookingId, $token);

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Booking lookup failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        return $row ?: null;
    }
}

if (!function_exists('booking_find_for_user')) {
    function booking_find_for_user($connection, $userId, $limit = 20)
    {
        if (!is_valid_positive_id($userId)) {
            return [];
        }

        $bookingUserId = (int) $userId;
        $bookingLimit = max(1, min(50, (int) $limit));
        $sql = "SELECT b.*, d.service_type, d.service_name, d.origin_label, d.destination_label, d.start_date, d.end_date,
                       d.quantity, d.unit_label, d.unit_price, d.subtotal
                FROM bookings b
                JOIN booking_details d ON d.booking_id = b.id
                WHERE b.user_id = ?
                ORDER BY b.id DESC
                LIMIT ?";
        $stmt = mysqli_prepare($connection, $sql);

        if (!$stmt) {
            error_log('Booking history prepare failed: ' . mysqli_error($connection));
            return [];
        }

        mysqli_stmt_bind_param($stmt, 'ii', $bookingUserId, $bookingLimit);

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Booking history lookup failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return [];
        }

        $result = mysqli_stmt_get_result($stmt);
        $rows = [];

        while ($result && ($row = mysqli_fetch_assoc($result))) {
            $rows[] = $row;
        }

        mysqli_stmt_close($stmt);

        return $rows;
    }
}

if (!function_exists('booking_count_for_user')) {
    function booking_count_for_user($connection, $userId)
    {
        if (!is_valid_positive_id($userId)) {
            return 0;
        }

        $bookingUserId = (int) $userId;
        $stmt = mysqli_prepare($connection, 'SELECT COUNT(*) AS total FROM bookings WHERE user_id = ?');

        if (!$stmt) {
            error_log('Booking history count prepare failed: ' . mysqli_error($connection));
            return 0;
        }

        mysqli_stmt_bind_param($stmt, 'i', $bookingUserId);

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Booking history count failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return 0;
        }

        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        return max(0, (int) ($row['total'] ?? 0));
    }
}

if (!function_exists('booking_render_history_list')) {
    function booking_render_history_list(array $bookings)
    {
        if (!$bookings) {
            ?>
            <div class="booking-empty-state">
              <span class="booking-eyebrow">Belum Ada Invoice</span>
              <h2>Riwayat invoice akun Anda masih kosong.</h2>
              <p>Booking yang dibuat saat login akan otomatis muncul di sini.</p>
              <a class="btn btn--primary" href="booking/index.php">Buat Booking</a>
            </div>
            <?php
            return;
        }

        foreach ($bookings as $booking):
            $routeLabel = ($booking['origin_label'] ? $booking['origin_label'] . ' ke ' : '') . $booking['destination_label'];
            $invoiceUrl = 'booking/invoice.php?id=' . (int) $booking['id'] . '&token=' . urlencode($booking['public_token']);
            ?>
            <article class="invoice-history-card" data-booking-id="<?= (int) $booking['id'] ?>">
              <div class="invoice-history-card__main">
                <span class="booking-eyebrow"><?= e($booking['service_type']) ?></span>
                <h2><?= e($booking['booking_code']) ?></h2>
                <p><?= e($booking['service_name']) ?> · <?= e($routeLabel) ?></p>
              </div>

              <div class="invoice-history-card__meta" aria-label="Ringkasan invoice">
                <span>
                  <small>Tanggal</small>
                  <strong><?= e($booking['start_date']) ?><?= $booking['end_date'] ? ' - ' . e($booking['end_date']) : '' ?></strong>
                </span>
                <span>
                  <small>Total</small>
                  <strong><?= e(booking_format_money($booking['subtotal'])) ?></strong>
                </span>
                <span>
                  <small>Status</small>
                  <strong><?= e($booking['booking_status']) ?> · <?= e($booking['payment_status']) ?></strong>
                </span>
              </div>

              <div class="invoice-history-card__actions">
                <a class="btn btn--outline" href="<?= e($invoiceUrl) ?>">Lihat Invoice</a>
              </div>
            </article>
            <?php
        endforeach;
    }
}

if (!function_exists('booking_format_money')) {
    function booking_format_money($value)
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}
