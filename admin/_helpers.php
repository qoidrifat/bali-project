<?php

if (!function_exists('admin_table_exists')) {
    function admin_table_exists($connection, $table)
    {
        static $cache = [];

        if (isset($cache[$table])) {
            return $cache[$table];
        }

        $tableName = mysqli_real_escape_string($connection, $table);
        $result = mysqli_query($connection, "SHOW TABLES LIKE '{$tableName}'");

        if (!$result) {
            error_log('Admin table check failed for ' . $table . ': ' . mysqli_error($connection));
            $cache[$table] = false;
            return false;
        }

        $exists = mysqli_num_rows($result) > 0;
        mysqli_free_result($result);
        $cache[$table] = $exists;

        return $exists;
    }
}

if (!function_exists('admin_count_table')) {
    function admin_count_table($connection, $table, array $allowedTables)
    {
        if (!in_array($table, $allowedTables, true)) {
            error_log('Admin count rejected unknown table: ' . $table);
            return null;
        }

        if (!admin_table_exists($connection, $table)) {
            return null;
        }

        $sql = 'SELECT COUNT(*) AS total FROM `' . $table . '`';
        $result = mysqli_query($connection, $sql);

        if (!$result) {
            error_log('Admin count failed for ' . $table . ': ' . mysqli_error($connection));
            return null;
        }

        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);

        return (int) ($row['total'] ?? 0);
    }
}

if (!function_exists('admin_sum_tables')) {
    function admin_sum_tables($connection, array $tables, array $allowedTables)
    {
        $total = 0;
        $available = 0;
        $missing = [];

        foreach ($tables as $table) {
            $count = admin_count_table($connection, $table, $allowedTables);

            if ($count === null) {
                $missing[] = $table;
                continue;
            }

            $available++;
            $total += $count;
        }

        return [
            'total' => $total,
            'available' => $available,
            'missing' => $missing,
        ];
    }
}

if (!function_exists('admin_format_number')) {
    function admin_format_number($value)
    {
        return number_format((int) $value, 0, ',', '.');
    }
}

if (!function_exists('admin_format_money')) {
    function admin_format_money($value)
    {
        return 'Rp ' . number_format((float) $value, 0, ',', '.');
    }
}

if (!function_exists('admin_fetch_all')) {
    function admin_fetch_all($result)
    {
        $rows = [];

        while ($result && ($row = mysqli_fetch_assoc($result))) {
            $rows[] = $row;
        }

        if ($result) {
            mysqli_free_result($result);
        }

        return $rows;
    }
}

if (!function_exists('admin_normalize_status')) {
    function admin_normalize_status($value, array $allowed, $fallback)
    {
        $value = strtolower(trim((string) $value));

        return in_array($value, $allowed, true) ? $value : $fallback;
    }
}

if (!function_exists('admin_badge_class')) {
    function admin_badge_class($status)
    {
        $status = strtolower((string) $status);

        if (in_array($status, ['active', 'confirmed', 'paid', 'published', 'read', 'replied'], true)) {
            return 'admin-badge--active';
        }

        if (in_array($status, ['pending', 'manual_review', 'unpaid', 'draft', 'new', 'unread'], true)) {
            return 'admin-badge--pending';
        }

        if (in_array($status, ['cancelled', 'archived', 'inactive', 'disabled'], true)) {
            return 'admin-badge--muted';
        }

        return 'admin-badge--neutral';
    }
}

if (!function_exists('admin_icon_path')) {
    function admin_icon_path($name)
    {
        $icons = [
            'admin' => '<path d="M12 3l7 4v5c0 4.4-2.8 7.4-7 9-4.2-1.6-7-4.6-7-9V7z"/><path d="M9 13l2 2 4-5"/>',
            'dashboard' => '<rect x="3" y="3" width="7" height="7" rx="2"/><rect x="14" y="3" width="7" height="7" rx="2"/><rect x="3" y="14" width="7" height="7" rx="2"/><rect x="14" y="14" width="7" height="7" rx="2"/>',
            'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
            'roles' => '<path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z"/><path d="M9 12l2 2 4-5"/>',
            'destinations' => '<path d="M12 21s7-5.2 7-11a7 7 0 1 0-14 0c0 5.8 7 11 7 11z"/><circle cx="12" cy="10" r="2.5"/>',
            'categories' => '<path d="M4 5h6v6H4z"/><path d="M14 5h6v6h-6z"/><path d="M4 15h6v4H4z"/><path d="M14 15h6v4h-6z"/>',
            'tickets' => '<path d="M4 7a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v3a2 2 0 0 0 0 4v3a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-3a2 2 0 0 0 0-4z"/><path d="M9 9h6"/><path d="M9 15h6"/>',
            'bookings' => '<rect x="4" y="5" width="16" height="16" rx="2"/><path d="M16 3v4"/><path d="M8 3v4"/><path d="M4 11h16"/><path d="M9 16l2 2 4-4"/>',
            'invoices' => '<path d="M6 2h9l5 5v15H6z"/><path d="M14 2v6h6"/><path d="M9 13h6"/><path d="M9 17h4"/>',
            'payments' => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M3 10h18"/><path d="M7 15h4"/>',
            'gallery' => '<rect x="3" y="5" width="18" height="14" rx="3"/><circle cx="8.5" cy="10.5" r="1.5"/><path d="M21 16l-5-5-4 4-2-2-5 5"/>',
            'articles' => '<path d="M5 3h10l4 4v14H5z"/><path d="M14 3v5h5"/><path d="M8 13h8"/><path d="M8 17h6"/>',
            'messages' => '<rect x="3" y="5" width="18" height="14" rx="3"/><path d="M4 7l8 6 8-6"/>',
            'settings' => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 0 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6V21a2 2 0 0 1-4 0v-.1a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 0 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.6-1H3a2 2 0 0 1 0-4h.1a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1A2 2 0 0 1 7.1 4l.1.1a1.7 1.7 0 0 0 1.9.3h.1a1.7 1.7 0 0 0 1-1.6V3a2 2 0 0 1 4 0v.1a1.7 1.7 0 0 0 1 1.6h.1a1.7 1.7 0 0 0 1.9-.3l.1-.1A2 2 0 0 1 20 7.1l-.1.1a1.7 1.7 0 0 0-.3 1.9v.1a1.7 1.7 0 0 0 1.6 1H21a2 2 0 0 1 0 4h-.1a1.7 1.7 0 0 0-1.5.8z"/>',
            'reports' => '<path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 16v-5"/><path d="M12 16V8"/><path d="M16 16v-7"/>',
            'activity' => '<path d="M3 12h4l3 8 4-16 3 8h4"/>',
        ];

        return $icons[$name] ?? $icons['dashboard'];
    }
}

