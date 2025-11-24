<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Services\RecommendationService;
use Symfony\Component\HttpFoundation\Request;

class RecommendationController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $recommendations = RecommendationService::getForUser($user['id'], 20);
        $trending = RecommendationService::getTrending(10);
        $popular = RecommendationService::getPopular(10);

        return ResponseHelper::view('recommendations/index', [
            'user' => $user,
            'recommendations' => $recommendations,
            'trending' => $trending,
            'popular' => $popular,
            'pageTitle' => 'Recommendations',
        ]);
    }
}

