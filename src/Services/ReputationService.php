<?php

namespace App\Services;

use App\Core\Database;
use App\Models\User;

class ReputationService
{
    public static function give(int $userId, int $giverId, int $amount, string $reason = ''): bool
    {
        // Prevent self-reputation
        if ($userId === $giverId) {
            return false;
        }

        // Check if already given today
        $today = strtotime('today');
        $existing = Database::fetchOne(
            "SELECT id FROM reputation WHERE user_id = :user_id AND giver_id = :giver_id AND created_at >= :today",
            ['user_id' => $userId, 'giver_id' => $giverId, 'today' => $today]
        );

        if ($existing) {
            return false;
        }

        // Add reputation record
        Database::execute(
            "INSERT INTO reputation (user_id, giver_id, amount, reason, created_at) 
             VALUES (:user_id, :giver_id, :amount, :reason, :time)",
            [
                'user_id' => $userId,
                'giver_id' => $giverId,
                'amount' => $amount,
                'reason' => $reason,
                'time' => time(),
            ]
        );

        // Update user reputation
        Database::execute(
            "UPDATE users SET reputation = reputation + :amount WHERE id = :user_id",
            ['user_id' => $userId, 'amount' => $amount]
        );

        return true;
    }

    public static function getUserReputation(int $userId): int
    {
        $user = User::findById($userId);
        return (int) ($user['reputation'] ?? 0);
    }

    public static function getReputationLevel(int $reputation): string
    {
        // Load reputation levels from cache or database
        $levels = self::getReputationLevels();
        
        $level = 'Neutral';
        foreach ($levels as $minRep => $levelName) {
            if ($reputation >= $minRep) {
                $level = $levelName;
            } else {
                break;
            }
        }
        
        return $level;
    }

    private static function getReputationLevels(): array
    {
        // This would typically load from cache/rep_settings_cache.php
        return [
            -500 => 'Terrible',
            -100 => 'Bad',
            0 => 'Neutral',
            100 => 'Good',
            500 => 'Excellent',
            1000 => 'Outstanding',
        ];
    }
}


