<?php

return [
    'host' => getenv('BALI_DB_HOST') ?: 'localhost',
    'user' => getenv('BALI_DB_USER') ?: 'root',
    'password' => getenv('BALI_DB_PASSWORD') ?: '',
    'database' => getenv('BALI_DB_NAME') ?: 'bali',
];
