<?php

namespace App\Services;

use App\Core\Database;
use PDO;
use PDOException;

class InstallerService
{
    /**
     * Check system requirements
     */
    public static function checkRequirements(): array
    {
        $requirements = [
            'php_version' => [
                'required' => '8.2.0',
                'current' => PHP_VERSION,
                'met' => version_compare(PHP_VERSION, '8.2.0', '>='),
            ],
            'pdo' => [
                'required' => 'PDO extension',
                'current' => extension_loaded('pdo') ? 'Installed' : 'Not installed',
                'met' => extension_loaded('pdo'),
            ],
            'pdo_mysql' => [
                'required' => 'PDO MySQL extension',
                'current' => extension_loaded('pdo_mysql') ? 'Installed' : 'Not installed',
                'met' => extension_loaded('pdo_mysql'),
            ],
            'mbstring' => [
                'required' => 'mbstring extension',
                'current' => extension_loaded('mbstring') ? 'Installed' : 'Not installed',
                'met' => extension_loaded('mbstring'),
            ],
            'openssl' => [
                'required' => 'OpenSSL extension',
                'current' => extension_loaded('openssl') ? 'Installed' : 'Not installed',
                'met' => extension_loaded('openssl'),
            ],
            'json' => [
                'required' => 'JSON extension',
                'current' => extension_loaded('json') ? 'Installed' : 'Not installed',
                'met' => extension_loaded('json'),
            ],
            'writable_torrents' => [
                'required' => 'torrents/ directory writable',
                'current' => self::isDirectoryWritable('./torrents') ? 'Writable' : 'Not writable',
                'met' => self::isDirectoryWritable('./torrents'),
            ],
            'writable_cache' => [
                'required' => 'cache/ directory writable',
                'current' => self::isDirectoryWritable('./cache') ? 'Writable' : 'Not writable',
                'met' => self::isDirectoryWritable('./cache'),
            ],
        ];

        $allMet = true;
        foreach ($requirements as $req) {
            if (!$req['met']) {
                $allMet = false;
                break;
            }
        }

        return [
            'requirements' => $requirements,
            'all_met' => $allMet,
        ];
    }

