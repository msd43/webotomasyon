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

    $createTableSql = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role ENUM('admin', 'client') NOT NULL DEFAULT 'client',
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(190) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    status TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    $pdo->exec($createTableSql);

    $plainPassword = '123456';
    if (defined('PASSWORD_ARGON2ID')) {
        $passwordHash = password_hash($plainPassword, PASSWORD_ARGON2ID);
    } else {
        $passwordHash = password_hash($plainPassword, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    if ($passwordHash === false) {
        throw new RuntimeException('Password hashing failed for default super admin user.');
    }

    $insertSql = <<<SQL
INSERT IGNORE INTO users (role, first_name, last_name, email, password_hash, status, created_at, updated_at)
VALUES (:role, :first_name, :last_name, :email, :password_hash, :status, NOW(), NOW());
SQL;

    $stmt = $pdo->prepare($insertSql);
    $stmt->execute([
        ':role' => 'admin',
        ':first_name' => 'Super',
        ':last_name' => 'Admin',
        ':email' => 'admin@mastervault.com',
        ':password_hash' => $passwordHash,
        ':status' => 1,
    ]);

    fwrite(STDOUT, "[OK] users table is ready and default super admin ensured.\n");
    fwrite(STDOUT, "[INFO] Default admin email: admin@mastervault.com\n");
    fwrite(STDOUT, "[INFO] Default admin password: 123456\n");
} catch (Throwable $exception) {
    fwrite(STDERR, '[ERROR] Migration failed: ' . $exception->getMessage() . "\n");
    exit(1);
}
