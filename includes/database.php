<?php

require_once __DIR__ . '/helpers.php';

if (!function_exists('db_config')) {
    function db_config()
    {
        static $config = null;

        if ($config === null) {
            $config = require __DIR__ . '/../config/database.php';
        }

        return $config;
    }
}

if (!function_exists('db_connect')) {
    function db_connect()
    {
        $config = db_config();

        $connection = mysqli_connect(
            $config['host'],
            $config['user'],
            $config['password'],
            $config['database']
        );

        if (!$connection) {
            error_log('Database connection failed: ' . mysqli_connect_error());
            return false;
        }

        mysqli_set_charset($connection, 'utf8mb4');

        return $connection;
    }
}
