<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Core\Config;
use App\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthController
{
    public function login(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        
        // Rate limiting
        $ip = Security::getClientIp();
        if (!Security::rateLimit('login_' . $ip, 5, 300)) {
            return new JsonResponse(['error' => 'Too many login attempts. Please try again later.'], 429);
        }
        
        if (!isset($data['username']) || !isset($data['password'])) {
            return new JsonResponse(['error' => 'Username and password required'], 400);
        }

        // Sanitize input
        $username = Security::sanitizeInput($data['username']);
        $password = $data['password'];

        if (!Security::validateUsername($username)) {
            return new JsonResponse(['error' => 'Invalid username format'], 400);
        }

        $result = Auth::attempt($username, $password);
        
        if (!$result) {
            return new JsonResponse(['error' => 'Invalid credentials'], 401);
        }

        $response = new JsonResponse($result);
        $response->headers->setCookie(
            new \Symfony\Component\HttpFoundation\Cookie(
                'auth_token',
                $result['token'],
                time() + Config::get('jwt.expiry'),
                '/',
                null,
                true, // secure
                true, // httpOnly
                false,
                'strict'
            )
        );

        return $response;
    }

    public function logout(Request $request): JsonResponse
    {
        $response = new JsonResponse(['message' => 'Logged out successfully']);
        $response->headers->clearCookie('auth_token', '/');
        return $response;
    }

    public function me(Request $request): JsonResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return new JsonResponse(['error' => 'Unauthorized'], 401);
        }

        // Remove sensitive data
        unset($user['passhash']);
        
        return new JsonResponse(['user' => $user]);
    }
}

