<?php

namespace App\Models;

use App\Core\Database;

class Message
{
    public static function findForUser(int $userId, string $location = 'in'): array
    {
        if ($location === 'in') {
            $sql = "SELECT m.*, u.username as sender_name, u.class as sender_class 
                    FROM messages m 
                    LEFT JOIN users u ON m.sender = u.id 
                    WHERE m.receiver = :user_id AND m.location IN (1, 3)
                    ORDER BY m.added DESC";
        } elseif ($location === 'out') {
            $sql = "SELECT m.*, u.username as receiver_name, u.class as receiver_class 
                    FROM messages m 
                    LEFT JOIN users u ON m.receiver = u.id 
                    WHERE m.sender = :user_id AND m.location IN (2, 3)
                    ORDER BY m.added DESC";
        } else {
            $sql = "SELECT m.*, 
                    u1.username as sender_name, u1.class as sender_class,
                    u2.username as receiver_name, u2.class as receiver_class
                    FROM messages m 
                    LEFT JOIN users u1 ON m.sender = u1.id 
                    LEFT JOIN users u2 ON m.receiver = u2.id 
                    WHERE (m.sender = :user_id OR m.receiver = :user_id) AND m.location = 3
                    ORDER BY m.added DESC";
        }
        
        return Database::fetchAll($sql, ['user_id' => $userId]);
    }

    public static function getUnreadCount(int $userId): int
    {
        $result = Database::fetchOne(
            "SELECT COUNT(*) as count FROM messages WHERE receiver = :user_id AND unread = 'yes'",
            ['user_id' => $userId]
        );
        return (int) ($result['count'] ?? 0);
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne(
            "SELECT m.*, u1.username as sender_name, u2.username as receiver_name 
             FROM messages m 
             LEFT JOIN users u1 ON m.sender = u1.id 
             LEFT JOIN users u2 ON m.receiver = u2.id 
             WHERE m.id = :id",
            ['id' => $id]
        );
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO messages (sender, receiver, added, subject, msg, unread, location) 
                VALUES (:sender, :receiver, :added, :subject, :msg, 'yes', :location)";
        
        $params = [
            'sender' => $data['sender'],
            'receiver' => $data['receiver'],
            'added' => time(),
            'subject' => $data['subject'],
            'msg' => $data['msg'],
            'location' => $data['location'] ?? 1, // 1=inbox, 2=sent, 3=both
        ];

        Database::execute($sql, $params);
        return (int) Database::lastInsertId();
    }

    public static function markAsRead(int $id, int $userId): bool
    {
        return Database::execute(
            "UPDATE messages SET unread = 'no' WHERE id = :id AND receiver = :user_id",
            ['id' => $id, 'user_id' => $userId]
        ) > 0;
    }

    public static function delete(int $id, int $userId): bool
    {
        // Check ownership
        $message = self::findById($id);
        if (!$message || ($message['sender'] != $userId && $message['receiver'] != $userId)) {
            return false;
        }

        return Database::execute(
            "DELETE FROM messages WHERE id = :id",
            ['id' => $id]
        ) > 0;
    }

    public static function save(int $id, int $userId): bool
    {
        return Database::execute(
            "UPDATE messages SET saved = 'yes' WHERE id = :id AND (sender = :user_id OR receiver = :user_id)",
            ['id' => $id, 'user_id' => $userId]
        ) > 0;
    }
}


