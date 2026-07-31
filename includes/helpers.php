<?php

if (!function_exists('e')) {
    function e($value)
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('safe_external_url')) {
    function safe_external_url($url)
    {
        $url = trim((string) $url);

        if ($url === '') {
            return '#';
        }

        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');

        if (!in_array($scheme, ['http', 'https'], true)) {
            return '#';
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : '#';
    }
}

if (!function_exists('sanitize_html_fragment')) {
    function sanitize_html_fragment($html)
    {
        $html = (string) $html;
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|meta|link)\b[^>]*>.*?</\1>#is', '', $html);
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button|meta|link)\b[^>]*\/?>#is', '', $html);
        $html = preg_replace('/\s+on[a-z]+\s*=\s*(".*?"|\'.*?\'|[^\s>]+)/is', '', $html);
        $html = preg_replace('/\s+(href|src)\s*=\s*([\'"])\s*(javascript:|data:).*?\2/is', '', $html);

        $allowedTags = '<p><div><span><br><strong><b><em><i><ul><ol><li><h2><h3><h4><blockquote><a>';

        return strip_tags($html, $allowedTags);
    }
}

if (!function_exists('public_image_path')) {
    function public_image_path($image)
    {
        $image = str_replace('\\', '/', trim((string) $image));
        $image = preg_replace('#^/?images/#', '', $image);
        $image = ltrim($image, '/');

        if ($image === '' || strpos($image, '..') !== false || preg_match('/[\x00-\x1F]/', $image)) {
            return 'images/logo.png';
        }

        $extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (!in_array($extension, $allowed, true)) {
            return 'images/logo.png';
        }

        $relativeDir = trim(str_replace('\\', '/', dirname($image)), '.');
        $baseName = pathinfo($image, PATHINFO_FILENAME);
        $optimizedRelative = ($relativeDir ? $relativeDir . '/' : '') . $baseName . '.webp';
        $optimizedFile = __DIR__ . '/../images/optimized/' . $optimizedRelative;

        if (is_file($optimizedFile)) {
            return 'images/optimized/' . $optimizedRelative;
        }

        return 'images/' . $image;
    }
}

if (!function_exists('image_dimensions_attr')) {
    function image_dimensions_attr($publicPath)
    {
        $publicPath = str_replace('\\', '/', trim((string) $publicPath));

        if ($publicPath === '' || strpos($publicPath, '..') !== false || preg_match('/[\x00-\x1F]/', $publicPath)) {
            return '';
        }

        $file = __DIR__ . '/../' . ltrim($publicPath, '/');

        if (!is_file($file)) {
            return '';
        }

        $size = @getimagesize($file);

        if (!$size || empty($size[0]) || empty($size[1])) {
            return '';
        }

        return ' width="' . (int) $size[0] . '" height="' . (int) $size[1] . '"';
    }
}

if (!function_exists('is_valid_date_value')) {
    function is_valid_date_value($value)
    {
        $date = DateTime::createFromFormat('Y-m-d', (string) $value);
        return $date && $date->format('Y-m-d') === $value;
    }
}

if (!function_exists('is_valid_positive_id')) {
    function is_valid_positive_id($value)
    {
        return ctype_digit((string) $value) && (int) $value > 0;
    }
}

if (!function_exists('db_column_exists')) {
    function db_column_exists($connection, $table, $column)
    {
        static $cache = [];
        $key = $table . '.' . $column;

        if (!preg_match('/^[A-Za-z0-9_]+$/', (string) $table) || !preg_match('/^[A-Za-z0-9_]+$/', (string) $column)) {
            error_log('Column check rejected unsafe identifier: ' . $key);
            return false;
        }

        if (isset($cache[$key])) {
            return $cache[$key];
        }

        $tableName = mysqli_real_escape_string($connection, (string) $table);
        $columnName = mysqli_real_escape_string($connection, (string) $column);
        $result = mysqli_query($connection, "SHOW COLUMNS FROM `{$tableName}` LIKE '{$columnName}'");

        if (!$result) {
            error_log('Column check failed for ' . $key . ': ' . mysqli_error($connection));
            $cache[$key] = false;
            return false;
        }

        $exists = mysqli_num_rows($result) > 0;
        mysqli_free_result($result);
        $cache[$key] = $exists;

        return $exists;
    }
}
