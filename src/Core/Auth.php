<?php

namespace App\Core;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\User;

class Auth
{
    public static function attempt(string $username, string $password): ?array
    {
        $user = User::findByUsername($username);
        
        if (!$user || !password_verify($password, $user['passhash'])) {
            return null;
        }

        if ($user['enabled'] !== 'yes' || $user['status'] !== 'confirmed') {
            return null;
        }

        return self::generateToken($user);
    }

    public static function generateToken(array $user): array
    {
        $secret = Config::get('jwt.secret');
        
        // Validate JWT secret is set and secure
        if (empty($secret) || strlen($secret) < 32) {
            error_log('CRITICAL: JWT_SECRET is not set or too weak. Please set a strong secret in .env file.');
            throw new \RuntimeException('JWT secret not configured properly');
        }
        
        $expiry = Config::get('jwt.expiry');

        $payload = [
            'iss' => Config::get('app.url'),
            'iat' => time(),
            'exp' => time() + $expiry,
            'sub' => $user['id'],
            'username' => $user['username'],
            'class' => $user['class'],
        ];

        $token = JWT::encode($payload, $secret, 'HS256');

        return [
            'token' => $token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'class' => $user['class'],
            ],
        ];
    }

    public static function validateToken(string $token): ?array
    {
        try {
            $secret = Config::get('jwt.secret');
            
            // Validate JWT secret is set
            if (empty($secret) || strlen($secret) < 32) {
                error_log('CRITICAL: JWT_SECRET is not set or too weak');
                return null;
            }
            
            $decoded = JWT::decode($token, new Key($secret, 'HS256'));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function user(): ?array
    {
        $token = self::getTokenFromRequest();
        if (!$token) {
            return null;
        }

        $payload = self::validateToken($token);
        if (!$payload) {
            return null;
        }

        return User::findById($payload['sub']);
    }

    private static function getTokenFromRequest(): ?string
    {
        $headers = getallheaders();
        
        if (isset($headers['Authorization'])) {
            $auth = $headers['Authorization'];
            if (preg_match('/Bearer\s+(.*)$/i', $auth, $matches)) {
                return $matches[1];
            }
        }

        if (isset($_COOKIE['auth_token'])) {
            return $_COOKIE['auth_token'];
        }

        return null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function requireAuth(): void
    {
        if (!self::check()) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }
}


