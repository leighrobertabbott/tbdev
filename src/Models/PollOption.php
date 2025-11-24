<?php

namespace App\Models;

use App\Core\Database;

class PollOption
{
    public static function create(array $data): int
    {
        $sql = "INSERT INTO poll_options (poll_id, option_text, option_order) 
                VALUES (:poll_id, :option_text, :option_order)";
        
        Database::execute($sql, [
            'poll_id' => $data['poll_id'],
            'option_text' => $data['option_text'],
            'option_order' => $data['option_order'] ?? 0,
        ]);
        
        return (int) Database::lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $allowed = ['option_text', 'option_order'];
        $updates = [];

        foreach ($allowed as $field) {
            if (isset($data[$field])) {
                $updates[] = "$field = :$field";
            }
        }

        if (empty($updates)) {
            return false;
        }

        $sql = "UPDATE poll_options SET " . implode(', ', $updates) . " WHERE id = :id";
        $data['id'] = $id;
        
        return Database::execute($sql, $data) > 0;
    }

    public static function delete(int $id): bool
    {
        return Database::execute("DELETE FROM poll_options WHERE id = :id", ['id' => $id]) > 0;
    }
}

