<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Services\AchievementService;
use Symfony\Component\HttpFoundation\Request;

class AchievementController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $achievements = AchievementService::getUserAchievements($user['id']);
        $earnedCount = count(array_filter($achievements, fn($a) => $a['earned']));
        $totalPoints = array_sum(array_column(array_filter($achievements, fn($a) => $a['earned']), 'points'));

        return ResponseHelper::view('achievements/index', [
            'user' => $user,
            'achievements' => $achievements,
            'earnedCount' => $earnedCount,
            'totalPoints' => $totalPoints,
            'pageTitle' => 'My Achievements',
        ]);
    }

    public function leaderboard(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $leaderboard = AchievementService::getLeaderboard(100);

        return ResponseHelper::view('achievements/leaderboard', [
            'user' => $user,
            'leaderboard' => $leaderboard,
            'pageTitle' => 'Achievement Leaderboard',
        ]);
    }
}

