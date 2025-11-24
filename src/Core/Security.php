<?php

namespace App\Core;

class Security
{
    public static function generateCsrfToken(): string
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken(string $token): bool
    {
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    public static function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeInput($input): mixed
    {
        if (is_array($input)) {
            return array_map([self::class, 'sanitizeInput'], $input);
        }
        
        if (is_string($input)) {
            // Remove null bytes
            $input = str_replace("\0", '', $input);
            // Trim whitespace
            $input = trim($input);
            // Remove control characters except newlines and tabs
            $input = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $input);
        }
        
        return $input;
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validateUsername(string $username): bool
    {
        // Username: 3-20 alphanumeric, underscore, hyphen
        return preg_match('/^[a-zA-Z0-9_-]{3,20}$/', $username) === 1;
    }

    public static function validatePassword(string $password): array
    {
        $errors = [];
        
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        
        return $errors;
    }

    public static function rateLimit(string $key, int $maxAttempts = 5, int $window = 300): bool
    {
        $cacheFile = sys_get_temp_dir() . '/ratelimit_' . md5($key) . '.json';
        
        // Use file locking to prevent race conditions
        $fp = fopen($cacheFile, 'c+');
        if (!$fp) {
            error_log('Failed to open rate limit file: ' . $cacheFile);
            return true; // Fail open to prevent DoS
        }
        
        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            error_log('Failed to acquire lock on rate limit file');
            return true; // Fail open
        }
        
        $data = [];
        rewind($fp); // Reset pointer to beginning before reading
        $content = stream_get_contents($fp);
        if ($content) {
            $data = json_decode($content, true) ?: [];
        }
        
        $now = time();
        $attempts = array_filter($data['attempts'] ?? [], function($timestamp) use ($now, $window) {
            return ($now - $timestamp) < $window;
        });
        
        if (count($attempts) >= $maxAttempts) {
            flock($fp, LOCK_UN);
            fclose($fp);
            error_log('Rate limit exceeded for: ' .
                $key .
                ' (attempts: ' .
                count($attempts) .
                ', max: ' .
                $maxAttempts .
                ')');
            return false;
        }
        
        $attempts[] = $now;
        
        // Write back to file
        ftruncate($fp, 0);
        rewind($fp);
        $written = fwrite($fp, json_encode(['attempts' => $attempts]));
        
        if ($written === false) {
            error_log('Failed to write rate limit data to file: ' . $cacheFile);
        }
        
        flock($fp, LOCK_UN);
        fclose($fp);
        
        return true;
    }
    
    public static function clearRateLimit(string $key): void
    {
        $cacheFile = sys_get_temp_dir() . '/ratelimit_' . md5($key) . '.json';
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
        }
    }

    public static function getClientIp(): string
    {
        // WARNING: HTTP_CLIENT_IP and HTTP_X_FORWARDED_FOR can be spoofed by clients
        // Only use these headers if you have a trusted proxy/load balancer
        // For production, consider implementing trusted proxy IP checking
        
        // If behind a trusted proxy, check forwarded headers
        // Otherwise, always use REMOTE_ADDR which cannot be spoofed
        $trustForwardedHeaders = Config::get('security.trust_forwarded_headers', false);
        
        if ($trustForwardedHeaders) {
            $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
            
            foreach ($ipKeys as $key) {
                if (!empty($_SERVER[$key])) {
                    $ip = $_SERVER[$key];
                    // If comma-separated (proxy chain), take the first IP
                    if (strpos($ip, ',') !== false) {
                        $ip = explode(',', $ip)[0];
                    }
                    $ip = trim($ip);
                    // Validate IP and exclude private/reserved ranges for public use
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                        return $ip;
                    }
                    // Also accept the IP even if it's private (for local dev)
                    if (filter_var($ip, FILTER_VALIDATE_IP)) {
                        return $ip;
                    }
                }
            }
        }
        
        // Default to REMOTE_ADDR which cannot be spoofed
        return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    }
}


