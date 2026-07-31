<?php

/*
|--------------------------------------------------------------------------
| Bali Project Production Configuration Example
|--------------------------------------------------------------------------
|
| Copy the values below into your hosting control panel environment variables
| or adapt them into a private config outside the public web root. Do not put
| real production credentials in this example file.
|
| Required environment variables used by config/database.php:
|
| BALI_DB_HOST=localhost
| BALI_DB_USER=your_database_user
| BALI_DB_PASSWORD=your_database_password
| BALI_DB_NAME=your_database_name
|
| Production PHP settings:
|
| display_errors=Off
| log_errors=On
|
*/

return [
    'database' => [
        'host_env' => 'BALI_DB_HOST',
        'user_env' => 'BALI_DB_USER',
        'password_env' => 'BALI_DB_PASSWORD',
        'name_env' => 'BALI_DB_NAME',
    ],
    'production' => [
        'display_errors' => false,
        'publish_sql_dumps' => false,
        'publish_archive_folder' => false,
        'backup_database_before_import' => true,
    ],
];
