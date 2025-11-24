<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class LogAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        // Delete old logs (older than 7 days)
        $weekAgo = time() - (7 * 24 * 60 * 60);
        Database::execute(
            "DELETE FROM sitelog WHERE added < :time",
            ['time' => $weekAgo]
        );

        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 100;
        $offset = ($page - 1) * $perPage;

        $logs = Database::fetchAll(
            "SELECT * FROM sitelog ORDER BY added DESC LIMIT :limit OFFSET :offset",
            ['limit' => $perPage, 'offset' => $offset]
        );

        $total = Database::fetchOne("SELECT COUNT(*) as count FROM sitelog")['count'] ?? 0;
        $totalPages = ceil($total / $perPage);

        return ResponseHelper::view('admin/logs/index', [
            'user' => $user,
            'logs' => $logs,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pageTitle' => 'Site Logs',
        ]);
    }
}

