<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Models\Torrent;
use App\Core\Database;
use App\Services\RecommendationService;
use App\Services\ActivityService;
use Symfony\Component\HttpFoundation\Request;

class TorrentController
{
    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $torrent = Torrent::findById($id);
        if (!$torrent || $torrent['visible'] !== 'yes') {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Torrent Not Found'], 404);
        }

        // Increment views
        Database::execute(
            "UPDATE torrents SET views = views + 1 WHERE id = :id",
            ['id' => $id]
        );

        // Get category
        $category = Database::fetchOne(
            "SELECT name FROM categories WHERE id = :id",
            ['id' => $torrent['category']]
        );

        // Get owner
        $owner = Database::fetchOne(
            "SELECT id, username FROM users WHERE id = :id",
            ['id' => $torrent['owner']]
        );

        // Get peer stats
        $peerStats = Database::fetchOne(
            "SELECT 
                COUNT(*) as total_peers,
                COALESCE(SUM(CASE WHEN seeder = 'yes' THEN 1 ELSE 0 END), 0) as seeders,
                COALESCE(SUM(CASE WHEN seeder = 'no' THEN 1 ELSE 0 END), 0) as leechers
             FROM peers WHERE torrent = :torrent",
            ['torrent' => $id]
        );
        
        // Ensure all values are integers (COALESCE should handle this, but be safe)
        $peerStats = [
            'total_peers' => (int) ($peerStats['total_peers'] ?? 0),
            'seeders' => (int) ($peerStats['seeders'] ?? 0),
            'leechers' => (int) ($peerStats['leechers'] ?? 0),
        ];

        // Get files
        $files = Database::fetchAll(
            "SELECT * FROM files WHERE torrent = :torrent ORDER BY id ASC",
            ['torrent' => $id]
        );

        // Get comments
        $comments = Database::fetchAll(
            "SELECT c.*, u.username, u.class, u.donor, u.warned, u.enabled
             FROM comments c
             LEFT JOIN users u ON c.user = u.id
             WHERE c.torrent = :torrent
             ORDER BY c.added ASC",
            ['torrent' => $id]
        );

        $torrent['category_name'] = $category['name'] ?? 'N/A';
        $torrent['owner_name'] = $owner['username'] ?? 'Unknown';
        $owned = ($user['id'] ?? 0) == $torrent['owner'];

        // Get related torrents
        $related = RecommendationService::getRelated($id, 6);

        // Log activity
        ActivityService::log($user['id'], 'torrent_view', 'torrent', $id);

        return ResponseHelper::view('torrent/details', [
            'user' => $user,
            'torrent' => $torrent,
            'peerStats' => $peerStats,
            'files' => $files,
            'comments' => $comments,
            'related' => $related,
            'owned' => $owned,
            'pageTitle' => htmlspecialchars($torrent['name']),
        ]);
    }

    public function viewNfo(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $torrent = Torrent::findById($id);
        if (!$torrent || $torrent['visible'] !== 'yes') {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Torrent Not Found'], 404);
        }

        if (empty($torrent['nfo'])) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'NFO Not Found'], 404);
        }

        return ResponseHelper::view('torrent/nfo', [
            'user' => $user,
            'torrent' => $torrent,
            'nfo' => $torrent['nfo'],
            'pageTitle' => 'NFO - ' . htmlspecialchars($torrent['name']),
        ]);
    }

    public function fileList(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $torrent = Torrent::findById($id);
        if (!$torrent || $torrent['visible'] !== 'yes') {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Torrent Not Found'], 404);
        }

        $files = Database::fetchAll(
            "SELECT * FROM files WHERE torrent = :torrent ORDER BY id ASC",
            ['torrent' => $id]
        );

        return ResponseHelper::view('torrent/filelist', [
            'user' => $user,
            'torrent' => $torrent,
            'files' => $files,
            'pageTitle' => 'File List - ' . htmlspecialchars($torrent['name']),
        ]);
    }

    public function peerList(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $torrent = Torrent::findById($id);
        if (!$torrent || $torrent['visible'] !== 'yes') {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Torrent Not Found'], 404);
        }

        $peers = Database::fetchAll(
            "SELECT p.*, u.username, u.class, u.donor
             FROM peers p
             LEFT JOIN users u ON p.userid = u.id
             WHERE p.torrent = :torrent
             ORDER BY p.started DESC",
            ['torrent' => $id]
        );

        return ResponseHelper::view('torrent/peerlist', [
            'user' => $user,
            'torrent' => $torrent,
            'peers' => $peers,
            'pageTitle' => 'Peer List - ' . htmlspecialchars($torrent['name']),
        ]);
    }
}
