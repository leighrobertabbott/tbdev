<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Core\Database;
use App\Models\Torrent;
use Symfony\Component\HttpFoundation\Request;

class TorrentAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;
        $status = $request->query->get('status', 'all');
        $search = Security::sanitizeInput($request->query->get('search', ''));

        $where = [];
        $params = [];

        if ($status === 'visible') {
            $where[] = "visible = 'yes'";
        } elseif ($status === 'hidden') {
            $where[] = "visible = 'no'";
        } elseif ($status === 'banned') {
            $where[] = "banned = 'yes'";
        }

        if (!empty($search)) {
            $where[] = "(name LIKE :search OR filename LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        
        // Remove limit/offset from params for COUNT query
        $countParams = $params;

        $torrents = Database::fetchAll(
            "SELECT t.*, u.username as owner_name, c.name as category_name
             FROM torrents t
             LEFT JOIN users u ON t.owner = u.id
             LEFT JOIN categories c ON t.category = c.id
             {$whereClause}
             ORDER BY t.id DESC
             LIMIT " . (int)$perPage . " OFFSET " . (int)$offset,
            $params
        );

        $total = Database::fetchOne(
            "SELECT COUNT(*) as count FROM torrents {$whereClause}",
            $countParams
        )['count'] ?? 0;

        $totalPages = ceil($total / $perPage);

        return ResponseHelper::view('admin/torrents/index', [
            'user' => $user,
            'torrents' => $torrents,
            'status' => $status,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pageTitle' => 'Torrent Management',
        ]);
    }

    public function edit(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $torrent = Torrent::findById($id);
        if (!$torrent) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Torrent Not Found'], 404);
        }

        if ($request->getMethod() === 'POST') {
            $name = Security::sanitizeInput($request->request->get('name', ''));
            $category = (int) $request->request->get('category', 0);
            $visible = $request->request->get('visible', 'yes');
            $banned = $request->request->get('banned', 'no');
            $description = Security::sanitizeInput($request->request->get('description', ''));

            Database::execute(
                "UPDATE torrents SET name = :name, category = :category, visible = :visible, banned = :banned, descr = :description WHERE id = :id",
                [
                    'id' => $id,
                    'name' => $name,
                    'category' => $category,
                    'visible' => $visible,
                    'banned' => $banned,
                    'description' => $description,
                ]
            );

            return ResponseHelper::redirect("/admin/torrents");
        }

        $categories = Database::fetchAll("SELECT * FROM categories ORDER BY name");

        return ResponseHelper::view('admin/torrents/edit', [
            'user' => $user,
            'torrent' => $torrent,
            'categories' => $categories,
            'pageTitle' => 'Edit Torrent',
        ]);
    }

    public function delete(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $torrent = Torrent::findById($id);
        if (!$torrent) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Torrent Not Found'], 404);
        }

        if ($request->getMethod() === 'POST') {
            $sure = $request->request->get('sure', false);
            if ($sure) {
                Database::execute("DELETE FROM torrents WHERE id = :id", ['id' => $id]);
                // Also delete related files, comments, etc.
                Database::execute("DELETE FROM files WHERE torrent = :id", ['id' => $id]);
                Database::execute("DELETE FROM comments WHERE torrent = :id", ['id' => $id]);
                return ResponseHelper::redirect('/admin/torrents');
            }
        }

        return ResponseHelper::view('admin/torrents/delete', [
            'user' => $user,
            'torrent' => $torrent,
            'pageTitle' => 'Delete Torrent',
        ]);
    }
}

