<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Core\Database;
use App\Core\Config;
use Symfony\Component\HttpFoundation\Request;

class RecoveryController
{
    public function show(Request $request)
    {
        return ResponseHelper::view('auth/recover', [
            'user' => Auth::user(),
            'pageTitle' => 'Password Recovery',
        ]);
    }

    public function recover(Request $request)
    {
        $email = Security::sanitizeInput($request->request->get('email', ''));

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ResponseHelper::view('auth/recover', [
                'user' => Auth::user(),
                'error' => 'Please enter a valid email address.',
                'pageTitle' => 'Password Recovery',
            ]);
        }

        $user = Database::fetchOne(
            "SELECT id, username, email FROM users WHERE email = :email",
            ['email' => $email]
        );

        if (!$user) {
            // Don't reveal if email exists for security
            return ResponseHelper::view('auth/recover-success', [
                'user' => Auth::user(),
                'pageTitle' => 'Recovery Email Sent',
            ]);
        }

        // Generate recovery token
        $token = bin2hex(random_bytes(32));
        $expires = time() + (3600 * 24); // 24 hours

        Database::execute(
            "UPDATE users SET editsecret = :token WHERE id = :id",
            ['token' => $token, 'id' => $user['id']]
        );

        // In production, send email here
        // For now, we'll show the recovery link (remove in production!)
        $recoveryUrl = Config::get('app.url') .
            "/recover/reset?token={$token}&email=" .
            urlencode($user['email']);

        return ResponseHelper::view('auth/recover-success', [
            'user' => Auth::user(),
            'recoveryUrl' => $recoveryUrl, // Remove in production!
            'pageTitle' => 'Recovery Email Sent',
        ]);
    }

    public function reset(Request $request)
    {
        $token = $request->query->get('token', '');
        $email = $request->query->get('email', '');

        if (empty($token) || empty($email)) {
            return ResponseHelper::view('errors/404', ['pageTitle' => 'Invalid Link'], 404);
        }

        $user = Database::fetchOne(
            "SELECT id, username FROM users WHERE email = :email AND editsecret = :token",
            ['email' => $email, 'token' => $token]
        );

        if (!$user) {
            return ResponseHelper::view('auth/recover', [
                'user' => Auth::user(),
                'error' => 'Invalid or expired recovery link.',
                'pageTitle' => 'Password Recovery',
            ]);
        }

        if ($request->getMethod() === 'POST') {
            $password = $request->request->get('password', '');
            $passwordConfirm = $request->request->get('password_confirm', '');

            if ($password !== $passwordConfirm) {
                return ResponseHelper::view('auth/reset-password', [
                    'user' => Auth::user(),
                    'token' => $token,
                    'email' => $email,
                    'error' => 'Passwords do not match.',
                    'pageTitle' => 'Reset Password',
                ]);
            }

            $passwordErrors = Security::validatePassword($password);
            if (!empty($passwordErrors)) {
                return ResponseHelper::view('auth/reset-password', [
                    'user' => Auth::user(),
                    'token' => $token,
                    'email' => $email,
                    'error' => implode(' ', $passwordErrors),
                    'pageTitle' => 'Reset Password',
                ]);
            }

            // Update password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            Database::execute(
                "UPDATE users SET passhash = :password, editsecret = '' WHERE id = :id",
                ['password' => $hashedPassword, 'id' => $user['id']]
            );

            return ResponseHelper::view('auth/reset-success', [
                'user' => Auth::user(),
                'pageTitle' => 'Password Reset',
            ]);
        }

        return ResponseHelper::view('auth/reset-password', [
            'user' => Auth::user(),
            'token' => $token,
            'email' => $email,
            'pageTitle' => 'Reset Password',
        ]);
    }
}

