<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Core\Database;
use App\Models\User;
use Symfony\Component\HttpFoundation\Request;

class UserAdminController
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
        $search = Security::sanitizeInput($request->query->get('search', ''));

        $where = [];
        $params = [];

        if (!empty($search)) {
            $where[] = "(username LIKE :search OR email LIKE :search)";
            $params['search'] = "%{$search}%";
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';
        $params['limit'] = $perPage;
        $params['offset'] = $offset;

        $users = Database::fetchAll(
            "SELECT id, username, email, class, enabled, warned, donor, added, last_access, uploaded, downloaded 
             FROM users {$whereClause} 
             ORDER BY id DESC 
             LIMIT :limit OFFSET :offset",
            $params
        );

        $total = Database::fetchOne(
            "SELECT COUNT(*) as count FROM users {$whereClause}",
            $params
        )['count'] ?? 0;

        $totalPages = ceil($total / $perPage);

        return ResponseHelper::view('admin/users/index', [
            'user' => $user,
            'users' => $users,
            'search' => $search,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pageTitle' => 'User Management',
        ]);
    }

    public function show(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $targetUser = User::findById($id);
        if (!$targetUser) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'User Not Found'], 404);
        }

        // Get user stats
        $torrents = Database::fetchOne(
            "SELECT COUNT(*) as count FROM torrents WHERE owner = :id",
            ['id' => $id]
        )['count'] ?? 0;

        $comments = Database::fetchOne(
            "SELECT COUNT(*) as count FROM comments WHERE user = :id",
            ['id' => $id]
        )['count'] ?? 0;

        $posts = Database::fetchOne(
            "SELECT COUNT(*) as count FROM posts WHERE userid = :id",
            ['id' => $id]
        )['count'] ?? 0;

        return ResponseHelper::view('admin/users/show', [
            'user' => $user,
            'targetUser' => $targetUser,
            'torrents' => $torrents,
            'comments' => $comments,
            'posts' => $posts,
            'pageTitle' => 'User Details - ' . htmlspecialchars($targetUser['username']),
        ]);
    }

    public function edit(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $targetUser = User::findById($id);
        if (!$targetUser) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'User Not Found'], 404);
        }

        if ($request->getMethod() === 'POST') {
            $class = (int) $request->request->get('class', 0);
            $enabled = $request->request->get('enabled', 'yes');
            $warned = $request->request->get('warned', 'no');
            $donor = $request->request->get('donor', 'no');
            $title = Security::sanitizeInput($request->request->get('title', ''));
            $modcomment = Security::sanitizeInput($request->request->get('modcomment', ''));

            Database::execute(
                "UPDATE users SET class = :class, enabled = :enabled, warned = :warned, donor = :donor, title = :title, modcomment = :modcomment WHERE id = :id",
                [
                    'id' => $id,
                    'class' => $class,
                    'enabled' => $enabled,
                    'warned' => $warned,
                    'donor' => $donor,
                    'title' => $title,
                    'modcomment' => $modcomment,
                ]
            );

            return ResponseHelper::redirect("/admin/users/{$id}");
        }

        return ResponseHelper::view('admin/users/edit', [
            'user' => $user,
            'targetUser' => $targetUser,
            'pageTitle' => 'Edit User - ' . htmlspecialchars($targetUser['username']),
        ]);
    }

    public function delete(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $targetUser = User::findById($id);
        if (!$targetUser) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'User Not Found'], 404);
        }

        if ($request->getMethod() === 'POST') {
            $sure = $request->request->get('sure', false);
            if ($sure) {
                Database::execute("DELETE FROM users WHERE id = :id", ['id' => $id]);
                return ResponseHelper::redirect('/admin/users');
            }
        }

        return ResponseHelper::view('admin/users/delete', [
            'user' => $user,
            'targetUser' => $targetUser,
            'pageTitle' => 'Delete User',
        ]);
    }

    public function add(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        if ($request->getMethod() === 'POST') {
            $username = Security::sanitizeInput($request->request->get('username', ''));
            $email = Security::sanitizeInput($request->request->get('email', ''));
            $password = $request->request->get('password', '');
            $class = (int) $request->request->get('class', 0);

            if (empty($username) || empty($email) || empty($password)) {
                return ResponseHelper::view('admin/users/add', [
                    'user' => $user,
                    'error' => 'Username, email, and password are required.',
                    'pageTitle' => 'Add User',
                ]);
            }

            // Check if username exists
            $existing = Database::fetchOne(
                "SELECT id FROM users WHERE username = :username",
                ['username' => $username]
            );

            if ($existing) {
                return ResponseHelper::view('admin/users/add', [
                    'user' => $user,
                    'error' => 'Username already exists.',
                    'pageTitle' => 'Add User',
                ]);
            }

            // Create user
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $passkey = bin2hex(random_bytes(16));
            $secret = bin2hex(random_bytes(10));

            Database::execute(
                "INSERT INTO users (username, email, passhash, passkey, secret, class, status, added, last_access) 
                 VALUES (:username, :email, :password, :passkey, :secret, :class, 'confirmed', :added, :added)",
                [
                    'username' => $username,
                    'email' => $email,
                    'password' => $hashedPassword,
                    'passkey' => $passkey,
                    'secret' => $secret,
                    'class' => $class,
                    'added' => time(),
                ]
            );

            return ResponseHelper::redirect('/admin/users');
        }

        return ResponseHelper::view('admin/users/add', [
            'user' => $user,
            'pageTitle' => 'Add User',
        ]);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $query = Security::sanitizeInput($request->query->get('q', ''));

        if (empty($query)) {
            return ResponseHelper::view('admin/users/search', [
                'user' => $user,
                'results' => [],
                'query' => '',
                'pageTitle' => 'Search Users',
            ]);
        }

        $results = Database::fetchAll(
            "SELECT id, username, email, class, enabled, warned, added, last_access 
             FROM users 
             WHERE username LIKE :query OR email LIKE :query OR ip LIKE :query
             ORDER BY id DESC 
             LIMIT 100",
            ['query' => "%{$query}%"]
        );

        return ResponseHelper::view('admin/users/search', [
            'user' => $user,
            'results' => $results,
            'query' => $query,
            'pageTitle' => 'Search Users',
        ]);
    }
}

