<?php

namespace App\Models;

use App\Core\Database;

class Comment
{
    public static function findByTorrent(int $torrentId): array
    {
        $sql = "SELECT c.*, u.username, u.class 
                FROM comments c 
                LEFT JOIN users u ON c.user = u.id 
                WHERE c.torrent = :torrent_id 
                ORDER BY c.added ASC";
        return Database::fetchAll($sql, ['torrent_id' => $torrentId]);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO comments (user, torrent, added, text, ori_text) 
                VALUES (:user, :torrent, :added, :text, :text)";
        
        $params = [
            'user' => $data['user'],
            'torrent' => $data['torrent'],
            'added' => time(),
            'text' => $data['text'],
        ];

        Database::execute($sql, $params);
        return (int) Database::lastInsertId();
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM comments WHERE id = :id", ['id' => $id]);
    }

    public static function update(int $id, string $text, int $editedBy): bool
    {
        $sql = "UPDATE comments SET text = :text, editedby = :editedby, editedat = :editedat WHERE id = :id";
        return Database::execute($sql, [
            'id' => $id,
            'text' => $text,
            'editedby' => $editedBy,
            'editedat' => time(),
        ]) > 0;
    }

    public static function delete(int $id): bool
    {
        return Database::execute("DELETE FROM comments WHERE id = :id", ['id' => $id]) > 0;
    }
}


