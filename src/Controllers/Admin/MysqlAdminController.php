<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class MysqlAdminController
{
    public function stats(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $db = Database::getInstance();

        // Get table sizes
        $tables = Database::fetchAll(
            "SELECT 
                table_name,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                table_rows
             FROM information_schema.TABLES 
             WHERE table_schema = DATABASE()
             ORDER BY (data_length + index_length) DESC"
        );

        // Get database size
        $dbSize = Database::fetchOne(
            "SELECT 
                ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
             FROM information_schema.TABLES 
             WHERE table_schema = DATABASE()"
        );

        // Get MySQL version
        $version = $db
            ->query("SELECT VERSION() as version")
            ->fetch()['version'] ?? 'Unknown';

        return ResponseHelper::view('admin/mysql/stats', [
            'user' => $user,
            'tables' => $tables,
            'dbSize' => $dbSize,
            'version' => $version,
            'pageTitle' => 'MySQL Statistics',
        ]);
    }

    public function overview(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $db = Database::getInstance();

        // Get all tables with row counts
        $tables = Database::fetchAll(
            "SELECT 
                table_name,
                table_rows,
                ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb,
                ROUND((data_length / 1024 / 1024), 2) AS data_mb,
                ROUND((index_length / 1024 / 1024), 2) AS index_mb
             FROM information_schema.TABLES 
             WHERE table_schema = DATABASE()
             ORDER BY table_name"
        );

        // Get status variables
        $status = Database::fetchAll("SHOW STATUS");

        return ResponseHelper::view('admin/mysql/overview', [
            'user' => $user,
            'tables' => $tables,
            'status' => $status,
            'pageTitle' => 'MySQL Overview',
        ]);
    }
}

