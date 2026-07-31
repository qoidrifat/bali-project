<?php

require_once __DIR__ . '/database.php';

if (!function_exists('auth_start_session')) {
    function auth_start_session()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');

        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        auth_start_session();

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
    }
}

if (!function_exists('verify_csrf_token')) {
    function verify_csrf_token($token)
    {
        auth_start_session();

        return is_string($token)
            && isset($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}

if (!function_exists('auth_user')) {
    function auth_user()
    {
        auth_start_session();

        return $_SESSION['user'] ?? null;
    }
}

if (!function_exists('auth_check')) {
    function auth_check()
    {
        return auth_user() !== null;
    }
}

if (!function_exists('auth_has_role')) {
    function auth_has_role($role)
    {
        $user = auth_user();

        return $user && isset($user['role']) && hash_equals((string) $role, (string) $user['role']);
    }
}

if (!function_exists('auth_require_login')) {
    function auth_require_login($redirect = 'login.php')
    {
        if (!auth_check()) {
            header('Location: ' . $redirect);
            exit;
        }
    }
}

if (!function_exists('auth_require_role')) {
    function auth_require_role($role, $redirect = 'login.php')
    {
        auth_require_login($redirect);

        if (!auth_has_role($role)) {
            http_response_code(403);
            echo 'Akses tidak diizinkan.';
            exit;
        }
    }
}

if (!function_exists('auth_login')) {
    function auth_login(array $user)
    {
        auth_start_session();
        session_regenerate_id(true);

        $_SESSION['user'] = [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role_name'] ?? 'user',
        ];
    }
}

if (!function_exists('auth_logout')) {
    function auth_logout()
    {
        auth_start_session();

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }

        session_destroy();
    }
}

if (!function_exists('auth_schema_ready')) {
    function auth_schema_ready($connection)
    {
        foreach (['roles', 'users'] as $table) {
            $tableName = mysqli_real_escape_string($connection, $table);
            $result = mysqli_query($connection, "SHOW TABLES LIKE '{$tableName}'");

            if (!$result) {
                error_log('Auth schema check failed: ' . mysqli_error($connection));
                return false;
            }

            $exists = mysqli_num_rows($result) > 0;
            mysqli_free_result($result);

            if (!$exists) {
                return false;
            }
        }

        $requiredColumns = [
            'roles' => ['id', 'name', 'label'],
            'users' => ['id', 'role_id', 'name', 'email', 'password_hash'],
        ];

        foreach ($requiredColumns as $table => $columns) {
            $tableName = mysqli_real_escape_string($connection, $table);
            $result = mysqli_query($connection, "SHOW COLUMNS FROM `{$tableName}`");

            if (!$result) {
                error_log('Auth column check failed: ' . mysqli_error($connection));
                return false;
            }

            $existingColumns = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $existingColumns[] = $row['Field'];
            }

            mysqli_free_result($result);

            if (array_diff($columns, $existingColumns)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('auth_find_user_by_email')) {
    function auth_find_user_by_email($connection, $email)
    {
        $sql = "SELECT users.id, users.name, users.email, users.password_hash, roles.name AS role_name
                FROM users
                LEFT JOIN roles ON roles.id = users.role_id
                WHERE users.email = ?
                LIMIT 1";

        $stmt = mysqli_prepare($connection, $sql);

        if (!$stmt) {
            error_log('Auth user lookup prepare failed: ' . mysqli_error($connection));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 's', $email);

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Auth user lookup execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $user = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        return $user ?: null;
    }
}

if (!function_exists('auth_find_user_by_id')) {
    function auth_find_user_by_id($connection, $id)
    {
        if (!is_valid_positive_id($id)) {
            return null;
        }

        $userId = (int) $id;
        $sql = "SELECT users.id, users.role_id, users.name, users.email, users.password_hash,
                       users.phone, users.city, users.country, users.birth_date,
                       users.preferred_contact, users.travel_style, users.bio,
                       roles.name AS role_name, roles.label AS role_label
                FROM users
                LEFT JOIN roles ON roles.id = users.role_id
                WHERE users.id = ?
                LIMIT 1";
        $stmt = mysqli_prepare($connection, $sql);

        if (!$stmt) {
            error_log('Auth user profile lookup prepare failed: ' . mysqli_error($connection));
            return null;
        }

        mysqli_stmt_bind_param($stmt, 'i', $userId);

        if (!mysqli_stmt_execute($stmt)) {
            error_log('Auth user profile lookup execute failed: ' . mysqli_stmt_error($stmt));
            mysqli_stmt_close($stmt);
            return null;
        }

        $result = mysqli_stmt_get_result($stmt);
        $user = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        return $user ?: null;
    }
}

if (!function_exists('auth_profile_schema_ready')) {
    function auth_profile_schema_ready($connection)
    {
        if (!auth_schema_ready($connection)) {
            return false;
        }

        $requiredColumns = ['phone', 'city', 'country', 'birth_date', 'preferred_contact', 'travel_style', 'bio'];
        foreach ($requiredColumns as $column) {
            if (!db_column_exists($connection, 'users', $column)) {
                return false;
            }
        }

        return true;
    }
}

if (!function_exists('auth_update_profile')) {
    function auth_update_profile($connection, $id, array $data)
    {
        if (!is_valid_positive_id($id)) {
            return false;
        }

        $userId = (int) $id;
        $stmt = mysqli_prepare(
            $connection,
            "UPDATE users
             SET name = ?, email = ?, phone = ?, city = ?, country = ?, birth_date = ?, preferred_contact = ?, travel_style = ?, bio = ?
             WHERE id = ?
             LIMIT 1"
        );

        if (!$stmt) {
            error_log('Auth profile update prepare failed: ' . mysqli_error($connection));
            return false;
        }

        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'] !== '' ? $data['phone'] : null;
        $city = $data['city'] !== '' ? $data['city'] : null;
        $country = $data['country'] !== '' ? $data['country'] : null;
        $birthDate = $data['birth_date'] !== '' ? $data['birth_date'] : null;
        $preferredContact = $data['preferred_contact'] !== '' ? $data['preferred_contact'] : null;
        $travelStyle = $data['travel_style'] !== '' ? $data['travel_style'] : null;
        $bio = $data['bio'] !== '' ? $data['bio'] : null;

        mysqli_stmt_bind_param(
            $stmt,
            'sssssssssi',
            $name,
            $email,
            $phone,
            $city,
            $country,
            $birthDate,
            $preferredContact,
            $travelStyle,
            $bio,
            $userId
        );

        $ok = mysqli_stmt_execute($stmt);

        if (!$ok) {
            error_log('Auth profile update failed: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);

        return $ok;
    }
}

if (!function_exists('auth_update_password')) {
    function auth_update_password($connection, $id, $password)
    {
        if (!is_valid_positive_id($id)) {
            return false;
        }

        $userId = (int) $id;
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($connection, 'UPDATE users SET password_hash = ? WHERE id = ? LIMIT 1');

        if (!$stmt) {
            error_log('Auth password update prepare failed: ' . mysqli_error($connection));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'si', $passwordHash, $userId);
        $ok = mysqli_stmt_execute($stmt);

        if (!$ok) {
            error_log('Auth password update failed: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);

        return $ok;
    }
}

if (!function_exists('auth_register_user')) {
    function auth_register_user($connection, $name, $email, $password)
    {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $roleName = 'user';

        $roleStmt = mysqli_prepare($connection, "SELECT id FROM roles WHERE name = ? LIMIT 1");

        if (!$roleStmt) {
            error_log('Auth role lookup prepare failed: ' . mysqli_error($connection));
            return false;
        }

        mysqli_stmt_bind_param($roleStmt, 's', $roleName);

        if (!mysqli_stmt_execute($roleStmt)) {
            error_log('Auth role lookup execute failed: ' . mysqli_stmt_error($roleStmt));
            mysqli_stmt_close($roleStmt);
            return false;
        }

        $roleResult = mysqli_stmt_get_result($roleStmt);
        $role = $roleResult ? mysqli_fetch_assoc($roleResult) : null;
        mysqli_stmt_close($roleStmt);

        $roleId = (int) ($role['id'] ?? 2);
        $stmt = mysqli_prepare($connection, "INSERT INTO users (role_id, name, email, password_hash) VALUES (?, ?, ?, ?)");

        if (!$stmt) {
            error_log('Auth register prepare failed: ' . mysqli_error($connection));
            return false;
        }

        mysqli_stmt_bind_param($stmt, 'isss', $roleId, $name, $email, $passwordHash);
        $ok = mysqli_stmt_execute($stmt);

        if (!$ok) {
            error_log('Auth register execute failed: ' . mysqli_stmt_error($stmt));
        }

        mysqli_stmt_close($stmt);

        return $ok;
    }
}
