<?php

namespace App\Models;

use App\Core\Database;

class Torrent
{
    public static function findById(int $id): ?array
    {
        $sql = "SELECT * FROM torrents WHERE id = :id";
        return Database::fetchOne($sql, ['id' => $id]);
    }

    public static function findAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = ['t.visible = :visible'];
        $params = ['visible' => $filters['visible'] ?? 'yes'];

        if (isset($filters['category'])) {
            $where[] = 't.category = :category';
            $params['category'] = $filters['category'];
        }

        if (isset($filters['search'])) {
            $where[] = '(t.name LIKE :search OR t.filename LIKE :search OR t.descr LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        // Filter by active/dead status based on seeders/leechers
        if (isset($filters['activeOnly']) && $filters['activeOnly']) {
            // Active only: must have at least 1 seeder or 1 leecher
            $where[] = "EXISTS (SELECT 1 FROM peers p WHERE p.torrent = t.id AND (p.seeder = 'yes' OR p.seeder = 'no'))";
        } elseif (isset($filters['deadOnly']) && $filters['deadOnly']) {
            // Dead only: must have 0 seeders and 0 leechers
            $where[] = "NOT EXISTS (SELECT 1 FROM peers p WHERE p.torrent = t.id)";
        }
        // If includeDead is true, don't add any filter (show all)

        $sql = "SELECT t.*, 
                       u.username as owner_name, 
                       c.name as category_name,
                       COALESCE((SELECT COUNT(*) FROM peers WHERE torrent = t.id AND seeder = 'yes'), 0) as seeders,
                       COALESCE((SELECT COUNT(*) FROM peers WHERE torrent = t.id AND seeder = 'no'), 0) as leechers
                FROM torrents t
                LEFT JOIN users u ON t.owner = u.id
                LEFT JOIN categories c ON t.category = c.id
                WHERE " . implode(' AND ', $where) . " 
                ORDER BY t.id DESC 
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        return Database::fetchAll($sql, $params);
    }

    public static function count(array $filters = []): int
    {
        $where = ['visible = :visible'];
        $params = ['visible' => $filters['visible'] ?? 'yes'];

        if (isset($filters['category'])) {
            $where[] = 'category = :category';
            $params['category'] = $filters['category'];
        }

        // Filter by active/dead status based on seeders/leechers
        if (isset($filters['activeOnly']) && $filters['activeOnly']) {
            // Active only: must have at least 1 seeder or 1 leecher
            $where[] = "EXISTS (SELECT 1 FROM peers p WHERE p.torrent = torrents.id AND (p.seeder = 'yes' OR p.seeder = 'no'))";
        } elseif (isset($filters['deadOnly']) && $filters['deadOnly']) {
            // Dead only: must have 0 seeders and 0 leechers
            $where[] = "NOT EXISTS (SELECT 1 FROM peers p WHERE p.torrent = torrents.id)";
        }
        // If includeDead is true, don't add any filter (show all)

        $sql = "SELECT COUNT(*) as count FROM torrents WHERE " .
            implode(' AND ', $where);
        $result = Database::fetchOne($sql, $params);
        return (int) ($result['count'] ?? 0);
    }

    public static function create(array $data): int
    {
        $sql = "INSERT INTO torrents (name, filename, owner, category, size, added, info_hash, visible) 
                VALUES (:name, :filename, :owner, :category, :size, :added, :info_hash, :visible)";
        
        $params = [
            'name' => $data['name'],
            'filename' => $data['filename'],
            'owner' => $data['owner'],
            'category' => $data['category'],
            'size' => $data['size'],
            'added' => time(),
            'info_hash' => $data['info_hash'],
            'visible' => 'yes',
        ];

        Database::execute($sql, $params);
        return (int) Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $allowed = ['name', 'category', 'visible', 'banned'];
        $updates = [];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE torrents SET " .
            implode(', ', $updates) .
            " WHERE id = :id";
        $data['id'] = $id;
        
        return Database::execute($sql, $data) > 0;
    }

    public static function search(string $query, array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = ['visible = :visible'];
        $params = ['visible' => $filters['visible'] ?? 'yes'];

        if (!empty($query)) {
            // Use LIKE as fallback (more compatible than FULLTEXT)
            $where[] = '(name LIKE :search OR filename LIKE :search OR descr LIKE :search)';
            $params['search'] = '%' . $query . '%';
        }

        if (isset($filters['category'])) {
            $where[] = 'category = :category';
            $params['category'] = $filters['category'];
        }

        $sql = "SELECT t.*, u.username as owner_name, c.name as category_name,
                       (SELECT COUNT(*) FROM peers WHERE torrent = t.id AND seeder = 'yes') as seeders,
                       (SELECT COUNT(*) FROM peers WHERE torrent = t.id AND seeder = 'no') as leechers,
                       t.times_completed
                FROM torrents t
                LEFT JOIN users u ON t.owner = u.id
                LEFT JOIN categories c ON t.category = c.id
                WHERE " . implode(' AND ', $where) . " 
                ORDER BY t.id DESC 
                LIMIT " . (int)$limit . " OFFSET " . (int)$offset;

        return Database::fetchAll($sql, $params);
    }

    public static function countSearch(string $query, array $filters = []): int
    {
        $where = ['visible = :visible'];
        $params = ['visible' => $filters['visible'] ?? 'yes'];

        if (!empty($query)) {
            $where[] = '(name LIKE :search OR filename LIKE :search OR descr LIKE :search)';
            $params['search'] = '%' . $query . '%';
        }

        if (isset($filters['category'])) {
            $where[] = 'category = :category';
            $params['category'] = $filters['category'];
        }

        $sql = "SELECT COUNT(*) as count FROM torrents WHERE " .
            implode(' AND ', $where);
        $result = Database::fetchOne($sql, $params);
        return (int) ($result['count'] ?? 0);
    }

    public static function getWithDetails(int $id): ?array
    {
        $sql = "SELECT t.*, u.username as owner_name, c.name as category_name 
                FROM torrents t 
                LEFT JOIN users u ON t.owner = u.id 
                LEFT JOIN categories c ON t.category = c.id 
                WHERE t.id = :id";
        return Database::fetchOne($sql, ['id' => $id]);
    }

    public static function getPeerStats(int $id): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_peers,
                    SUM(CASE WHEN seeder = 'yes' THEN 1 ELSE 0 END) as seeders,
                    SUM(CASE WHEN seeder = 'no' THEN 1 ELSE 0 END) as leechers
                FROM peers 
                WHERE torrent = :id";
        return Database::fetchOne($sql, ['id' => $id]) ?: ['total_peers' => 0, 'seeders' => 0, 'leechers' => 0];
    }
}

