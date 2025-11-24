<?php

namespace App\Core;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class Middleware
{
    abstract public function handle(Request $request, callable $next): Response;

    public static function csrf(): callable
    {
        return function(Request $request, callable $next) {
            // Ensure session is started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
                $token = $request
                    ->request
                    ->get('_token') ?? $request
                    ->headers
                    ->get('X-CSRF-TOKEN');
                
                // Ensure we have a token in session
                if (!isset($_SESSION['csrf_token'])) {
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                }
                
                if (!$token || !Security::validateCsrfToken($token)) {
                    // For web requests, redirect back with error
                    if (!$request
                        ->isXmlHttpRequest() && strpos($request
                        ->getPathInfo(), '/api/') !== 0) {
                        $_SESSION['csrf_error'] = 'CSRF token mismatch. Please try again.';
                        $referer = $request
                            ->headers
                            ->get('Referer') ?? '/login';
                        return new Response('', 302, ['Location' => $referer]);
                    }
                    // For API requests, return JSON error
                    return new Response(json_encode(['error' => 'CSRF token mismatch']), 403, ['Content-Type' => 'application/json']);
                }
            }
            
            return $next($request);
        };
    }

    public static function auth(): callable
    {
        return function(Request $request, callable $next) {
            if (!Auth::check()) {
                if ($request
                    ->isXmlHttpRequest() || strpos($request
                    ->getPathInfo(), '/api/') === 0) {
                    return new Response(json_encode(['error' => 'Unauthorized']), 401, ['Content-Type' => 'application/json']);
                }
                // Redirect to login for web requests
                $loginUrl = '/login?returnto=' .
                    urlencode($request->getPathInfo());
                return new Response('', 302, ['Location' => $loginUrl]);
            }
            
            return $next($request);
        };
    }

    public static function cors(): callable
    {
        return function(Request $request, callable $next) {
            $response = $next($request);
            
            $origin = $request->headers->get('Origin');
            $allowedOrigins = Config::get('app.allowed_origins', [Config::get('app.url')]);
            
            if (in_array($origin, $allowedOrigins)) {
                $response->headers->set('Access-Control-Allow-Origin', $origin);
                $response
                    ->headers
                    ->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                $response
                    ->headers
                    ->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-CSRF-TOKEN');
                $response
                    ->headers
                    ->set('Access-Control-Allow-Credentials', 'true');
            }
            
            if ($request->getMethod() === 'OPTIONS') {
                return new Response('', 200);
            }
            
            return $response;
        };
    }
}


