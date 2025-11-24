<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Cache;
use App\Models\User;

class AchievementService
{
    private static array $achievements = [
        'first_upload' => [
            'name' => 'First Upload',
            'description' => 'Upload your first torrent',
            'icon' => '📤',
            'points' => 10,
        ],
        'upload_10' => [
            'name' => 'Uploader',
            'description' => 'Upload 10 torrents',
            'icon' => '⭐',
            'points' => 50,
        ],
        'upload_100' => [
            'name' => 'Power Uploader',
            'description' => 'Upload 100 torrents',
            'icon' => '🌟',
            'points' => 200,
        ],
        'ratio_1' => [
            'name' => 'Good Ratio',
            'description' => 'Maintain a ratio of 1.0 or higher',
            'icon' => '⚖️',
            'points' => 25,
        ],
        'ratio_2' => [
            'name' => 'Excellent Ratio',
            'description' => 'Maintain a ratio of 2.0 or higher',
            'icon' => '💎',
            'points' => 100,
        ],
        'upload_1tb' => [
            'name' => 'Terabyte Uploader',
            'description' => 'Upload 1 TB of data',
            'icon' => '💾',
            'points' => 150,
        ],
        'seed_100' => [
            'name' => 'Seeder',
            'description' => 'Seed 100 torrents',
            'icon' => '🌱',
            'points' => 75,
        ],
        'comment_50' => [
            'name' => 'Commenter',
            'description' => 'Post 50 comments',
            'icon' => '💬',
            'points' => 30,
        ],
        'reputation_100' => [
            'name' => 'Respected',
            'description' => 'Reach 100 reputation points',
            'icon' => '👑',
            'points' => 50,
        ],
        'donor' => [
            'name' => 'Donor',
            'description' => 'Donate to the site',
            'icon' => '❤️',
            'points' => 100,
        ],
    ];

    /**
     * Check and award achievements for a user
     */
    public static function checkAchievements(int $userId): array
    {
        $user = User::findById($userId);
        if (!$user) {
            return [];
        }

        $newAchievements = [];
        $uploadCount = (int) Database::fetchOne(
            "SELECT COUNT(*) as count FROM torrents WHERE owner = :user_id",
            ['user_id' => $userId]
        )['count'] ?? 0;

        $uploaded = (int) ($user['uploaded'] ?? 0);
        $downloaded = (int) ($user['downloaded'] ?? 0);
        $ratio = $downloaded > 0 ? $uploaded / $downloaded : 0;

        // Check first upload
        if ($uploadCount >= 1 && !self::hasAchievement($userId, 'first_upload')) {
            self::award($userId, 'first_upload');
            $newAchievements[] = 'first_upload';
        }

        // Check upload milestones
        if ($uploadCount >= 10 && !self::hasAchievement($userId, 'upload_10')) {
            self::award($userId, 'upload_10');
            $newAchievements[] = 'upload_10';
        }

        if ($uploadCount >= 100 && !self::hasAchievement($userId, 'upload_100')) {
            self::award($userId, 'upload_100');
            $newAchievements[] = 'upload_100';
        }

        // Check ratio achievements
        if ($ratio >= 1.0 && !self::hasAchievement($userId, 'ratio_1')) {
            self::award($userId, 'ratio_1');
            $newAchievements[] = 'ratio_1';
        }

        if ($ratio >= 2.0 && !self::hasAchievement($userId, 'ratio_2')) {
            self::award($userId, 'ratio_2');
            $newAchievements[] = 'ratio_2';
        }

        // Check upload size
        if ($uploaded >= 1099511627776 && !self::hasAchievement($userId, 'upload_1tb')) { // 1 TB
            self::award($userId, 'upload_1tb');
            $newAchievements[] = 'upload_1tb';
        }

        // Check reputation
        $reputation = (int) ($user['reputation'] ?? 0);
        if ($reputation >= 100 && !self::hasAchievement($userId, 'reputation_100')) {
            self::award($userId, 'reputation_100');
            $newAchievements[] = 'reputation_100';
        }

        // Check donor
        if (($user['donor'] ?? 'no') === 'yes' && !self::hasAchievement($userId, 'donor')) {
            self::award($userId, 'donor');
            $newAchievements[] = 'donor';
        }

        return $newAchievements;
    }

    /**
     * Award an achievement to a user
     */
    public static function award(int $userId, string $achievementId): bool
    {
        if (!isset(self::$achievements[$achievementId])) {
            return false;
        }

        $achievement = self::$achievements[$achievementId];

        // Check if already awarded
        if (self::hasAchievement($userId, $achievementId)) {
            return false;
        }

        // Record achievement
        Database::execute(
            "INSERT INTO user_achievements (user_id, achievement_id, awarded_at) 
             VALUES (:user_id, :achievement_id, :time)",
            [
                'user_id' => $userId,
                'achievement_id' => $achievementId,
                'time' => time(),
            ]
        );

        // Add points to user
        Database::execute(
            "UPDATE users SET achievement_points = achievement_points + :points WHERE id = :user_id",
            [
                'user_id' => $userId,
                'points' => $achievement['points'],
            ]
        );

        // Clear cache
        Cache::delete("achievements:user:{$userId}");

        return true;
    }

    /**
     * Check if user has an achievement
     */
    public static function hasAchievement(int $userId, string $achievementId): bool
    {
        $result = Database::fetchOne(
            "SELECT id FROM user_achievements WHERE user_id = :user_id AND achievement_id = :achievement_id",
            ['user_id' => $userId, 'achievement_id' => $achievementId]
        );

        return $result !== null;
    }

    /**
     * Get all achievements for a user
     */
    public static function getUserAchievements(int $userId): array
    {
        return Cache::remember(
            "achievements:user:{$userId}",
            function () use ($userId) {
                $earned = Database::fetchAll(
                    "SELECT achievement_id, awarded_at FROM user_achievements WHERE user_id = :user_id",
                    ['user_id' => $userId]
                );

                $earnedIds = array_column($earned, 'achievement_id');
                $earnedMap = array_combine($earnedIds, $earned);

                $all = [];
                foreach (self::$achievements as $id => $achievement) {
                    $all[] = [
                        'id' => $id,
                        'name' => $achievement['name'],
                        'description' => $achievement['description'],
                        'icon' => $achievement['icon'],
                        'points' => $achievement['points'],
                        'earned' => isset($earnedMap[$id]),
                        'awarded_at' => $earnedMap[$id]['awarded_at'] ?? null,
                    ];
                }

                return $all;
            },
            3600
        );
    }

    /**
     * Get leaderboard by achievement points
     */
    public static function getLeaderboard(int $limit = 100): array
    {
        return Cache::remember(
            "achievements:leaderboard",
            function () use ($limit) {
                return Database::fetchAll(
                    "SELECT id, username, achievement_points, 
                            (SELECT COUNT(*) FROM user_achievements WHERE user_id = users.id) as achievement_count
                     FROM users
                     WHERE achievement_points > 0
                     ORDER BY achievement_points DESC, achievement_count DESC
                     LIMIT :limit",
                    ['limit' => $limit]
                );
            },
            300 // Cache for 5 minutes
        );
    }

    /**
     * Get all available achievements
     */
    public static function getAllAchievements(): array
    {
        return self::$achievements;
    }
}

