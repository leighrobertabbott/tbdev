<?php

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Models\User;
use App\Services\TwoFactorService;
use App\Services\ActivityService;
use Symfony\Component\HttpFoundation\Request;

class AuthController
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            return ResponseHelper::redirect('/');
        }

        $returnto = $request->query->get('returnto');
        
        // Check for CSRF error from session
        $error = null;
        $csrfDebug = null;
        if (isset($_SESSION['csrf_error'])) {
            $error = $_SESSION['csrf_error'];
            unset($_SESSION['csrf_error']);
        }
        if (isset($_SESSION['csrf_debug'])) {
            $csrfDebug = $_SESSION['csrf_debug'];
            unset($_SESSION['csrf_debug']);
        }
        
        return ResponseHelper::view('auth/login', [
            'returnto' => $returnto,
            'pageTitle' => 'Login',
            'error' => $error,
        ]);
    }

    public function login(Request $request)
    {
        $username = Security::sanitizeInput($request->request->get('username', ''));
        $password = $request->request->get('password', '');
        $returnto = $request->request->get('returnto', '/');

        // Rate limiting
        $ip = Security::getClientIp();
        if (!Security::rateLimit('login_' . $ip, 5, 300)) {
            error_log('Rate limit exceeded');
            return ResponseHelper::view('auth/login', [
                'error' => 'Too many login attempts. Please try again later.',
                'returnto' => $returnto,
                'pageTitle' => 'Login',
            ]);
        }

        if (empty($username) || empty($password)) {
            error_log('Empty username or password');
            return ResponseHelper::view('auth/login', [
                'error' => 'Username and password are required.',
                'returnto' => $returnto,
                'pageTitle' => 'Login',
                'debug' => implode("\n", $debug),
            ]);
        }

        $user = User::findByUsername($username);
        
        if (!$user || !password_verify($password, $user['passhash'])) {
            return ResponseHelper::view('auth/login', [
                'error' => 'Invalid username or password.',
                'returnto' => $returnto,
                'pageTitle' => 'Login',
            ]);
        }

        if ($user['enabled'] !== 'yes' || $user['status'] !== 'confirmed') {
            return ResponseHelper::view('auth/login', [
                'error' => 'Your account is disabled or not confirmed.',
                'returnto' => $returnto,
                'pageTitle' => 'Login',
            ]);
        }

        // Check 2FA
        if (($user['two_factor_enabled'] ?? 'no') === 'yes') {
            $code = $request->request->get('two_factor_code', '');
            $backupCode = $request->request->get('backup_code', '');
            
            if (empty($code) && empty($backupCode)) {
                // Store user ID in session for 2FA verification
                $_SESSION['2fa_user_id'] = $user['id'];
                $_SESSION['2fa_returnto'] = $returnto;
                // Show 2FA form
                return ResponseHelper::view('auth/login-2fa', [
                    'user_id' => $user['id'],
                    'returnto' => $returnto,
                    'pageTitle' => 'Two-Factor Authentication',
                ]);
            }
            
            $secret = $user['two_factor_secret'] ?? '';
            $valid = false;
            
            if (!empty($code) && !empty($secret)) {
                $valid = TwoFactorService::verifyCode($secret, $code);
            } elseif (!empty($backupCode)) {
                $valid = TwoFactorService::verifyBackupCode($user['id'], $backupCode);
            }
            
            if (!$valid) {
                return ResponseHelper::view('auth/login-2fa', [
                    'user_id' => $user['id'],
                    'returnto' => $returnto,
                    'error' => 'Invalid verification code.',
                    'pageTitle' => 'Two-Factor Authentication',
                ]);
            }
        }

        // Generate token and set cookie
        $tokenData = Auth::generateToken($user);
        
        // Use secure=false for localhost/HTTP, secure=true for HTTPS in production
        $isSecure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
                    (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
        setcookie('auth_token', $tokenData['token'], time() + 86400 * 365, '/', '', $isSecure, true);

        // Update last access
        User::updateLastAccess($user['id'], $ip);

        // Log activity
        ActivityService::log($user['id'], 'user_login', 'auth');

        return ResponseHelper::redirect($returnto);
    }

    public function showSignup(Request $request)
    {
        if (Auth::check()) {
            return ResponseHelper::redirect('/');
        }

        // Check user limit
        $maxUsers = \App\Core\Config::get('site.max_users', 5000);
        if (User::count() >= $maxUsers) {
            return ResponseHelper::view('auth/signup', [
                'error' => "User limit reached ({$maxUsers}).",
                'pageTitle' => 'Sign Up',
            ]);
        }

        return ResponseHelper::view('auth/signup', [
            'pageTitle' => 'Sign Up',
        ]);
    }

    public function signup(Request $request)
    {
        $username = Security::sanitizeInput($request->request->get('wantusername', ''));
        $password = $request->request->get('wantpassword', '');
        $passAgain = $request->request->get('passagain', '');
        $email = Security::sanitizeInput($request->request->get('email', ''));
        $timezone = $request->request->get('user_timezone', '0');

        // Validation
        if (empty($username) || empty($password) || empty($email)) {
            return ResponseHelper::view('auth/signup', [
                'error' => 'All fields are required.',
                'pageTitle' => 'Sign Up',
            ]);
        }

        if (!Security::validateUsername($username)) {
            return ResponseHelper::view('auth/signup', [
                'error' => 'Username must be 3-20 alphanumeric characters, underscores, or hyphens.',
                'pageTitle' => 'Sign Up',
            ]);
        }

        if (!Security::validateEmail($email)) {
            return ResponseHelper::view('auth/signup', [
                'error' => 'Invalid email address.',
                'pageTitle' => 'Sign Up',
            ]);
        }

        if ($password !== $passAgain) {
            return ResponseHelper::view('auth/signup', [
                'error' => 'Passwords do not match.',
                'pageTitle' => 'Sign Up',
            ]);
        }

        $passwordErrors = Security::validatePassword($password);
        if (!empty($passwordErrors)) {
            return ResponseHelper::view('auth/signup', [
                'error' => implode(' ', $passwordErrors),
                'pageTitle' => 'Sign Up',
            ]);
        }

        // Check if username/email exists
        if (User::findByUsername($username)) {
            return ResponseHelper::view('auth/signup', [
                'error' => 'Username already taken.',
                'pageTitle' => 'Sign Up',
            ]);
        }

        if (User::findByEmail($email)) {
            return ResponseHelper::view('auth/signup', [
                'error' => 'Email already registered.',
                'pageTitle' => 'Sign Up',
            ]);
        }

        // Create user
        $userId = User::create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
        ]);

        // First user becomes sysop
        if ($userId === 1) {
            User::update($userId, ['class' => 6, 'status' => 'confirmed']);
        }

        return ResponseHelper::view('auth/signup-success', [
            'pageTitle' => 'Sign Up Successful',
        ]);
    }

    public function logout(Request $request)
    {
        setcookie('auth_token', '', time() - 3600, '/');
        return ResponseHelper::redirect('/');
    }
}


