<?php
declare(strict_types=1);

$configFile = __DIR__ . '/config.php';

if (!is_file($configFile)) {
    fwrite(STDERR, "[ERROR] config.php not found.\n");
    exit(1);
}

$config = require $configFile;
$dbConfig = $config['database'] ?? [];

$host = (string) ($dbConfig['host'] ?? '127.0.0.1');
$port = (int) ($dbConfig['port'] ?? 3306);
$dbName = (string) ($dbConfig['name'] ?? 'mastervault');
$username = (string) ($dbConfig['username'] ?? 'root');
$password = (string) ($dbConfig['password'] ?? '');
$charset = (string) ($dbConfig['charset'] ?? 'utf8mb4');
$options = $dbConfig['options'] ?? [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbName, $charset);

try {
    $pdo = new PDO($dsn, $username, $password, $options);

    $sql = <<<SQL
CREATE TABLE IF NOT EXISTS clients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    phone VARCHAR(30) DEFAULT NULL,
    company VARCHAR(190) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    city VARCHAR(120) DEFAULT NULL,
    country VARCHAR(120) DEFAULT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_clients_email (email),
    KEY idx_clients_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    $pdo->exec($sql);

    fwrite(STDOUT, "[OK] clients table is ready.\n");
} catch (Throwable $exception) {
    fwrite(STDERR, '[ERROR] Migration failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
