<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class IpTestAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $ip = $request->query->get('ip', '');
        $result = null;

        if (!empty($ip)) {
            $ipLong = ip2long($ip);
            if ($ipLong) {
                $ban = Database::fetchOne(
                    "SELECT * FROM bans WHERE :ip BETWEEN first AND last",
                    ['ip' => $ipLong]
                );

                $result = [
                    'ip' => $ip,
                    'ip_long' => $ipLong,
                    'banned' => $ban !== null,
                    'ban' => $ban,
                ];
            } else {
                $result = [
                    'ip' => $ip,
                    'error' => 'Invalid IP address',
                ];
            }
        }

        return ResponseHelper::view('admin/iptest/index', [
            'user' => $user,
            'result' => $result,
            'pageTitle' => 'IP Test',
        ]);
    }
}

