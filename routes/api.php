<?php

use App\Core\Router;
use App\Core\Auth;
use App\Core\Middleware;
use App\Controllers\Api\AuthController;
use App\Controllers\Api\TorrentController;
use App\Controllers\Api\UserController;
use Symfony\Component\HttpFoundation\JsonResponse;

/** @var Router $router */

// Auth routes
$router->post(
    '/api/auth/login',
    [AuthController::class, 'login'],
    'api.auth.login',
    [Middleware::csrf()]);
$router->post(
    '/api/auth/logout',
    [AuthController::class, 'logout'],
    'api.auth.logout',
    [Middleware::auth(), Middleware::csrf()]);
$router->get(
    '/api/auth/me',
    [AuthController::class, 'me'],
    'api.auth.me',
    [Middleware::auth()]);

// Torrent routes
$router->get(
    '/api/torrents',
    [TorrentController::class, 'index'],
    'api.torrents.index',
    [Middleware::auth()]);
$router->get(
    '/api/torrents/{id}',
    [TorrentController::class, 'show'],
    'api.torrents.show',
    [Middleware::auth()]);
$router->post(
    '/api/torrents',
    [TorrentController::class, 'create'],
    'api.torrents.create',
    [Middleware::auth(), Middleware::csrf()]);

// User routes
$router->get(
    '/api/users/{id}',
    [UserController::class, 'show'],
    'api.users.show',
    [Middleware::auth()]);

// Advanced API routes
$router->get(
    '/api/stats',
    [\App\Controllers\Api\AdvancedApiController::class, 'stats'],
    'api.stats');
$router->get(
    '/api/search',
    [\App\Controllers\Api\AdvancedApiController::class, 'search'],
    'api.search',
    [Middleware::auth()]);
$router->get(
    '/api/categories',
    [\App\Controllers\Api\AdvancedApiController::class, 'categories'],
    'api.categories');
$router->get(
    '/api/users/{id}/stats',
    [\App\Controllers\Api\AdvancedApiController::class, 'userStats'],
    'api.users.stats',
    [Middleware::auth()]);

// Poll API routes
$router->get(
    '/api/polls',
    [\App\Controllers\Api\PollApiController::class, 'index'],
    'api.polls',
    [Middleware::auth()]);
$router->get(
    '/api/polls/{id}',
    [\App\Controllers\Api\PollApiController::class, 'show'],
    'api.polls.show',
    [Middleware::auth()]);
$router->post(
    '/api/polls/{id}/vote',
    [\App\Controllers\Api\PollApiController::class, 'vote'],
    'api.polls.vote',
    [Middleware::auth(), Middleware::csrf()]);

