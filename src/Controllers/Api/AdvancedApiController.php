<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Models\Torrent;
use App\Models\User;
use App\Core\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AdvancedApiController
{
    public function stats(Request $request): JsonResponse
    {
        $stats = [
            'users' => User::count(),
            'torrents' => Torrent::count(),
            'peers' => Database::fetchOne("SELECT COUNT(*) as count FROM peers")['count'] ?? 0,
        ];

        $peerStats = Database::fetchOne(
            "SELECT 
                SUM(CASE WHEN seeder = 'yes' THEN 1 ELSE 0 END) as seeders,
                SUM(CASE WHEN seeder = 'no' THEN 1 ELSE 0 END) as leechers
             FROM peers"
        );

        $stats['seeders'] = (int) ($peerStats['seeders'] ?? 0);
        $stats['leechers'] = (int) ($peerStats['leechers'] ?? 0);

        return new JsonResponse(['data' => $stats]);
    }

    public function search(Request $request): JsonResponse
    {
        Auth::requireAuth();

        $query = $request->query->get('q', '');
        $category = (int) $request->query->get('category', 0);
        $sort = $request->query->get('sort', 'newest');
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $filters = ['visible' => 'yes'];
        if ($category > 0) {
            $filters['category'] = $category;
        }

        $torrents = Torrent::search($query, $filters, $perPage, $offset);
        $total = Torrent::count($filters);

        // Apply sorting
        if ($sort === 'seeders') {
            usort($torrents, fn($a, $b) => ($b['seeders'] ?? 0) <=> ($a['seeders'] ?? 0));
        } elseif ($sort === 'size') {
            usort($torrents, fn($a, $b) => ($b['size'] ?? 0) <=> ($a['size'] ?? 0));
        }

        return new JsonResponse([
            'data' => $torrents,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'pages' => ceil($total / $perPage),
            ],
        ]);
    }

    public function categories(Request $request): JsonResponse
    {
        $categories = \App\Models\Category::all();
        return new JsonResponse(['data' => $categories]);
    }

    public function userStats(Request $request, int $id): JsonResponse
    {
        Auth::requireAuth();

        $user = User::findById($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $stats = [
            'id' => $user['id'],
            'username' => $user['username'],
            'uploaded' => (int) ($user['uploaded'] ?? 0),
            'downloaded' => (int) ($user['downloaded'] ?? 0),
            'ratio' => \App\Core\FormatHelper::ratio($user['uploaded'] ?? 0, $user['downloaded'] ?? 0),
        ];

        return new JsonResponse(['data' => $stats]);
    }
}

