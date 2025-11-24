<?php

namespace App\Controllers\Api;

use App\Core\Auth;
use App\Models\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController
{
    public function show(Request $request, int $id): JsonResponse
    {
        Auth::requireAuth();

        $user = User::findById($id);
        
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Remove sensitive data
        unset($user['passhash']);
        
        return new JsonResponse(['data' => $user]);
    }
}


