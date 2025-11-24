<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Models\Category;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class CategoryAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $categories = Category::all();

        return ResponseHelper::view('admin/categories/index', [
            'user' => $user,
            'categories' => $categories,
            'pageTitle' => 'Manage Categories',
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        if ($request->getMethod() === 'POST') {
            $name = Security::sanitizeInput($request->request->get('name', ''));
            $image = Security::sanitizeInput($request
                ->request
                ->get('image', ''));
            $description = Security::sanitizeInput($request
                ->request
                ->get('description', ''));

            if (empty($name)) {
                return ResponseHelper::view('admin/categories/create', [
                    'user' => $user,
                    'error' => 'Name is required.',
                    'pageTitle' => 'Create Category',
                ]);
            }

            try {
                Database::execute(
                    "INSERT INTO categories (name, image, cat_desc) VALUES (:name, :image, :description)",
                    ['name' => $name, 'image' => $image, 'description' => $description]
                );

                return ResponseHelper::redirect('/admin/categories');
            } catch (\Exception $e) {
                return ResponseHelper::view('admin/categories/create', [
                    'user' => $user,
                    'error' => 'Failed to create category: ' . $e->getMessage(),
                    'pageTitle' => 'Create Category',
                ]);
            }
        }

        return ResponseHelper::view('admin/categories/create', [
            'user' => $user,
            'pageTitle' => 'Create Category',
        ]);
    }

    public function delete(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        // Check if category is in use
        $inUse = Database::fetchOne(
            "SELECT COUNT(*) as count FROM torrents WHERE category = :id",
            ['id' => $id]
        );

        if (($inUse['count'] ?? 0) > 0) {
            return ResponseHelper::view('admin/categories/index', [
                'user' => $user,
                'categories' => Category::all(),
                'error' => 'Cannot delete category that is in use.',
                'pageTitle' => 'Manage Categories',
            ]);
        }

        Database::execute("DELETE FROM categories WHERE id = :id", ['id' => $id]);
        return ResponseHelper::redirect('/admin/categories');
    }
}

