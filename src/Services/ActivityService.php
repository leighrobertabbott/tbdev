<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Security;

class ActivityService
{
    /**
     * Log user activity
     */
    public static function log(int $userId, string $action, ?string $resourceType = null, ?int $resourceId = null): void
    {
        $ip = Security::getClientIp();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

        Database::execute(
            "INSERT INTO user_activity (user_id, action, resource_type, resource_id, ip_address, user_agent, created_at)
             VALUES (:user_id, :action, :resource_type, :resource_id, :ip, :user_agent, :time)",
            [
                'user_id' => $userId,
                'action' => $action,
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'time' => time(),
            ]
        );
    }

    /**
     * Get user activity feed
     */
    public static function getFeed(int $userId, int $limit = 50): array
    {
        return Database::fetchAll(
            "SELECT * FROM user_activity 
             WHERE user_id = :user_id 
             ORDER BY created_at DESC 
             LIMIT :limit",
            ['user_id' => $userId, 'limit' => $limit]
        );
    }

    /**
     * Get recent activity for a resource
     */
    public static function getResourceActivity(string $resourceType, int $resourceId, int $limit = 20): array
    {
        return Database::fetchAll(
            "SELECT ua.*, u.username
             FROM user_activity ua
             LEFT JOIN users u ON ua.user_id = u.id
             WHERE ua.resource_type = :resource_type AND ua.resource_id = :resource_id
             ORDER BY ua.created_at DESC
             LIMIT :limit",
            [
                'resource_type' => $resourceType,
                'resource_id' => $resourceId,
                'limit' => $limit,
            ]
        );
    }
}

