<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\ResponseHelper;
use App\Core\Security;
use App\Services\SettingsService;
use Symfony\Component\HttpFoundation\Request;

class SettingsAdminController
{
    public function index(Request $request)
    {
        $user = Auth::user();
        if (!$user || ($user['class'] ?? 0) < 5) {
            return ResponseHelper::view('errors/403', ['pageTitle' => 'Access Denied'], 403);
        }

        // Get current settings
        $settings = SettingsService::getAll();
        $defaults = SettingsService::getDefaults();

        // Merge with defaults to show all available settings
        $allSettings = array_merge($defaults, $settings);

        if ($request->getMethod() === 'POST') {
            // Handle form submission
            $newSettings = [];

            // Site Information
            $newSettings['site_name'] = Security::sanitizeInput($request->request->get('site_name', ''));
            $newSettings['site_tagline'] = Security::sanitizeInput($request->request->get('site_tagline', ''));
            $newSettings['site_description'] = Security::sanitizeInput($request->request->get('site_description', ''));
            $newSettings['site_keywords'] = Security::sanitizeInput($request->request->get('site_keywords', ''));
            $newSettings['site_logo_url'] = Security::sanitizeInput($request->request->get('site_logo_url', ''));
            $newSettings['site_favicon_url'] = Security::sanitizeInput($request->request->get('site_favicon_url', ''));
            $newSettings['site_footer_text'] = Security::sanitizeInput($request->request->get('site_footer_text', ''));

            // Theme Colors
            $newSettings['theme_primary_color'] = Security::sanitizeInput($request->request->get('theme_primary_color', ''));
            $newSettings['theme_secondary_color'] = Security::sanitizeInput($request->request->get('theme_secondary_color', ''));
            $newSettings['theme_accent_color'] = Security::sanitizeInput($request->request->get('theme_accent_color', ''));

            // Social Media
            $newSettings['social_facebook'] = Security::sanitizeInput($request->request->get('social_facebook', ''));
            $newSettings['social_twitter'] = Security::sanitizeInput($request->request->get('social_twitter', ''));
            $newSettings['social_discord'] = Security::sanitizeInput($request->request->get('social_discord', ''));
            $newSettings['social_telegram'] = Security::sanitizeInput($request->request->get('social_telegram', ''));
            $newSettings['social_reddit'] = Security::sanitizeInput($request->request->get('social_reddit', ''));

            // Meta Tags
            $newSettings['meta_og_image'] = Security::sanitizeInput($request->request->get('meta_og_image', ''));
            $newSettings['meta_twitter_card'] = Security::sanitizeInput($request->request->get('meta_twitter_card', 'summary_large_image'));

            // Contact Information
            $newSettings['site_email'] = Security::sanitizeInput($request->request->get('site_email', ''));
            $newSettings['site_contact_email'] = Security::sanitizeInput($request->request->get('site_contact_email', ''));

            // Maintenance
            $newSettings['site_maintenance_mode'] = $request->request->get('site_maintenance_mode', '0') ? '1' : '0';
            $newSettings['site_maintenance_message'] = Security::sanitizeInput($request->request->get('site_maintenance_message', ''));

            // Save all settings
            SettingsService::setMultiple($newSettings);

            return ResponseHelper::redirect('/admin/settings?success=1');
        }

        return ResponseHelper::view('admin/settings/index', [
            'user' => $user,
            'settings' => $allSettings,
            'pageTitle' => 'Site Settings',
        ]);
    }
}

