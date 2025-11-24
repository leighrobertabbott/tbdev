<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Core\Database;
use App\Models\Forum;
use Symfony\Component\HttpFoundation\Request;

class ForumAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        // Get sections with forums
        $sections = Database::fetchAll("SELECT * FROM forum_sections ORDER BY sort_order ASC, id ASC");
        $forums = Database::fetchAll("SELECT f.*, fs.name as section_name, pf.name as parent_name 
                                      FROM forums f 
                                      LEFT JOIN forum_sections fs ON f.section_id = fs.id 
                                      LEFT JOIN forums pf ON f.parent_id = pf.id 
                                      ORDER BY f.section_id ASC, f.parent_id ASC, f.sort ASC");

        return ResponseHelper::view('admin/forums/index', [
            'user' => $user,
            'sections' => $sections,
            'forums' => $forums,
            'pageTitle' => 'Forum Management',
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        if ($request->getMethod() === 'POST') {
            $name = Security::sanitizeInput($request->request->get('name', ''));
            $description = Security::sanitizeInput($request->request->get('description', ''));
            $sectionId = (int) $request->request->get('section_id', 0);
            $parentId = (int) $request->request->get('parent_id', 0);
            $minClassRead = (int) $request->request->get('min_class_read', 0);
            $minClassWrite = (int) $request->request->get('min_class_write', 0);
            $minClassCreate = (int) $request->request->get('min_class_create', 0);
            $sort = (int) $request->request->get('sort', 0);

            if (empty($name)) {
                $sections = Database::fetchAll("SELECT * FROM forum_sections ORDER BY sort_order ASC");
                $forums = Database::fetchAll("SELECT * FROM forums WHERE parent_id IS NULL OR parent_id = 0 ORDER BY name ASC");
                return ResponseHelper::view('admin/forums/create', [
                    'user' => $user,
                    'sections' => $sections,
                    'forums' => $forums,
                    'error' => 'Forum name is required.',
                    'pageTitle' => 'Create Forum',
                ]);
            }

            if ($sectionId <= 0) {
                $sections = Database::fetchAll("SELECT * FROM forum_sections ORDER BY sort_order ASC");
                $forums = Database::fetchAll("SELECT * FROM forums WHERE parent_id IS NULL OR parent_id = 0 ORDER BY name ASC");
                return ResponseHelper::view('admin/forums/create', [
                    'user' => $user,
                    'sections' => $sections,
                    'forums' => $forums,
                    'error' => 'Section is required.',
                    'pageTitle' => 'Create Forum',
                ]);
            }

            // If parent_id is 0, set to NULL
            $parentId = $parentId > 0 ? $parentId : null;

            Database::execute(
                "INSERT INTO forums (name, description, section_id, parent_id, minclassread, minclasswrite, minclasscreate, sort, postcount, topiccount) 
                 VALUES (:name, :description, :section_id, :parent_id, :min_class_read, :min_class_write, :min_class_create, :sort, 0, 0)",
                [
                    'name' => $name,
                    'description' => $description,
                    'section_id' => $sectionId,
                    'parent_id' => $parentId,
                    'min_class_read' => $minClassRead,
                    'min_class_write' => $minClassWrite,
                    'min_class_create' => $minClassCreate,
                    'sort' => $sort,
                ]
            );

            return ResponseHelper::redirect('/admin/forums');
        }

        $sections = Database::fetchAll("SELECT * FROM forum_sections ORDER BY sort_order ASC");
        $forums = Database::fetchAll("SELECT * FROM forums WHERE parent_id IS NULL OR parent_id = 0 ORDER BY name ASC");

        return ResponseHelper::view('admin/forums/create', [
            'user' => $user,
            'sections' => $sections,
            'forums' => $forums,
            'pageTitle' => 'Create Forum',
        ]);
    }

    public function delete(Request $request, int $id)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 4) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        Database::execute("DELETE FROM forums WHERE id = :id", ['id' => $id]);
        return ResponseHelper::redirect('/admin/forums');
    }
}

