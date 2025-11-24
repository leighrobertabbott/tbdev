<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class StatsAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        // Get comprehensive stats
        $stats = [
            'users' => Database::fetchOne("SELECT COUNT(*) as count FROM users")['count'] ?? 0,
            'users_confirmed' => Database::fetchOne("SELECT COUNT(*) as count FROM users WHERE status = 'confirmed'")['count'] ?? 0,
            'users_pending' => Database::fetchOne("SELECT COUNT(*) as count FROM users WHERE status = 'pending'")['count'] ?? 0,
            'torrents' => Database::fetchOne("SELECT COUNT(*) as count FROM torrents")['count'] ?? 0,
            'torrents_visible' => Database::fetchOne("SELECT COUNT(*) as count FROM torrents WHERE visible = 'yes'")['count'] ?? 0,
            'torrents_hidden' => Database::fetchOne("SELECT COUNT(*) as count FROM torrents WHERE visible = 'no'")['count'] ?? 0,
            'comments' => Database::fetchOne("SELECT COUNT(*) as count FROM comments")['count'] ?? 0,
            'topics' => Database::fetchOne("SELECT COUNT(*) as count FROM topics")['count'] ?? 0,
            'posts' => Database::fetchOne("SELECT COUNT(*) as count FROM posts")['count'] ?? 0,
            'messages' => Database::fetchOne("SELECT COUNT(*) as count FROM messages")['count'] ?? 0,
        ];

        // Peer stats
        $peerStats = Database::fetchOne(
            "SELECT 
                COUNT(*) as total_peers,
                SUM(CASE WHEN seeder = 'yes' THEN 1 ELSE 0 END) as seeders,
                SUM(CASE WHEN seeder = 'no' THEN 1 ELSE 0 END) as leechers
             FROM peers"
        );

        // Upload/Download stats
        $trafficStats = Database::fetchOne(
            "SELECT 
                SUM(uploaded) as total_uploaded,
                SUM(downloaded) as total_downloaded
             FROM users"
        );

        // Top uploaders
        $topUploaders = Database::fetchAll(
            "SELECT id, username, uploaded, downloaded, 
                    (uploaded / NULLIF(downloaded, 0)) as ratio
             FROM users 
             WHERE uploaded > 0 
             ORDER BY uploaded DESC 
             LIMIT 10"
        );

        // Top torrents
        $topTorrents = Database::fetchAll(
            "SELECT id, name, seeders, leechers, times_completed, size
             FROM torrents 
             WHERE visible = 'yes'
             ORDER BY seeders DESC 
             LIMIT 10"
        );

        return ResponseHelper::view('admin/stats/index', [
            'user' => $user,
            'stats' => $stats,
            'peerStats' => $peerStats,
            'trafficStats' => $trafficStats,
            'topUploaders' => $topUploaders,
            'topTorrents' => $topTorrents,
            'pageTitle' => 'Statistics',
        ]);
    }
}

