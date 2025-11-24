<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class UserHistoryController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $userId = (int) $request->query->get('id', $user['id']);

        // Check permissions
        if ($userId != $user['id'] && ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $action = $request->query->get('action', 'posts');
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 25;
        $offset = ($page - 1) * $perPage;

        $data = [];

        if ($action === 'posts') {
            $data = Database::fetchAll(
                "SELECT p.*, t.subject as topic_subject, f.name as forum_name 
                 FROM posts p 
                 LEFT JOIN topics t ON p.topic = t.id 
                 LEFT JOIN forums f ON t.forum = f.id 
                 WHERE p.author = :user_id 
                 ORDER BY p.added DESC 
                 LIMIT :limit OFFSET :offset",
                ['user_id' => $userId, 'limit' => $perPage, 'offset' => $offset]
            );
        } elseif ($action === 'comments') {
            $data = Database::fetchAll(
                "SELECT c.*, t.name as torrent_name 
                 FROM comments c 
                 LEFT JOIN torrents t ON c.torrent = t.id 
                 WHERE c.user = :user_id 
                 ORDER BY c.added DESC 
                 LIMIT :limit OFFSET :offset",
                ['user_id' => $userId, 'limit' => $perPage, 'offset' => $offset]
            );
        } elseif ($action === 'torrents') {
            $data = Database::fetchAll(
                "SELECT * FROM torrents 
                 WHERE owner = :user_id 
                 ORDER BY added DESC 
                 LIMIT :limit OFFSET :offset",
                ['user_id' => $userId, 'limit' => $perPage, 'offset' => $offset]
            );
        }

        return ResponseHelper::view('user/history', [
            'user' => $user,
            'viewUserId' => $userId,
            'action' => $action,
            'data' => $data,
            'page' => $page,
            'pageTitle' => 'User History',
        ]);
    }
}

