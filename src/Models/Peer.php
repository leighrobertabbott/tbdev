<?php

namespace App\Models;

use App\Core\Database;

class Peer
{
    public static function findByTorrent(int $torrentId, bool $seedersOnly = false): array
    {
        $where = "torrent = :torrent_id";
        $params = ['torrent_id' => $torrentId];
        
        if ($seedersOnly) {
            $where .= " AND seeder = 'yes'";
        }
        
        $sql = "SELECT p.*, u.username 
                FROM peers p 
                LEFT JOIN users u ON p.userid = u.id 
                WHERE $where 
                ORDER BY p.started DESC";
        
        return Database::fetchAll($sql, $params);
    }

    public static function findByUser(int $userId): array
    {
        return Database::fetchAll(
            "SELECT p.*, t.name as torrent_name 
             FROM peers p 
             LEFT JOIN torrents t ON p.torrent = t.id 
             WHERE p.userid = :user_id",
            ['user_id' => $userId]
        );
    }

    public static function getStats(int $torrentId): array
    {
        $result = Database::fetchOne(
            "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN seeder = 'yes' THEN 1 ELSE 0 END) as seeders,
                SUM(CASE WHEN seeder = 'no' THEN 1 ELSE 0 END) as leechers,
                SUM(uploaded) as total_uploaded,
                SUM(downloaded) as total_downloaded
             FROM peers 
             WHERE torrent = :torrent_id",
            ['torrent_id' => $torrentId]
        );
        
        return $result ?: ['total' => 0, 'seeders' => 0, 'leechers' => 0, 'total_uploaded' => 0, 'total_downloaded' => 0];
    }
}


