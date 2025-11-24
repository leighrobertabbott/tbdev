<?php

namespace App\Services;

use App\Core\Database;
use App\Models\User;

class NotificationService
{
    public static function create(int $userId, string $type, string $message, array $data = []): int
    {
        $sql = "INSERT INTO notifications (user_id, type, message, data, created_at, read_at) 
                VALUES (:user_id, :type, :message, :data, :created_at, NULL)";
        
        Database::execute($sql, [
            'user_id' => $userId,
            'type' => $type,
            'message' => $message,
            'data' => json_encode($data),
            'created_at' => time(),
        ]);
        
        return (int) Database::lastInsertId();
    }

    public static function getUnreadCount(int $userId): int
    {
        $result = Database::fetchOne(
            "SELECT COUNT(*) as count FROM notifications WHERE user_id = :user_id AND read_at IS NULL",
            ['user_id' => $userId]
        );
        return (int) ($result['count'] ?? 0);
    }

    public static function getForUser(int $userId, int $limit = 20): array
    {
        return Database::fetchAll(
            "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :limit",
            ['user_id' => $userId, 'limit' => $limit]
        );
    }

    public static function markAsRead(int $id, int $userId): bool
    {
        return Database::execute(
            "UPDATE notifications SET read_at = :time WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $userId, 'time' => time()]
        ) > 0;
    }

    public static function markAllAsRead(int $userId): bool
    {
        return Database::execute(
            "UPDATE notifications SET read_at = :time WHERE user_id = :user_id AND read_at IS NULL",
            ['user_id' => $userId, 'time' => time()]
        ) > 0;
    }

    // Notification types
    public static function notifyNewMessage(int $userId, int $senderId): void
    {
        $sender = User::findById($senderId);
        self::create($userId, 'message', "New message from {$sender['username']}", ['sender_id' => $senderId]);
    }

    public static function notifyTorrentComment(int $userId, int $torrentId, int $commenterId): void
    {
        $commenter = User::findById($commenterId);
        self::create($userId, 'comment', "New comment on your torrent from {$commenter['username']}", [
            'torrent_id' => $torrentId,
            'commenter_id' => $commenterId,
        ]);
    }

    public static function notifyTorrentApproved(int $userId, int $torrentId): void
    {
        self::create($userId, 'torrent_approved', 'Your torrent has been approved', ['torrent_id' => $torrentId]);
    }
}


