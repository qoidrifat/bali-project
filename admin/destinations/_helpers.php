<?php
require_once __DIR__ . '/../_auth.php';
require_once __DIR__ . '/../_helpers.php';

const ADMIN_DESTINATION_UPLOAD_DIR = __DIR__ . '/../../images/uploads/destinations';
const ADMIN_DESTINATION_UPLOAD_PREFIX = 'uploads/destinations/';
const ADMIN_DESTINATION_MAX_UPLOAD_BYTES = 3145728;

if (!function_exists('admin_destination_require')) {
    function admin_destination_require()
    {
        return require_admin('../../login.php', '../../');
    }
}

if (!function_exists('admin_destination_is_valid_id')) {
    function admin_destination_is_valid_id($value)
    {
        return is_string($value) || is_int($value)
            ? ctype_digit((string) $value) && (int) $value > 0
            : false;
    }
}

if (!function_exists('admin_destination_column_exists')) {
    function admin_destination_column_exists($connection, $column)
    {
        static $cache = [];
        $key = 'destination.' . $column;

        if (!preg_match('/^[A-Za-z0-9_]+$/', (string) $column)) {
            error_log('Destination column check rejected unsafe identifier: ' . $column);
            return false;
        }

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $columnName = mysqli_real_escape_string($connection, $column);
        $result = mysqli_query($connection, "SHOW COLUMNS FROM destination LIKE '{$columnName}'");

        if (!$result) {
            error_log('Destination column check failed: ' . mysqli_error($connection));
            $cache[$key] = false;
            return false;
        }

        $exists = mysqli_num_rows($result) > 0;
        mysqli_free_result($result);
        $cache[$key] = $exists;

        return $exists;
    }
}

if (!function_exists('admin_destination_schema')) {
    function admin_destination_schema($connection)
    {
        return [
            'has_is_active' => admin_destination_column_exists($connection, 'is_active'),
            'has_deleted_at' => admin_destination_column_exists($connection, 'deleted_at'),
            'has_created_at' => admin_destination_column_exists($connection, 'created_at'),
            'has_updated_at' => admin_destination_column_exists($connection, 'updated_at'),
        ];
    }
}

if (!function_exists('admin_destination_detail_html')) {
    function admin_destination_detail_html($text)
    {
        $safe = trim((string) $text);

        if ($safe === '') {
            return '<p>Deskripsi destinasi belum tersedia.</p>';
        }

        return '<p>' . nl2br(e($safe)) . '</p>';
    }
}

if (!function_exists('admin_destination_detail_text')) {
    function admin_destination_detail_text($html)
    {
        $withBreaks = preg_replace('/<\/(p|div|h2|h3|li)>/i', "\n", (string) $html);
        $text = html_entity_decode(strip_tags($withBreaks), ENT_QUOTES, 'UTF-8');
        $text = preg_replace("/\n{3,}/", "\n\n", $text);

        return trim($text);
    }
}

if (!function_exists('admin_destination_validate_upload')) {
    function admin_destination_validate_upload($file, $required = false)
    {
        if (!isset($file) || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $required ? 'Gambar utama wajib diupload.' : null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            return 'Upload gambar gagal. Silakan pilih file lain.';
        }

        if (($file['size'] ?? 0) <= 0 || $file['size'] > ADMIN_DESTINATION_MAX_UPLOAD_BYTES) {
            return 'Ukuran gambar maksimal 3 MB.';
        }

        $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowedExtensions, true)) {
            return 'Format gambar harus JPG, PNG, atau WebP.';
        }

        $allowedMime = [
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'webp' => ['image/webp'],
        ];

        $mime = null;

        if (function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = $finfo ? finfo_file($finfo, $file['tmp_name']) : null;
            if ($finfo) {
                finfo_close($finfo);
            }
        }

        if (!$mime && function_exists('getimagesize')) {
            $imageInfo = getimagesize($file['tmp_name']);
            $mime = $imageInfo['mime'] ?? null;
        }

        if (!$mime || !in_array($mime, $allowedMime[$extension], true)) {
            return 'Tipe file gambar tidak sesuai dengan ekstensi.';
        }

        return null;
    }
}

