<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Database;
use Symfony\Component\HttpFoundation\Request;

class ConfirmController
{
    public function confirm(Request $request)
    {
        $id = (int) $request->query->get('id', 0);
        $secret = $request->query->get('secret', '');

        if ($id <= 0 || empty($secret) || !preg_match('/^[a-f0-9]{32}$/i', $secret)) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Invalid Link'], 404);
        }

        $user = Database::fetchOne(
            "SELECT id, status, editsecret FROM users WHERE id = :id",
            ['id' => $id]
        );

        if (!$user) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'User Not Found'], 404);
        }

        if ($user['status'] !== 'pending') {
            return ResponseHelper::view('auth/confirm-success', [
                'user' => Auth::user(),
                'message' => 'Your account is already confirmed.',
                'pageTitle' => 'Account Confirmed',
            ]);
        }

        // Verify secret (in original, it was hashed)
        if ($user['editsecret'] !== $secret) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Invalid Confirmation Link'], 404);
        }

        // Activate account
        Database::execute(
            "UPDATE users SET status = 'confirmed', editsecret = '' WHERE id = :id",
            ['id' => $id]
        );

        return ResponseHelper::view('auth/confirm-success', [
            'user' => Auth::user(),
            'message' => 'Your account has been successfully confirmed! You can now log in.',
            'pageTitle' => 'Account Confirmed',
        ]);
    }

    public function confirmEmail(Request $request)
    {
        $id = (int) $request->query->get('uid', 0);
        $key = $request->query->get('key', '');
        $email = urldecode($request->query->get('email', ''));

        if ($id <= 0 || empty($key) || empty($email)) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Invalid Link'], 404);
        }

        if (!preg_match('/^[a-f0-9]{32}$/i', $key) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Invalid Link'], 404);
        }

        $user = Database::fetchOne(
            "SELECT id, email, editsecret FROM users WHERE id = :id AND email = :email",
            ['id' => $id, 'email' => $email]
        );

        if (!$user || $user['editsecret'] !== $key) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Invalid Confirmation Link'], 404);
        }

        // Confirm email
        Database::execute(
            "UPDATE users SET editsecret = '' WHERE id = :id",
            ['id' => $id]
        );

        return ResponseHelper::view('auth/confirm-success', [
            'user' => Auth::user(),
            'message' => 'Your email has been successfully confirmed!',
            'pageTitle' => 'Email Confirmed',
        ]);
    }
}

