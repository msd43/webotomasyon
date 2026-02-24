<?php
declare(strict_types=1);

return [
    'app' => [
        'name' => 'MasterVault',
        'env' => $_ENV['APP_ENV'] ?? 'development',
        'debug' => filter_var($_ENV['APP_DEBUG'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'url' => $_ENV['APP_URL'] ?? 'http://localhost',
        'timezone' => $_ENV['APP_TIMEZONE'] ?? 'Europe/Istanbul',
    ],

    'database' => [
        'driver' => 'mysql',
        'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
        'port' => (int) ($_ENV['DB_PORT'] ?? 3306),
        'name' => $_ENV['DB_NAME'] ?? 'mastervault',
        'username' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASS'] ?? '',
        'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4',
        'collation' => $_ENV['DB_COLLATION'] ?? 'utf8mb4_unicode_ci',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],

    'error' => [
        'display' => filter_var($_ENV['ERROR_DISPLAY'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'log' => filter_var($_ENV['ERROR_LOG'] ?? true, FILTER_VALIDATE_BOOLEAN),
        'log_file' => __DIR__ . '/storage/logs/php-error.log',
    ],
];
