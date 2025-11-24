<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class SavedSearchController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $searches = Database::fetchAll(
            "SELECT * FROM saved_searches WHERE user_id = :user_id ORDER BY created_at DESC",
            ['user_id' => $user['id']]
        );

        return ResponseHelper::view('savedsearches/index', [
            'user' => $user,
            'searches' => $searches,
            'pageTitle' => 'Saved Searches',
        ]);
    }

    public function save(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::json(['error' => 'Unauthorized'], 401);
        }

        if ($request->getMethod() !== 'POST') {
            return ResponseHelper::json(['error' => 'Method not allowed'], 405);
        }

        $name = Security::sanitizeInput($request->request->get('name', ''));
        $query = Security::sanitizeInput($request->request->get('query', ''));
        $filters = $request->request->get('filters', []);

        if (empty($name) || empty($query)) {
            return ResponseHelper::json(['error' => 'Name and query required'], 400);
        }

        Database::execute(
            "INSERT INTO saved_searches (user_id, name, query, filters, created_at)
             VALUES (:user_id, :name, :query, :filters, :time)",
            [
                'user_id' => $user['id'],
                'name' => $name,
                'query' => $query,
                'filters' => json_encode($filters),
                'time' => time(),
            ]
        );

        return ResponseHelper::json(['success' => true]);
    }

    public function delete(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::json(['error' => 'Unauthorized'], 401);
        }

        $search = Database::fetchOne(
            "SELECT * FROM saved_searches WHERE id = :id AND user_id = :user_id",
            ['id' => $id, 'user_id' => $user['id']]
        );

        if (!$search) {
            return ResponseHelper::json(['error' => 'Search not found'], 404);
        }

        Database::execute("DELETE FROM saved_searches WHERE id = :id", ['id' => $id]);

        return ResponseHelper::json(['success' => true]);
    }
}

