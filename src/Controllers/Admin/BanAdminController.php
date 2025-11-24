<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class BanAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $bans = Database::fetchAll(
            "SELECT b.*, u.username as addedby_name 
             FROM bans b 
             LEFT JOIN users u ON b.addedby = u.id 
             ORDER BY b.added DESC"
        );

        return ResponseHelper::view('admin/bans/index', [
            'user' => $user,
            'bans' => $bans,
            'pageTitle' => 'Manage Bans',
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        if ($request->getMethod() === 'POST') {
            $first = $request->request->get('first', '');
            $last = $request->request->get('last', '');
            $comment = Security::sanitizeInput($request->request->get('comment', ''));

            $firstIp = ip2long($first);
            $lastIp = ip2long($last);

            if ($firstIp && $lastIp) {
                Database::execute(
                    "INSERT INTO bans (added, addedby, comment, first, last) VALUES (:added, :addedby, :comment, :first, :last)",
                    [
                        'added' => time(),
                        'addedby' => $user['id'],
                        'comment' => $comment,
                        'first' => $firstIp,
                        'last' => $lastIp,
                    ]
                );
            }

            return ResponseHelper::redirect('/admin/bans');
        }

        return ResponseHelper::view('admin/bans/create', [
            'user' => $user,
            'pageTitle' => 'Add Ban',
        ]);
    }

    public function delete(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        Database::execute("DELETE FROM bans WHERE id = :id", ['id' => $id]);
        return ResponseHelper::redirect('/admin/bans');
    }
}

