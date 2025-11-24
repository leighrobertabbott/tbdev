<?php

namespace App\Core;

class Cache
{
    private static ?\Predis\Client $redis = null;
    private static bool $enabled = false;

    public static function init(): void
    {
        $config = Config::get('cache', []);
        
        if (empty($config['enabled']) || $config['driver'] !== 'redis') {
            self::$enabled = false;
            return;
        }

        try {
            $parameters = [
                'host' => $config['host'] ?? '127.0.0.1',
                'port' => $config['port'] ?? 6379,
                'database' => $config['database'] ?? 0,
            ];

            if (!empty($config['password'])) {
                $parameters['password'] = $config['password'];
            }

            self::$redis = new \Predis\Client($parameters);
            self::$redis->ping(); // Test connection
            self::$enabled = true;
        } catch (\Exception $e) {
            // Silently fail if Redis is not available - cache will be disabled
            self::$enabled = false;
        }
    }

    public static function get(string $key, $default = null)
    {
        if (!self::$enabled) {
            return $default;
        }

        try {
            $value = self::$redis->get($key);
            return $value !== null ? unserialize($value) : $default;
        } catch (\Exception $e) {
            error_log('Cache get error: ' . $e->getMessage());
            return $default;
        }
    }

    public static function set(string $key, $value, int $ttl = 3600): bool
    {
        if (!self::$enabled) {
            return false;
        }

        try {
            return self::$redis->setex($key, $ttl, serialize($value));
        } catch (\Exception $e) {
            error_log('Cache set error: ' . $e->getMessage());
            return false;
        }
    }

    public static function delete(string $key): bool
    {
        if (!self::$enabled) {
            return false;
        }

        try {
            return self::$redis->del([$key]) > 0;
        } catch (\Exception $e) {
            error_log('Cache delete error: ' . $e->getMessage());
            return false;
        }
    }

    public static function flush(string $pattern = '*'): bool
    {
        if (!self::$enabled) {
            return false;
        }

        try {
            $keys = self::$redis->keys($pattern);
            if (!empty($keys)) {
                return self::$redis->del($keys) > 0;
            }
            return true;
        } catch (\Exception $e) {
            error_log('Cache flush error: ' . $e->getMessage());
            return false;
        }
    }

    public static function remember(string $key, callable $callback, int $ttl = 3600)
    {
        $value = self::get($key);
        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        self::set($key, $value, $ttl);
        return $value;
    }

    public static function increment(string $key, int $value = 1): int
    {
        if (!self::$enabled) {
            return 0;
        }

        try {
            return self::$redis->incrBy($key, $value);
        } catch (\Exception $e) {
            error_log('Cache increment error: ' . $e->getMessage());
            return 0;
        }
    }

    public static function isEnabled(): bool
    {
        return self::$enabled;
    }
}

