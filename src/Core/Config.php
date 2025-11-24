<?php

namespace App\Core;

use Dotenv\Dotenv;

class Config
{
    private static array $config = [];

    public static function load(): void
    {
        $envPath = __DIR__ . '/../../.env';
        if (file_exists($envPath)) {
            $dotenv = Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();
        }

        self::$config = [
            'db' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'port' => (int)($_ENV['DB_PORT'] ?? 3306),
                'name' => $_ENV['DB_NAME'] ?? 'TBDev',
                'user' => $_ENV['DB_USER'] ?? 'root',
                'pass' => $_ENV['DB_PASS'] ?? '',
            ],
            'app' => [
                'env' => $_ENV['APP_ENV'] ?? 'production',
                'debug' => filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'url' => $_ENV['APP_URL'] ?? 'http://localhost',
                'name' => $_ENV['APP_NAME'] ?? 'TorrentBits',
            ],
            'jwt' => [
                'secret' => $_ENV['JWT_SECRET'] ?? '',
                'expiry' => (int)($_ENV['JWT_EXPIRY'] ?? 86400),
            ],
            'session' => [
                'lifetime' => (int)($_ENV['SESSION_LIFETIME'] ?? 7200),
            ],
            'mail' => [
                'host' => $_ENV['MAIL_HOST'] ?? '',
                'port' => (int)($_ENV['MAIL_PORT'] ?? 587),
                'user' => $_ENV['MAIL_USER'] ?? '',
                'pass' => $_ENV['MAIL_PASS'] ?? '',
                'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
                'from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? '',
                'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'TorrentBits',
            ],
            'uploads' => [
                'max_torrent_size' => (int)($_ENV['MAX_TORRENT_SIZE'] ?? 1048576),
                'torrent_dir' => $_ENV['TORRENT_DIR'] ?? './torrents',
            ],
            'tracker' => [
                'announce_interval' => (int)($_ENV['ANNOUNCE_INTERVAL'] ?? 1800),
                'signup_timeout' => (int)($_ENV['SIGNUP_TIMEOUT'] ?? 259200),
                'max_dead_torrent_time' => (int)($_ENV['MAX_DEAD_TORRENT_TIME'] ?? 21600),
            ],
            'site' => [
                'online' => filter_var($_ENV['SITE_ONLINE'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'max_users' => (int)($_ENV['MAX_USERS'] ?? 5000),
                'min_votes' => (int)($_ENV['MIN_VOTES'] ?? 1),
            ],
            'cache' => [
                'driver' => $_ENV['CACHE_DRIVER'] ?? 'file',
                'host' => $_ENV['CACHE_HOST'] ?? '127.0.0.1',
                'port' => (int)($_ENV['CACHE_PORT'] ?? 6379),
                'password' => $_ENV['CACHE_PASSWORD'] ?? '',
                'database' => (int)($_ENV['CACHE_DATABASE'] ?? 0),
                'enabled' => filter_var($_ENV['CACHE_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN),
            ],
        ];
    }

    public static function get(string $key, $default = null)
    {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    public static function all(): array
    {
        return self::$config;
    }
}


