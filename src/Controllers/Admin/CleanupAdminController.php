<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class CleanupAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        if ($request->getMethod() === 'POST') {
            $action = $request->request->get('action', '');
            $results = [];

            switch ($action) {
                case 'dead_peers':
                    // Remove peers that haven't announced in 30 minutes
                    $timeout = time() - 1800;
                    $results['dead_peers'] = Database::execute(
                        "DELETE FROM peers WHERE last_action < :timeout",
                        ['timeout' => $timeout]
                    );
                    break;

                case 'old_torrents':
                    // Hide torrents with no seeders for 30 days
                    $oldDate = time() - (30 * 24 * 60 * 60);
                    $results['old_torrents'] = Database::execute(
                        "UPDATE torrents SET visible = 'no' 
                         WHERE visible = 'yes' 
                         AND seeders = 0 
                         AND last_action < :old_date",
                        ['old_date' => $oldDate]
                    );
                    break;

                case 'orphaned_files':
                    // Remove file entries for deleted torrents
                    $results['orphaned_files'] = Database::execute(
                        "DELETE FROM files WHERE torrent NOT IN (SELECT id FROM torrents)"
                    );
                    break;

                case 'orphaned_comments':
                    // Remove comments for deleted torrents
                    $results['orphaned_comments'] = Database::execute(
                        "DELETE FROM comments WHERE torrent NOT IN (SELECT id FROM torrents)"
                    );
                    break;

                case 'old_logs':
                    // Remove logs older than 30 days
                    $oldDate = time() - (30 * 24 * 60 * 60);
                    $results['old_logs'] = Database::execute(
                        "DELETE FROM sitelog WHERE added < :old_date",
                        ['old_date' => $oldDate]
                    );
                    break;
            }

            return ResponseHelper::view('admin/cleanup/index', [
                'user' => $user,
                'results' => $results,
                'pageTitle' => 'Database Cleanup',
            ]);
        }

        // Get stats for display
        $stats = [
            'dead_peers' => Database::fetchOne(
                "SELECT COUNT(*) as count FROM peers WHERE last_action < :timeout",
                ['timeout' => time() - 1800]
            )['count'] ?? 0,
            'old_torrents' => Database::fetchOne(
                "SELECT COUNT(*) as count FROM torrents WHERE visible = 'yes' AND seeders = 0 AND last_action < :old_date",
                ['old_date' => time() - (30 * 24 * 60 * 60)]
            )['count'] ?? 0,
            'orphaned_files' => Database::fetchOne(
                "SELECT COUNT(*) as count FROM files WHERE torrent NOT IN (SELECT id FROM torrents)"
            )['count'] ?? 0,
            'orphaned_comments' => Database::fetchOne(
                "SELECT COUNT(*) as count FROM comments WHERE torrent NOT IN (SELECT id FROM torrents)"
            )['count'] ?? 0,
            'old_logs' => Database::fetchOne(
                "SELECT COUNT(*) as count FROM sitelog WHERE added < :old_date",
                ['old_date' => time() - (30 * 24 * 60 * 60)]
            )['count'] ?? 0,
        ];

        return ResponseHelper::view('admin/cleanup/index', [
            'user' => $user,
            'stats' => $stats,
            'pageTitle' => 'Database Cleanup',
        ]);
    }
}

