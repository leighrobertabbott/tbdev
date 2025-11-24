<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Models\Torrent;
use App\Models\Category;
use Symfony\Component\HttpFoundation\Request;

class BrowseController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login?returnto=' .
                urlencode($request->getRequestUri()));
        }

        $categories = Category::all();
        $selectedCategory = (int) $request->query->get('cat', 0);
        $includeDead = (int) $request->query->get('incldead', 0);
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $filters = [];
        if ($selectedCategory > 0) {
            $filters['category'] = $selectedCategory;
        }
        
        if ($includeDead === 1) {
            // Include dead torrents but not banned
            $filters['visible'] = 'yes';
            $filters['includeDead'] = true;
        } elseif ($includeDead === 2) {
            // Dead only - torrents with 0 seeders and 0 leechers
            $filters['visible'] = 'yes';
            $filters['deadOnly'] = true;
        } else {
            // Active only - must have at least 1 seeder or 1 leecher
            $filters['visible'] = 'yes';
            $filters['activeOnly'] = true;
        }

        $torrents = Torrent::findAll($filters, $perPage, $offset);
        $total = Torrent::count($filters);
        $totalPages = ceil($total / $perPage);

        return ResponseHelper::view('browse/index', [
            'user' => $user,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'includeDead' => $includeDead,
            'torrents' => $torrents,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pageTitle' => 'Browse Torrents',
        ]);
    }

    public function search(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login?returnto=' .
                urlencode($request->getRequestUri()));
        }

        $query = Security::sanitizeInput($request
            ->query
            ->get('q', $request
            ->query
            ->get('search', '')));
        $selectedCategory = (int) $request->query->get('cat', 0);
        $page = max(1, (int) $request->query->get('page', 1));
        $perPage = 50;
        $offset = ($page - 1) * $perPage;

        $filters = ['visible' => 'yes'];
        if ($selectedCategory > 0) {
            $filters['category'] = $selectedCategory;
        }

        $torrents = [];
        $total = 0;
        $totalPages = 0;

        if (!empty($query)) {
            $torrents = Torrent::search($query, $filters, $perPage, $offset);
            $total = Torrent::countSearch($query, $filters);
            $totalPages = ceil($total / $perPage);
        }

        $categories = Category::all();

        return ResponseHelper::view('browse/search', [
            'user' => $user,
            'categories' => $categories,
            'selectedCategory' => $selectedCategory,
            'query' => $query,
            'torrents' => $torrents,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'pageTitle' => 'Search Torrents',
        ]);
    }
}


