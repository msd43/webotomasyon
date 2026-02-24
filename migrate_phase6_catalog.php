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

    $pdo->beginTransaction();

    $productGroupsSql = <<<SQL
CREATE TABLE IF NOT EXISTS product_groups (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(190) NOT NULL,
    slug VARCHAR(190) NOT NULL,
    description TEXT DEFAULT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_product_groups_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    $productsSql = <<<SQL
CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    group_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(190) NOT NULL,
    slug VARCHAR(190) NOT NULL,
    description TEXT DEFAULT NULL,
    type ENUM('hosting', 'server', 'license', 'general') NOT NULL DEFAULT 'general',
    module VARCHAR(120) DEFAULT NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    setup_fee DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    billing_cycle ENUM('monthly', 'annually', 'one-time', 'free') NOT NULL DEFAULT 'monthly',
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_products_slug (slug),
    KEY idx_products_group_id (group_id),
    CONSTRAINT fk_products_group_id FOREIGN KEY (group_id)
        REFERENCES product_groups(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    $pdo->exec($productGroupsSql);
    $pdo->exec($productsSql);

    $pdo->commit();

    fwrite(STDOUT, "[OK] product_groups and products tables are ready.\n");
} catch (Throwable $exception) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    fwrite(STDERR, '[ERROR] Migration failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
