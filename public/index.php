<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Config;
use App\Core\Router;
use App\Core\Middleware;
use App\Core\Cache;
use Symfony\Component\HttpFoundation\Request;

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
Config::load();

// Initialize cache
Cache::init();

// Set error reporting - Always show errors during development
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Create request
$request = Request::createFromGlobals();

// Initialize router
$router = new Router();

// Load routes
require_once __DIR__ . '/../routes/web.php';
require_once __DIR__ . '/../routes/api.php';

// Apply CORS middleware
$corsMiddleware = Middleware::cors();
$response = $corsMiddleware($request, function($req) use ($router) {
    return $router->dispatch($req);
});

$response->send();

