<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Models\News;
use Symfony\Component\HttpFoundation\Request;

class NewsAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        $news = News::all(100);

        return ResponseHelper::view('admin/news/index', [
            'user' => $user,
            'news' => $news,
            'pageTitle' => 'Manage News',
        ]);
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        if ($request->getMethod() === 'POST') {
            $headline = Security::sanitizeInput($request
                ->request
                ->get('headline', ''));
            $body = Security::sanitizeInput($request->request->get('body', ''));

            if (empty($headline) || empty($body)) {
                return ResponseHelper::view('admin/news/create', [
                    'user' => $user,
                    'error' => 'Headline and body are required.',
                    'pageTitle' => 'Create News',
                ]);
            }

            News::create([
                'userid' => $user['id'],
                'headline' => $headline,
                'body' => $body,
            ]);

            return ResponseHelper::redirect('/admin/news');
        }

        return ResponseHelper::view('admin/news/create', [
            'user' => $user,
            'pageTitle' => 'Create News',
        ]);
    }
}

