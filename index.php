<?php
declare(strict_types=1);

use App\Core\Database;
use App\Core\Router;

const BASE_PATH = __DIR__;

$config = require BASE_PATH . '/config.php';

date_default_timezone_set((string) ($config['app']['timezone'] ?? 'UTC'));

$displayErrors = (bool) ($config['error']['display'] ?? false);
$errorLogEnabled = (bool) ($config['error']['log'] ?? true);
$errorLogFile = (string) ($config['error']['log_file'] ?? BASE_PATH . '/storage/logs/php-error.log');

ini_set('display_errors', $displayErrors ? '1' : '0');
ini_set('log_errors', $errorLogEnabled ? '1' : '0');
ini_set('error_log', $errorLogFile);

error_reporting(E_ALL);

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    if (is_file($file)) {
        require $file;
    }
});

$database = Database::getInstance($config['database']);
$pdo = $database->getConnection();
unset($pdo);

$router = new Router();

$routesFile = BASE_PATH . '/routes/web.php';
if (!is_file($routesFile)) {
    throw new RuntimeException('Route file not found: ' . $routesFile);
}

require $routesFile;

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
