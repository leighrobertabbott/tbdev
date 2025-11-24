<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class CollectionController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $collections = Database::fetchAll(
            "SELECT c.*, COUNT(ci.id) as torrent_count
             FROM collections c
             LEFT JOIN collection_items ci ON c.id = ci.collection_id
             WHERE c.user_id = :user_id
             GROUP BY c.id
             ORDER BY c.updated_at DESC",
            ['user_id' => $user['id']]
        );

        return ResponseHelper::view('collections/index', [
            'user' => $user,
            'collections' => $collections,
            'pageTitle' => 'My Collections',
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $collection = Database::fetchOne(
            "SELECT * FROM collections WHERE id = :id",
            ['id' => $id]
        );

        if (!$collection) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Collection Not Found'], 404);
        }

        if ($collection['user_id'] != $user['id'] && $collection['is_public'] !== 'yes') {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $torrents = Database::fetchAll(
            "SELECT t.*, u.username as owner_name, c.name as category_name
             FROM collection_items ci
             INNER JOIN torrents t ON ci.torrent_id = t.id
             LEFT JOIN users u ON t.owner = u.id
             LEFT JOIN categories c ON t.category = c.id
             WHERE ci.collection_id = :id
             ORDER BY ci.added_at DESC",
            ['id' => $id]
        );

        return ResponseHelper::view('collections/show', [
            'user' => $user,
            'collection' => $collection,
            'torrents' => $torrents,
            'pageTitle' => htmlspecialchars($collection['name']),
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        if ($request->getMethod() === 'POST') {
            $name = Security::sanitizeInput($request->request->get('name', ''));
            $description = Security::sanitizeInput($request->request->get('description', ''));
            $isPublic = $request->request->get('is_public', 'no') === 'yes' ? 'yes' : 'no';

            if (empty($name)) {
                return ResponseHelper::view('collections/create', [
                    'user' => $user,
                    'error' => 'Collection name is required',
                    'pageTitle' => 'Create Collection',
                ]);
            }

            $now = time();
            Database::execute(
                "INSERT INTO collections (user_id, name, description, is_public, created_at, updated_at)
                 VALUES (:user_id, :name, :description, :is_public, :created_at, :updated_at)",
                [
                    'user_id' => $user['id'],
                    'name' => $name,
                    'description' => $description,
                    'is_public' => $isPublic,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]
            );

            return ResponseHelper::redirect('/collections');
        }

        return ResponseHelper::view('collections/create', [
            'user' => $user,
            'pageTitle' => 'Create Collection',
        ]);
    }

    public function addTorrent(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::json(['error' => 'Unauthorized'], 401);
        }

        $collection = Database::fetchOne(
            "SELECT * FROM collections WHERE id = :id",
            ['id' => $id]
        );

        if (!$collection || $collection['user_id'] != $user['id']) {
            return ResponseHelper::json(['error' => 'Collection not found'], 404);
        }

        $torrentId = (int) $request->request->get('torrent_id', 0);

        if (!$torrentId) {
            return ResponseHelper::json(['error' => 'Torrent ID required'], 400);
        }

        // Check if already in collection
        $existing = Database::fetchOne(
            "SELECT id FROM collection_items WHERE collection_id = :collection_id AND torrent_id = :torrent_id",
            ['collection_id' => $id, 'torrent_id' => $torrentId]
        );

        if ($existing) {
            return ResponseHelper::json(['error' => 'Torrent already in collection'], 400);
        }

        Database::execute(
            "INSERT INTO collection_items (collection_id, torrent_id, added_at) VALUES (:collection_id, :torrent_id, :time)",
            ['collection_id' => $id, 'torrent_id' => $torrentId, 'time' => time()]
        );

        Database::execute(
            "UPDATE collections SET updated_at = :time WHERE id = :id",
            ['id' => $id, 'time' => time()]
        );

        return ResponseHelper::json(['success' => true]);
    }

    public function removeTorrent(Request $request, int $id, int $torrentId)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::json(['error' => 'Unauthorized'], 401);
        }

        $collection = Database::fetchOne(
            "SELECT * FROM collections WHERE id = :id",
            ['id' => $id]
        );

        if (!$collection || $collection['user_id'] != $user['id']) {
            return ResponseHelper::json(['error' => 'Collection not found'], 404);
        }

        Database::execute(
            "DELETE FROM collection_items WHERE collection_id = :collection_id AND torrent_id = :torrent_id",
            ['collection_id' => $id, 'torrent_id' => $torrentId]
        );

        return ResponseHelper::json(['success' => true]);
    }

    public function delete(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $collection = Database::fetchOne(
            "SELECT * FROM collections WHERE id = :id",
            ['id' => $id]
        );

        if (!$collection || $collection['user_id'] != $user['id']) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        if ($request->getMethod() === 'POST') {
            Database::execute("DELETE FROM collection_items WHERE collection_id = :id", ['id' => $id]);
            Database::execute("DELETE FROM collections WHERE id = :id", ['id' => $id]);
            return ResponseHelper::redirect('/collections');
        }

        return ResponseHelper::view('collections/delete', [
            'user' => $user,
            'collection' => $collection,
            'pageTitle' => 'Delete Collection',
        ]);
    }
}

