<?php

namespace App\Models;

use App\Core\Database;

class News
{
    public static function all(int $limit = 10): array
    {
        $sql = "SELECT n.*, u.username 
                FROM news n 
                LEFT JOIN users u ON n.userid = u.id 
                WHERE n.added + (3600 * 24 * 45) > ? 
                ORDER BY n.added DESC 
                LIMIT ?";
        return Database::fetchAll($sql, [time(), $limit]);
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne(
            "SELECT n.*, u.username FROM news n LEFT JOIN users u ON n.userid = u.id WHERE n.id = :id",
            ['id' => $id]
        );
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO news (userid, added, body, headline) 
                VALUES (:userid, :added, :body, :headline)";
        
        Database::execute($sql, [
            'userid' => $data['userid'],
            'added' => time(),
            'body' => $data['body'],
            'headline' => $data['headline'],
        ]);
        
        return (int) Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $allowed = ['headline', 'body'];
        $updates = [];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE news SET " . implode(', ', $updates) . " WHERE id = :id";
        $data['id'] = $id;
        
        return Database::execute($sql, $data) > 0;
    }

    public static function delete(int $id): bool
    {
        return Database::execute("DELETE FROM news WHERE id = :id", ['id' => $id]) > 0;
    }
}