    /**
     * Test database connection
     */
    public static function testConnection(array $config): array
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            return [
                'success' => true,
                'message' => 'Connection successful',
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create database if it doesn't exist
     */
    public static function createDatabase(array $config): array
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$config['name']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

            return [
                'success' => true,
                'message' => 'Database created successfully',
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Import database schema
     */
    public static function importSchema(array $config): array
    {
        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['name']};charset=utf8mb4";
            $pdo = new PDO($dsn, $config['user'], $config['pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);

            // Read main schema file
            $schemaFile = __DIR__ . '/../../SQL/tb.sql';
            if (!file_exists($schemaFile)) {
                return [
                    'success' => false,
                    'message' => 'Schema file not found: ' . $schemaFile,
                ];
            }

            $sql = file_get_contents($schemaFile);
            
            // Remove comments and split by semicolon
            $sql = preg_replace('/--.*$/m', '', $sql);
            $statements = array_filter(array_map('trim', explode(';', $sql)));

            // Execute statements (DDL statements like CREATE TABLE auto-commit)
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (!empty($statement)) {
                    try {
                        $pdo->exec($statement);
                    } catch (PDOException $e) {
                        // Ignore "table already exists" errors
                        if (strpos($e->getMessage(), 'already exists') === false) {
                            throw $e;
                        }
                    }
                }
            }

            // Import additional schema files
            $additionalFiles = [
                'notifications.sql',
                'polls.sql',
                'advanced_features.sql', // Achievement system, collections, 2FA, etc.
                'avps.sql',
                'categories.sql',
                'countries.sql',
                'reputationlevel.sql',
                'searchcloud.sql',
                'stylesheets.sql',
                'forum_structure.sql', // Forum sections and hierarchy
            ];

            foreach ($additionalFiles as $file) {
                $filePath = __DIR__ . '/../../SQL/' . $file;
                if (file_exists($filePath)) {
                    $sql = file_get_contents($filePath);
                    $sql = preg_replace('/--.*$/m', '', $sql);
                    $statements = array_filter(array_map('trim', explode(';', $sql)));
                    
                    foreach ($statements as $statement) {
                        $statement = trim($statement);
                        if (!empty($statement)) {
                            // Handle MySQL's lack of "IF NOT EXISTS" for ALTER TABLE
                            // Replace "ADD COLUMN IF NOT EXISTS" with conditional check
                            if (preg_match('/ALTER TABLE\s+`?(\w+)`?\s+ADD COLUMN IF NOT EXISTS/i', $statement, $matches)) {
                                $tableName = $matches[1];
                                // Extract column definition
                                if (preg_match('/ADD COLUMN IF NOT EXISTS\s+(.+)/i', $statement, $colMatches)) {
                                    $columnDef = $colMatches[1];
                                    // Extract column name
                                    if (preg_match('/`?(\w+)`?/i', $columnDef, $colNameMatches)) {
                                        $columnName = $colNameMatches[1];
                                        // Check if column exists
                                        $checkSql = "SELECT COUNT(*) as count FROM information_schema.COLUMNS 
                                                    WHERE TABLE_SCHEMA = DATABASE() 
                                                    AND TABLE_NAME = '{$tableName}' 
                                                    AND COLUMN_NAME = '{$columnName}'";
                                        $result = $pdo->query($checkSql)->fetch(PDO::FETCH_ASSOC);
                                        if ($result['count'] == 0) {
                                            // Column doesn't exist, add it
                                            $statement = "ALTER TABLE `{$tableName}` ADD COLUMN {$columnDef}";
                                        } else {
                                            // Column exists, skip
                                            continue;
                                        }
                                    }
                                }
                            }
                            
                            try {
                                $pdo->exec($statement);
                            } catch (PDOException $e) {
                                // Ignore "table already exists" and "duplicate column" errors for idempotency
                                $errorMsg = $e->getMessage();
                                if (strpos($errorMsg, 'already exists') === false && 
                                    strpos($errorMsg, 'Duplicate column name') === false &&
                                    strpos($errorMsg, 'table or view already exists') === false &&
                                    strpos($errorMsg, 'Duplicate key name') === false) {
                                    error_log("SQL Error in {$file}: {$errorMsg}");
                                    error_log("Statement: {$statement}");
                                    throw $e;
                                }
                            }
                        }
                    }
                } else {
                    error_log("Warning: SQL file not found: {$filePath}");
                }
            }

            return [
                'success' => true,
                'message' => 'Schema imported successfully',
            ];
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create .env file
     */
    public static function createEnvFile(array $config): array
    {
        try {
            $envContent = "# Database Configuration\n";
            $envContent .= "DB_HOST={$config['db_host']}\n";
            $envContent .= "DB_PORT={$config['db_port']}\n";
            $envContent .= "DB_NAME={$config['db_name']}\n";
            $envContent .= "DB_USER={$config['db_user']}\n";
            $envContent .= "DB_PASS={$config['db_pass']}\n\n";
            
            $envContent .= "# Application\n";
            $envContent .= "APP_ENV={$config['app_env']}\n";
            $envContent .= "APP_DEBUG={$config['app_debug']}\n";
            $envContent .= "APP_URL={$config['app_url']}\n";
            $envContent .= "APP_NAME={$config['app_name']}\n\n";
            
            $envContent .= "# Security\n";
            $envContent .= "JWT_SECRET={$config['jwt_secret']}\n";
            $envContent .= "JWT_EXPIRY=86400\n";
            $envContent .= "SESSION_LIFETIME=7200\n\n";
            
            $envContent .= "# SMTP Mail Settings\n";
            $envContent .= "MAIL_HOST={$config['mail_host']}\n";
            $envContent .= "MAIL_PORT={$config['mail_port']}\n";
            $envContent .= "MAIL_USER={$config['mail_user']}\n";
            $envContent .= "MAIL_PASS={$config['mail_pass']}\n";
            $envContent .= "MAIL_ENCRYPTION={$config['mail_encryption']}\n";
            $envContent .= "MAIL_FROM_ADDRESS={$config['mail_from_address']}\n";
            $envContent .= "MAIL_FROM_NAME={$config['mail_from_name']}\n\n";
            
            $envContent .= "# File Uploads\n";
            $envContent .= "MAX_TORRENT_SIZE=1048576\n";
            $envContent .= "TORRENT_DIR=./torrents\n\n";
            
            $envContent .= "# Tracker Settings\n";
            $envContent .= "ANNOUNCE_INTERVAL=1800\n";
            $envContent .= "SIGNUP_TIMEOUT=259200\n";
            $envContent .= "MAX_DEAD_TORRENT_TIME=21600\n\n";
            
            $envContent .= "# Site Settings\n";
            $envContent .= "SITE_ONLINE=1\n";
            $envContent .= "MAX_USERS=5000\n";
            $envContent .= "MIN_VOTES=1\n\n";
            
            $envContent .= "# Cache Redis\n";
            $envContent .= "CACHE_DRIVER=redis\n";
            $envContent .= "CACHE_HOST=127.0.0.1\n";
            $envContent .= "CACHE_PORT=6379\n";
            $envContent .= "CACHE_PASSWORD=\n";
            $envContent .= "CACHE_DATABASE=0\n";
            $envContent .= "CACHE_ENABLED=true\n\n";
            
            $envContent .= "# Two-Factor Authentication\n";
            $envContent .= "TWO_FACTOR_ISSUER={$config['app_name']}\n";

            if (file_put_contents('.env', $envContent) === false) {
                return [
                    'success' => false,
                    'message' => 'Failed to write .env file. Check permissions.',
                ];
            }

            return [
                'success' => true,
                'message' => '.env file created successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create admin user
     */
    public static function createAdmin(array $userData): array
    {
        try {
            // Reload config to get new database settings
            if (file_exists('.env')) {
                // Disconnect existing connection
                Database::disconnect();
                
                // Reload config
                \App\Core\Config::load();
                
                // Get new database connection using Config
                $dbConfig = \App\Core\Config::get('db');
                $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['name']};charset=utf8mb4";
                $pdo = new PDO($dsn, $dbConfig['user'], $dbConfig['pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                ]);
                
                Database::setInstance($pdo);
            }

            $passhash = password_hash($userData['password'], PASSWORD_BCRYPT);
            $passkey = bin2hex(random_bytes(16));
            $secret = bin2hex(random_bytes(10));
            $added = time();

            $sql = "INSERT INTO users (username, email, passhash, passkey, secret, class, status, enabled, added, last_access) 
                    VALUES (:username, :email, :passhash, :passkey, :secret, 6, 'confirmed', 'yes', :added, :added)";

            Database::execute($sql, [
                'username' => $userData['username'],
                'email' => $userData['email'],
                'passhash' => $passhash,
                'passkey' => $passkey,
                'secret' => $secret,
                'added' => $added,
            ]);

            return [
                'success' => true,
                'message' => 'Admin user created successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create installation lock file
     */
    public static function createLockFile(): bool
    {
        return file_put_contents('.installed', date('Y-m-d H:i:s')) !== false;
    }

    /**
     * Check if already installed
     */
    public static function isInstalled(): bool
    {
        return file_exists('.installed') && file_exists('.env');
    }

    /**
     * Create required directories
     */
    public static function createDirectories(): array
    {
        $directories = ['torrents', 'cache', 'logs'];
        $results = [];

        foreach ($directories as $dir) {
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true)) {
                    $results[$dir] = false;
                } else {
                    $results[$dir] = true;
                }
            } else {
                $results[$dir] = true;
            }
        }

        return $results;
    }

    /**
     * Check if directory is writable (Windows-compatible)
     */
    private static function isDirectoryWritable(string $path): bool
    {
        if (!is_dir($path)) {
            return false;
        }

        // Try to create a test file
        $testFile = $path . '/.writable_test_' . uniqid();
        $result = @file_put_contents($testFile, 'test');
        
        if ($result !== false) {
            @unlink($testFile);
            return true;
        }

        // Fallback to is_writable
        return is_writable($path);
    }
}

