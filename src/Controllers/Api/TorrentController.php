<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Models\Torrent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TorrentController
{
    public function index(Request $request): JsonResponse
    {
        Auth::requireAuth();

        $filters = [];
        
        if ($request->query->has('category')) {
            $filters['category'] = (int) $request->query->get('category');
        }
        
        if ($request->query->has('search')) {
            $filters['search'] = $request->query->get('search');
        }

        $page = max(1, (int) ($request->query->get('page', 1)));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $torrents = Torrent::findAll($filters, $perPage, $offset);
        $total = Torrent::count($filters);

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

    public function show(Request $request, int $id): JsonResponse
    {
        Auth::requireAuth();

        $torrent = Torrent::findById($id);
        
        if (!$torrent) {
            return new JsonResponse(['error' => 'Torrent not found'], 404);
        }

        return new JsonResponse(['data' => $torrent]);
    }

    public function create(Request $request): JsonResponse
    {
        Auth::requireAuth();

        $user = Auth::user();
        $data = json_decode($request->getContent(), true);

        // Validation would go here
        $torrentId = Torrent::create([
            'name' => $data['name'] ?? '',
            'filename' => $data['filename'] ?? '',
            'owner' => $user['id'],
            'category' => $data['category'] ?? 0,
            'size' => $data['size'] ?? 0,
            'info_hash' => $data['info_hash'] ?? '',
        ]);

        return new JsonResponse(['id' => $torrentId], 201);
    }
}


