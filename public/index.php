<?php

declare(strict_types=1);

use Core\Env;
use Core\Router;

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/utils/helpers.php';

spl_autoload_register(function (string $class): void {
    $prefixes = [
        'App\\' => base_path('app/'),
        'Core\\' => base_path('core/'),
    ];

    foreach ($prefixes as $prefix => $baseDirectory) {
        if (!str_starts_with($class, $prefix)) {
            continue;
        }

        $relativeClass = substr($class, strlen($prefix));
        $file = $baseDirectory . str_replace('\\', '/', $relativeClass) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
});

Env::load(base_path('.env'));

set_exception_handler(function (\Throwable $exception): void {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');

    $response = [
        'success' => false,
        'message' => 'An unexpected server error occurred.',
    ];

    if (env('APP_DEBUG', 'false') === 'true') {
        $response['error'] = $exception->getMessage();
    }

    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
});

$router = new Router();

require base_path('routes/web.php');
require base_path('routes/api.php');

$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

$baseDirectory = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

if ($baseDirectory !== '' && $baseDirectory !== '/') {
    $requestUri = preg_replace('#^' . preg_quote($baseDirectory, '#') . '#', '', $requestUri) ?: '/';
}

$router->dispatch($requestUri, strtoupper($requestMethod));