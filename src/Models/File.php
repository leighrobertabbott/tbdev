<?php

namespace App\Models;

use App\Core\Database;

class File
{
    public static function findByTorrent(int $torrentId): array
    {
        return Database::fetchAll(
            "SELECT * FROM files WHERE torrent = :torrent_id ORDER BY filename",
            ['torrent_id' => $torrentId]
        );
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO files (torrent, filename, size) 
                VALUES (:torrent, :filename, :size)";
        
        Database::execute($sql, [
            'torrent' => $data['torrent'],
            'filename' => $data['filename'],
            'size' => $data['size'],
        ]);
        
        return (int) Database::lastInsertId();
    }

    public static function deleteByTorrent(int $torrentId): bool
    {
        return Database::execute(
            "DELETE FROM files WHERE torrent = :torrent_id",
            ['torrent_id' => $torrentId]
        ) > 0;
    }
}


