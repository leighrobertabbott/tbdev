<?php

namespace App\Controllers\Web;

use App\Core\ResponseHelper;
use App\Services\InstallerService;
use Symfony\Component\HttpFoundation\Request;

class InstallerController
{
    public function index(Request $request)
    {
        // Check if already installed
        if (InstallerService::isInstalled()) {
            return ResponseHelper::view('installer/already-installed', [
                'pageTitle' => 'Already Installed',
            ]);
        }

        return ResponseHelper::view('installer/index', [
            'pageTitle' => 'Installation Wizard',
        ]);
    }

    public function step1(Request $request)
    {
        if (InstallerService::isInstalled()) {
            return ResponseHelper::redirect('/installer');
        }

        $requirements = InstallerService::checkRequirements();
        $directories = InstallerService::createDirectories();

        return ResponseHelper::view('installer/step1-requirements', [
            'requirements' => $requirements['requirements'],
            'allMet' => $requirements['all_met'],
            'directories' => $directories,
            'pageTitle' => 'Step 1: System Requirements',
        ]);
    }

    public function step2(Request $request)
    {
        if (InstallerService::isInstalled()) {
            return ResponseHelper::redirect('/installer');
        }

        if ($request->getMethod() === 'POST') {
            $config = [
                'host' => $request->request->get('db_host', 'localhost'),
                'port' => (int) $request->request->get('db_port', 3306),
                'user' => $request->request->get('db_user', ''),
                'pass' => $request->request->get('db_pass', ''),
                'name' => $request->request->get('db_name', ''),
            ];

            // Test connection
            $test = InstallerService::testConnection($config);
            
            if (!$test['success']) {
                return ResponseHelper::view('installer/step2-database', [
                    'error' => $test['message'],
                    'config' => $config,
                    'pageTitle' => 'Step 2: Database Configuration',
                ]);
            }

            // Create database
            $create = InstallerService::createDatabase($config);
            if (!$create['success']) {
                return ResponseHelper::view('installer/step2-database', [
                    'error' => $create['message'],
                    'config' => $config,
                    'pageTitle' => 'Step 2: Database Configuration',
                ]);
            }

            // Import schema
            $import = InstallerService::importSchema($config);
            if (!$import['success']) {
                return ResponseHelper::view('installer/step2-database', [
                    'error' => $import['message'],
                    'config' => $config,
                    'pageTitle' => 'Step 2: Database Configuration',
                ]);
            }

            // Store config in session for next step
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['installer_db_config'] = $config;

            return ResponseHelper::redirect('/installer/step3');
        }

        return ResponseHelper::view('installer/step2-database', [
            'pageTitle' => 'Step 2: Database Configuration',
        ]);
    }

    public function step3(Request $request)
    {
        if (InstallerService::isInstalled()) {
            return ResponseHelper::redirect('/installer');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['installer_db_config'])) {
            return ResponseHelper::redirect('/installer/step2');
        }

        if ($request->getMethod() === 'POST') {
            $appConfig = [
                'db_host' => $_SESSION['installer_db_config']['host'],
                'db_port' => $_SESSION['installer_db_config']['port'],
                'db_user' => $_SESSION['installer_db_config']['user'],
                'db_pass' => $_SESSION['installer_db_config']['pass'],
                'db_name' => $_SESSION['installer_db_config']['name'],
                'app_env' => $request->request->get('app_env', 'production'),
                'app_debug' => $request->request->get('app_debug', 'false'),
                'app_url' => $request
                    ->request
                    ->get('app_url', 'http://localhost'),
                'app_name' => $request->request->get('app_name', 'TorrentBits'),
                'jwt_secret' => bin2hex(random_bytes(32)),
                'mail_host' => $request
                    ->request
                    ->get('mail_host', 'smtp.example.com'),
                'mail_port' => $request->request->get('mail_port', '587'),
                'mail_user' => $request->request->get('mail_user', ''),
                'mail_pass' => $request->request->get('mail_pass', ''),
                'mail_encryption' => $request
                    ->request
                    ->get('mail_encryption', 'tls'),
                'mail_from_address' => $request
                    ->request
                    ->get('mail_from_address', 'noreply@example.com'),
                'mail_from_name' => $request
                    ->request
                    ->get('mail_from_name', 'TorrentBits'),
            ];

            $result = InstallerService::createEnvFile($appConfig);
            
            if (!$result['success']) {
                return ResponseHelper::view('installer/step3-config', [
                    'error' => $result['message'],
                    'config' => $appConfig,
                    'pageTitle' => 'Step 3: Application Configuration',
                ]);
            }

            $_SESSION['installer_app_config'] = $appConfig;
            return ResponseHelper::redirect('/installer/step4');
        }

        return ResponseHelper::view('installer/step3-config', [
            'pageTitle' => 'Step 3: Application Configuration',
        ]);
    }

    public function step4(Request $request)
    {
        if (InstallerService::isInstalled()) {
            return ResponseHelper::redirect('/installer');
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['installer_app_config'])) {
            return ResponseHelper::redirect('/installer/step3');
        }

        if ($request->getMethod() === 'POST') {
            $userData = [
                'username' => $request->request->get('username', ''),
                'email' => $request->request->get('email', ''),
                'password' => $request->request->get('password', ''),
            ];

            if (empty($userData['username']) || empty($userData['email']) || empty($userData['password'])) {
                return ResponseHelper::view('installer/step4-admin', [
                    'error' => 'All fields are required.',
                    'pageTitle' => 'Step 4: Create Admin Account',
                ]);
            }

            if ($userData['password'] !== $request
                ->request
                ->get('password_confirm', '')) {
                return ResponseHelper::view('installer/step4-admin', [
                    'error' => 'Passwords do not match.',
                    'pageTitle' => 'Step 4: Create Admin Account',
                ]);
            }

            $result = InstallerService::createAdmin($userData);
            
            if (!$result['success']) {
                return ResponseHelper::view('installer/step4-admin', [
                    'error' => $result['message'],
                    'pageTitle' => 'Step 4: Create Admin Account',
                ]);
            }

            // Create lock file
            InstallerService::createLockFile();

            // Clear session
            unset($_SESSION['installer_db_config']);
            unset($_SESSION['installer_app_config']);

            return ResponseHelper::redirect('/installer/complete');
        }

        return ResponseHelper::view('installer/step4-admin', [
            'pageTitle' => 'Step 4: Create Admin Account',
        ]);
    }

    public function complete(Request $request)
    {
        if (!InstallerService::isInstalled()) {
            return ResponseHelper::redirect('/installer');
        }

        return ResponseHelper::view('installer/complete', [
            'pageTitle' => 'Installation Complete',
        ]);
    }
}

