<?php
declare(strict_types=1);

session_start();

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('BASE_PATH', dirname(__FILE__));

define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'online_exam');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

spl_autoload_register(function (string $class): void {
    $paths = [
        BASE_PATH . '/controller/' . $class . '.php',
        BASE_PATH . '/model/' . $class . '.php',
    ];

    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

function base_url(string $path = ''): string
{
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

    // Normalize script directory
    $scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

    // App base path: remove possible "/public" suffix
    $baseDir = rtrim(preg_replace('#/public$#', '', $scriptDir), '/');

    $base = $scheme . '://' . $host . ($baseDir ? $baseDir : '') . '/';

    return $base . ltrim($path, '/');
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function sanitize(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

