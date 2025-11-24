<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class AnalyticsAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        // Log activity
        \App\Services\ActivityService::log($user['id'], 'admin_analytics_view', 'admin');

        $dateRange = $request->query->get('range', '7d');
        $startDate = $this->getStartDate($dateRange);
        $endDate = time();

        // User activity stats
        $activityStats = Database::fetchAll(
            "SELECT action, COUNT(*) as count
             FROM user_activity
             WHERE created_at >= :start AND created_at <= :end
             GROUP BY action
             ORDER BY count DESC",
            ['start' => $startDate, 'end' => $endDate]
        );

        // Daily user registrations
        $dailyRegistrations = Database::fetchAll(
            "SELECT DATE(FROM_UNIXTIME(added)) as date, COUNT(*) as count
             FROM users
             WHERE added >= :start AND added <= :end
             GROUP BY DATE(FROM_UNIXTIME(added))
             ORDER BY date ASC",
            ['start' => $startDate, 'end' => $endDate]
        );

        // Daily torrent uploads
        $dailyUploads = Database::fetchAll(
            "SELECT DATE(FROM_UNIXTIME(added)) as date, COUNT(*) as count
             FROM torrents
             WHERE added >= :start AND added <= :end
             GROUP BY DATE(FROM_UNIXTIME(added))
             ORDER BY date ASC",
            ['start' => $startDate, 'end' => $endDate]
        );

        // Top active users
        $topUsers = Database::fetchAll(
            "SELECT u.id, u.username, COUNT(ua.id) as activity_count
             FROM users u
             INNER JOIN user_activity ua ON u.id = ua.user_id
             WHERE ua.created_at >= :start AND ua.created_at <= :end
             GROUP BY u.id
             ORDER BY activity_count DESC
             LIMIT 20",
            ['start' => $startDate, 'end' => $endDate]
        );

        // Popular categories
        $popularCategories = Database::fetchAll(
            "SELECT c.id, c.name, COUNT(t.id) as torrent_count
             FROM categories c
             LEFT JOIN torrents t ON c.id = t.category
             WHERE t.added >= :start AND t.added <= :end
             GROUP BY c.id
             ORDER BY torrent_count DESC
             LIMIT 10",
            ['start' => $startDate, 'end' => $endDate]
        );

        // Traffic stats
        $trafficStats = Database::fetchOne(
            "SELECT 
                SUM(uploaded) as total_uploaded,
                SUM(downloaded) as total_downloaded,
                COUNT(DISTINCT user_id) as active_users
             FROM user_activity
             WHERE action IN ('torrent_download', 'torrent_upload')
             AND created_at >= :start AND created_at <= :end",
            ['start' => $startDate, 'end' => $endDate]
        );

        return ResponseHelper::view('admin/analytics/index', [
            'user' => $user,
            'dateRange' => $dateRange,
            'activityStats' => $activityStats,
            'dailyRegistrations' => $dailyRegistrations,
            'dailyUploads' => $dailyUploads,
            'topUsers' => $topUsers,
            'popularCategories' => $popularCategories,
            'trafficStats' => $trafficStats,
            'pageTitle' => 'Analytics Dashboard',
        ]);
    }

    private function getStartDate(string $range): int
    {
        $now = time();
        switch ($range) {
            case '1d':
                return $now - 86400;
            case '7d':
                return $now - (7 * 86400);
            case '30d':
                return $now - (30 * 86400);
            case '90d':
                return $now - (90 * 86400);
            default:
                return $now - (7 * 86400);
        }
    }
}