if (!function_exists('admin_destination_store_upload')) {
    function admin_destination_store_upload($file, &$errors)
    {
        $error = admin_destination_validate_upload($file, false);

        if ($error) {
            $errors[] = $error;
            return null;
        }

        if (!isset($file) || !is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (!is_dir(ADMIN_DESTINATION_UPLOAD_DIR) && !mkdir(ADMIN_DESTINATION_UPLOAD_DIR, 0755, true)) {
            $errors[] = 'Folder upload belum tersedia.';
            return null;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $filename = 'destination-' . date('Ymd-His') . '-' . bin2hex(random_bytes(5)) . '.' . $extension;
        $target = ADMIN_DESTINATION_UPLOAD_DIR . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            $errors[] = 'Gambar belum bisa disimpan.';
            return null;
        }

        return ADMIN_DESTINATION_UPLOAD_PREFIX . $filename;
    }
}

if (!function_exists('admin_destination_uploaded_files')) {
    function admin_destination_uploaded_files($files)
    {
        if (!is_array($files) || !isset($files['name']) || !is_array($files['name'])) {
            return [];
        }

        $normalized = [];
        $count = count($files['name']);

        for ($i = 0; $i < $count; $i++) {
            $normalized[] = [
                'name' => $files['name'][$i] ?? '',
                'type' => $files['type'][$i] ?? '',
                'tmp_name' => $files['tmp_name'][$i] ?? '',
                'error' => $files['error'][$i] ?? UPLOAD_ERR_NO_FILE,
                'size' => $files['size'][$i] ?? 0,
            ];
        }

        return $normalized;
    }
}

if (!function_exists('admin_destination_insert_gallery_image')) {
    function admin_destination_insert_gallery_image($connection, $detailId, $imagePath)
    {
        $stmt = mysqli_prepare($connection, 'INSERT INTO detail_image (id_detail, gambar) VALUES (?, ?)');

        if (!$stmt) {
            error_log('Destination gallery insert prepare failed: ' . mysqli_error($connection));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'is', $detailId, $imagePath);
        $ok = mysqli_stmt_execute($stmt);

        if (!$ok) {
            error_log('Destination gallery insert failed: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);

        return $ok;
    }
}

if (!function_exists('admin_destination_fetch')) {
    function admin_destination_fetch($connection, $id)
    {
        $schema = admin_destination_schema($connection);
        $select = 'd.id_des, d.nama_des, d.gambar, detail.id_detail, detail.`desc` AS detail_desc';
        $select .= $schema['has_is_active'] ? ', d.is_active' : ', 1 AS is_active';
        $select .= $schema['has_deleted_at'] ? ', d.deleted_at' : ', NULL AS deleted_at';

        $stmt = mysqli_prepare($connection, "SELECT {$select} FROM destination d LEFT JOIN detail ON detail.id_des = d.id_des WHERE d.id_des = ? LIMIT 1");

        if (!$stmt) {
            error_log('Destination fetch prepare failed: ' . mysqli_error($connection));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $id);

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Destination fetch execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $row = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        return $row ?: null;
    }
}

if (!function_exists('admin_destination_insert_detail')) {
    function admin_destination_insert_detail($connection, $destinationId, $detailHtml)
    {
        $stmt = mysqli_prepare($connection, 'INSERT INTO detail (id_des, `desc`) VALUES (?, ?)');

        if (!$stmt) {
            error_log('Destination detail insert prepare failed: ' . mysqli_error($connection));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'is', $destinationId, $detailHtml);
        $ok = mysqli_stmt_execute($stmt);

        if (!$ok) {
            error_log('Destination detail insert failed: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);

        return $ok;
    }
}

if (!function_exists('admin_destination_upsert_detail')) {
    function admin_destination_upsert_detail($connection, $destinationId, $detailId, $detailHtml)
    {
        if ($detailId) {
            $stmt = mysqli_prepare($connection, 'UPDATE detail SET `desc` = ? WHERE id_detail = ? AND id_des = ?');

            if (!$stmt) {
                error_log('Destination detail update prepare failed: ' . mysqli_error($connection));
                return false;
            }

            mysqli_stmt_bind_param($stmt, 'sii', $detailHtml, $detailId, $destinationId);
            $ok = mysqli_stmt_execute($stmt);

            if (!$ok) {
                error_log('Destination detail update failed: ' . mysqli_stmt_error($stmt));
            }

            mysqli_stmt_close($stmt);
            return $ok;
        }

        return admin_destination_insert_detail($connection, $destinationId, $detailHtml);
    }
}
