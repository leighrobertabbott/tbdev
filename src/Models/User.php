<?php

namespace App\Models;

use App\Core\Database;

class User
{
    public static function findById(int $id): ?array
    {
        $sql = "SELECT * FROM users WHERE id = :id AND enabled = 'yes' AND status = 'confirmed'";
        return Database::fetchOne($sql, ['id' => $id]);
    }

    public static function findByUsername(string $username): ?array
    {
        $sql = "SELECT * FROM users WHERE username = :username";
        return Database::fetchOne($sql, ['username' => $username]);
    }

    public static function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM users WHERE email = :email";
        return Database::fetchOne($sql, ['email' => $email]);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO users (username, email, passhash, added, status, enabled, class) 
                VALUES (:username, :email, :passhash, :added, :status, :enabled, :class)";
        
        $params = [
            'username' => $data['username'],
            'email' => $data['email'],
            'passhash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'added' => time(),
            'status' => 'pending',
            'enabled' => 'yes',
            'class' => 0,
        ];

        Database::execute($sql, $params);
        return (int) Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        // Only allow fields that actually exist in the users table
        $allowed = [
            'username', 'email', 'passhash', 'class', 'enabled', 'status',
            'avatar', 'title', 'info', 'stylesheet', 'time_offset', 'privacy'
        ];
        $updates = [];
        $params = ['id' => $id];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
                $params[$field] = $data[$field];
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = :id";
        
        return Database::execute($sql, $params) > 0;
    }

    public static function updateLastAccess(int $id, string $ip): void
    {
        $sql = "UPDATE users SET last_access = :time, ip = :ip WHERE id = :id";
        Database::execute($sql, [
            'id' => $id,
            'time' => time(),
            'ip' => $ip,
        ]);
    }

    public static function count(): int
    {
        $sql = "SELECT COUNT(*) as count FROM users";
        $result = Database::fetchOne($sql);
        return (int) ($result['count'] ?? 0);
    }

    public static function findLatest(): ?array
    {
        $sql = "SELECT id, username FROM users WHERE status='confirmed' ORDER BY id DESC LIMIT 1";
        return Database::fetchOne($sql);
    }

    public static function getUserClass(int $userId): int
    {
        $user = self::findById($userId);
        return (int) ($user['class'] ?? 0);
    }
}

