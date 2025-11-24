<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Cache;
use PDO;

class RecommendationService
{
    /**
     * Get recommended torrents for a user based on their download history
     */
    public static function getForUser(int $userId, int $limit = 10): array
    {
        return Cache::remember(
            "recommendations:user:{$userId}",
            function () use ($userId, $limit) {
                // Get user's downloaded categories
                $userCategories = Database::fetchAll(
                    "SELECT DISTINCT category, COUNT(*) as count 
                     FROM torrents t
                     INNER JOIN peers p ON t.id = p.torrent
                     WHERE p.userid = :user_id AND p.downloaded > 0
                     GROUP BY category
                     ORDER BY count DESC
                     LIMIT 5",
                    ['user_id' => $userId]
                );

                if (empty($userCategories)) {
                    // Fallback to popular torrents
                    return self::getPopular($limit);
                }

                $categoryIds = array_column($userCategories, 'category');
                
                if (empty($categoryIds)) {
                    return self::getPopular($limit);
                }
                
                // Build query with named parameters for better compatibility
                $categoryPlaceholders = [];
                $params = [];
                foreach ($categoryIds as $idx => $catId) {
                    $key = 'cat_' . $idx;
                    $categoryPlaceholders[] = ':' . $key;
                    $params[$key] = $catId;
                }
                $placeholders = implode(',', $categoryPlaceholders);
                $params['user_id'] = $userId;
                $params['limit'] = $limit;

                // Get torrents from user's preferred categories
                $db = \App\Core\Database::getInstance();
                $sql = "SELECT t.*, u.username as owner_name, c.name as category_name
                     FROM torrents t
                     LEFT JOIN users u ON t.owner = u.id
                     LEFT JOIN categories c ON t.category = c.id
                     WHERE t.visible = 'yes' 
                     AND t.category IN ({$placeholders})
                     AND t.id NOT IN (
                         SELECT DISTINCT torrent FROM peers WHERE userid = :user_id
                     )
                     ORDER BY t.seeders DESC, t.times_completed DESC
                     LIMIT :limit";
                $stmt = $db->prepare($sql);
                foreach ($params as $key => $value) {
                    $stmt->bindValue(':' .
                        $key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
                }
                $stmt->execute();
                $torrents = $stmt->fetchAll(PDO::FETCH_ASSOC);

                return $torrents;
            },
            3600 // Cache for 1 hour
        );
    }

    /**
     * Get related torrents based on category and tags
     */
    public static function getRelated(int $torrentId, int $limit = 8): array
    {
        return Cache::remember(
            "recommendations:related:{$torrentId}",
            function () use ($torrentId, $limit) {
                $torrent = Database::fetchOne(
                    "SELECT category FROM torrents WHERE id = :id",
                    ['id' => $torrentId]
                );

                if (!$torrent) {
                    return [];
                }

                return Database::fetchAll(
                    "SELECT t.*, u.username as owner_name, c.name as category_name
                     FROM torrents t
                     LEFT JOIN users u ON t.owner = u.id
                     LEFT JOIN categories c ON t.category = c.id
                     WHERE t.visible = 'yes'
                     AND t.category = :category
                     AND t.id != :torrent_id
                     ORDER BY t.seeders DESC, t.times_completed DESC
                     LIMIT :limit",
                    [
                        'category' => $torrent['category'],
                        'torrent_id' => $torrentId,
                        'limit' => $limit,
                    ]
                );
            },
            1800 // Cache for 30 minutes
        );
    }

    /**
     * Get popular torrents
     */
    public static function getPopular(int $limit = 20): array
    {
        return Cache::remember(
            "recommendations:popular",
            function () use ($limit) {
                $db = \App\Core\Database::getInstance();
                $stmt = $db->prepare(
                    "SELECT t.*, u.username as owner_name, c.name as category_name
                     FROM torrents t
                     LEFT JOIN users u ON t.owner = u.id
                     LEFT JOIN categories c ON t.category = c.id
                     WHERE t.visible = 'yes'
                     ORDER BY (t.seeders * 2 + t.times_completed) DESC
                     LIMIT :limit"
                );
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            },
            600 // Cache for 10 minutes
        );
    }

    /**
     * Get trending torrents (recent uploads with high activity)
     */
    public static function getTrending(int $limit = 20): array
    {
        $dayAgo = time() - 86400;

        return Cache::remember(
            "recommendations:trending",
            function () use ($limit, $dayAgo) {
                $db = \App\Core\Database::getInstance();
                $stmt = $db->prepare(
                    "SELECT t.*, u.username as owner_name, c.name as category_name
                     FROM torrents t
                     LEFT JOIN users u ON t.owner = u.id
                     LEFT JOIN categories c ON t.category = c.id
                     WHERE t.visible = 'yes'
                     AND t.added > :day_ago
                     ORDER BY (t.seeders + t.leechers) DESC, t.times_completed DESC
                     LIMIT :limit"
                );
                $stmt->bindValue(':day_ago', $dayAgo, PDO::PARAM_INT);
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            },
            300 // Cache for 5 minutes
        );
    }
}

