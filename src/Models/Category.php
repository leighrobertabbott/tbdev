<?php

namespace App\Models;

use App\Core\Database;

class Category
{
    public static function all(): array
    {
        return Database::fetchAll("SELECT * FROM categories ORDER BY name");
    }

    public static function findById(int $id): ?array
    {
        return Database::fetchOne("SELECT * FROM categories WHERE id = :id", ['id' => $id]);
    }
}


