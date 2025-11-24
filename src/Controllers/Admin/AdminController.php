<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Models\User;
use App\Models\Torrent;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class AdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        // Get stats
        $stats = [
            'users' => User::count(),
            'torrents' => Torrent::count(),
            'pending_torrents' => Torrent::count(['visible' => 'no']),
        ];

        $peerStats = Database::fetchOne(
            "SELECT 
                SUM(CASE WHEN seeder = 'yes' THEN 1 ELSE 0 END) as seeders,
                SUM(CASE WHEN seeder = 'no' THEN 1 ELSE 0 END) as leechers
             FROM peers"
        );

        return ResponseHelper::view('admin/index', [
            'user' => $user,
            'stats' => $stats,
            'peerStats' => $peerStats,
            'pageTitle' => 'Admin Panel',
        ]);
    }
}