if (!function_exists('admin_icon')) {
    function admin_icon($name, $class = 'admin-ui-icon')
    {
        $class = preg_replace('/[^A-Za-z0-9_\\-\\s]/', '', (string) $class);

        return '<svg class="' . trim($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . admin_icon_path($name) . '</svg>';
    }
}

if (!function_exists('admin_query_value')) {
    function admin_query_value($connection, $sql)
    {
        $result = mysqli_query($connection, $sql);

        if (!$result) {
            error_log('Admin value query failed: ' . mysqli_error($connection));
            return null;
        }

        $row = mysqli_fetch_row($result);
        mysqli_free_result($result);

        return $row ? $row[0] : null;
    }
}

if (!function_exists('admin_excerpt')) {
    function admin_excerpt($value, $length = 120)
    {
        $text = trim((string) $value);

        if ($text === '') {
            return '';
        }

        if (function_exists('mb_strimwidth')) {
            return mb_strimwidth($text, 0, $length, '...', 'UTF-8');
        }

        return strlen($text) > $length ? substr($text, 0, max(0, $length - 3)) . '...' : $text;
    }
}

if (!function_exists('admin_slugify')) {
    function admin_slugify($value)
    {
        $value = strtolower(trim((string) $value));
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value);
        $value = trim((string) $value, '-');

        return $value !== '' ? $value : 'item-' . date('YmdHis');
    }
}

if (!function_exists('admin_redirect')) {
    function admin_redirect($url)
    {
        header('Location: ' . $url);
        exit;
    }
}

if (!function_exists('admin_flash_message')) {
    function admin_flash_message($notice)
    {
        $messages = [
            'created' => 'Data berhasil dibuat.',
            'updated' => 'Data berhasil diperbarui.',
            'status-updated' => 'Status berhasil diperbarui.',
            'payment-updated' => 'Status pembayaran berhasil diperbarui.',
            'settings-saved' => 'Pengaturan berhasil disimpan.',
            'export-ready' => 'Export siap diproses.',
        ];

        return $messages[$notice] ?? '';
    }
}

if (!function_exists('admin_log_activity')) {
    function admin_log_activity($connection, $action, $entityType = null, $entityId = null, $description = null)
    {
        if (!$connection || !admin_table_exists($connection, 'admin_activity_logs')) {
            return false;
        }

        $user = function_exists('auth_user') ? auth_user() : null;
        $adminUserId = $user && isset($user['id']) ? (int) $user['id'] : null;
        $action = substr(trim((string) $action), 0, 120);
        $entityType = $entityType !== null ? substr(trim((string) $entityType), 0, 80) : null;
        $entityId = $entityId !== null ? substr(trim((string) $entityId), 0, 80) : null;
        $description = $description !== null ? trim((string) $description) : null;
        $ipAddress = substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45);

        if ($action === '') {
            return false;
        }

        $stmt = mysqli_prepare(
            $connection,
            'INSERT INTO admin_activity_logs (admin_user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, ?, ?, ?, ?, ?)'
        );

        if (!$stmt) {
            error_log('Admin activity log prepare failed: ' . mysqli_error($connection));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'isssss', $adminUserId, $action, $entityType, $entityId, $description, $ipAddress);
        $ok = mysqli_stmt_execute($stmt);

        if (!$ok) {
            error_log('Admin activity log execute failed: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);

        return $ok;
    }
}

if (!function_exists('admin_output_csv')) {
    function admin_output_csv($filename, array $headers, array $rows)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . preg_replace('/[^A-Za-z0-9_.-]/', '-', $filename) . '"');
        header('X-Content-Type-Options: nosniff');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);

        foreach ($rows as $row) {
            fputcsv($output, $row);
        }

        fclose($output);
        exit;
    }
}
