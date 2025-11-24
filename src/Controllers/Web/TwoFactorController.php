<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Services\TwoFactorService;
use Symfony\Component\HttpFoundation\Request;

class TwoFactorController
{
    public function show(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        $enabled = ($user['two_factor_enabled'] ?? 'no') === 'yes';
        $secret = $user['two_factor_secret'] ?? '';

        $qrUrl = null;
        if (!$enabled && empty($secret)) {
            $secret = TwoFactorService::generateSecret();
            $qrUrl = TwoFactorService::getQRCodeUrl(
                $user['email'],
                $secret,
                \App\Core\Config::get('app.name', 'TorrentBits')
            );
        }

        return ResponseHelper::view('twofactor/show', [
            'user' => $user,
            'enabled' => $enabled,
            'secret' => $secret,
            'qrUrl' => $qrUrl,
            'pageTitle' => 'Two-Factor Authentication',
        ]);
    }

    public function enable(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        if ($request->getMethod() !== 'POST') {
            return ResponseHelper::redirect('/twofactor');
        }

        $code = Security::sanitizeInput($request->request->get('code', ''));
        $secret = Security::sanitizeInput($request->request->get('secret', ''));

        if (empty($code) || empty($secret)) {
            return ResponseHelper::view('twofactor/show', [
                'user' => $user,
                'error' => 'Code and secret are required',
                'secret' => $secret,
                'qrUrl' => TwoFactorService::getQRCodeUrl(
                    $user['email'],
                    $secret,
                    \App\Core\Config::get('app.name', 'TorrentBits')
                ),
                'pageTitle' => 'Two-Factor Authentication',
            ]);
        }

        if (!TwoFactorService::verifyCode($secret, $code)) {
            return ResponseHelper::view('twofactor/show', [
                'user' => $user,
                'error' => 'Invalid verification code',
                'secret' => $secret,
                'qrUrl' => TwoFactorService::getQRCodeUrl(
                    $user['email'],
                    $secret,
                    \App\Core\Config::get('app.name', 'TorrentBits')
                ),
                'pageTitle' => 'Two-Factor Authentication',
            ]);
        }

        $backupCodes = TwoFactorService::generateBackupCodes();
        $backupCode = implode('-', array_slice($backupCodes, 0, 1)); // Use first as master backup

        TwoFactorService::enable($user['id'], $secret, $backupCode);

        return ResponseHelper::view('twofactor/enabled', [
            'user' => $user,
            'backupCodes' => $backupCodes,
            'pageTitle' => '2FA Enabled',
        ]);
    }

    public function disable(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return ResponseHelper::redirect('/login');
        }

        if ($request->getMethod() !== 'POST') {
            return ResponseHelper::redirect('/twofactor');
        }

        $password = $request->request->get('password', '');
        
        // Verify password
        $dbUser = \App\Models\User::findById($user['id']);
        if (!password_verify($password, $dbUser['passhash'])) {
            return ResponseHelper::view('twofactor/show', [
                'user' => $user,
                'error' => 'Invalid password',
                'enabled' => true,
                'pageTitle' => 'Two-Factor Authentication',
            ]);
        }

        TwoFactorService::disable($user['id']);

        return ResponseHelper::redirect('/twofactor');
    }
}

