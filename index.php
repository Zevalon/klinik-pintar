<?php
session_start();

define('BASE_PATH', __DIR__);
define('APP_PATH', BASE_PATH . '/application');
define('STORAGE_PATH', BASE_PATH . '/storage');

require APP_PATH . '/config/config.php';
require APP_PATH . '/helpers/common_helper.php';
require APP_PATH . '/helpers/url_helper.php';
require APP_PATH . '/libraries/DB.php';
require APP_PATH . '/core/Model.php';
require APP_PATH . '/core/Controller.php';
require APP_PATH . '/libraries/Auth.php';

spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/controllers/' . $class . '.php',
        APP_PATH . '/models/' . $class . '.php',
        APP_PATH . '/libraries/' . $class . '.php',
    ];
    foreach ($paths as $file) {
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$scriptDir = rtrim(str_replace('\\', '/', dirname($scriptName)), '/');
$scriptBase = $scriptDir === '/' ? '' : $scriptDir;

if ($scriptBase && strpos($uri, $scriptBase) === 0) {
    $uri = substr($uri, strlen($scriptBase));
}

$uri = preg_replace('~^/?index\.php/?~', '', $uri);
$uri = trim($uri, '/');
$segments = $uri === '' ? [] : explode('/', $uri);
if (!empty($segments) && strtolower($segments[0]) === 'index.php') {
    array_shift($segments);
}
$routeKey = implode('/', $segments);

$routes = require APP_PATH . '/config/routes.php';

if (isset($routes[$routeKey])) {
    [$controllerName, $method] = explode('@', $routes[$routeKey]);
    $params = [];
} else {
    $controllerName = !empty($segments[0]) ? ucfirst($segments[0]) : 'Dashboard';
    $method = $segments[1] ?? 'index';
    $params = array_slice($segments, 2);
}

if (!class_exists($controllerName)) {
    http_response_code(404);
    echo 'Controller tidak ditemukan: ' . htmlspecialchars($controllerName);
    exit;
}

$controller = new $controllerName();
if (!method_exists($controller, $method)) {
    http_response_code(404);
    echo 'Method tidak ditemukan: ' . htmlspecialchars($method);
    exit;
}

call_user_func_array([$controller, $method], $params);
