<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/config.php';

$routes = require dirname(__DIR__) . '/routes.php';

$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$scriptName = $_SERVER['SCRIPT_NAME'] ?? '';

// Normalize script directory, e.g. "/OnlineExam" or "/OnlineExam/public"
$scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');

// App base path: remove "/public" suffix if it exists
$basePath = rtrim(preg_replace('#/public$#', '', $scriptDir), '/');

$path = $requestUri;

// Remove the app base path from the request URI
if ($basePath !== '' && strpos($requestUri, $basePath) === 0) {
    $path = substr($requestUri, strlen($basePath));
}

$path = trim($path, '/');

if (isset($routes[$path])) {
    $route = $routes[$path];
} else {
    http_response_code(404);
    echo '404 Not Found';
    exit;
}

// rest of your file stays the same:
$controllerName = $route['controller'];
$actionName = $route['action'];

if (!class_exists($controllerName)) {
    http_response_code(500);
    echo 'Controller not found';
    exit;
}

$controller = new $controllerName();

if (!method_exists($controller, $actionName)) {
    http_response_code(500);
    echo 'Action not found';
    exit;
}

call_user_func([$controller, $actionName]);