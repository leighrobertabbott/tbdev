<?php

namespace App\Services;

use App\Core\Database;

class SettingsService
{
    /**
     * Get a setting value
     */
    public static function get(string $key, $default = null)
    {
        $result = Database::fetchOne(
            "SELECT value_s, value_u, value_i FROM avps WHERE arg = :key",
            ['key' => $key]
        );

        if (!$result) {
            return $default;
        }

        // Return the first non-null value
        if ($result['value_s'] !== null) {
            return $result['value_s'];
        }
        if ($result['value_u'] !== null) {
            return $result['value_u'];
        }
        if ($result['value_i'] !== null) {
            return $result['value_i'];
        }

        return $default;
    }

    /**
     * Set a setting value
     */
    public static function set(string $key, $value): bool
    {
        // Determine which column to use based on value type
        $type = 'value_s'; // default to string
        if (is_int($value)) {
            $type = 'value_i';
        } elseif (is_numeric($value) && (string)(int)$value === (string)$value) {
            $type = 'value_i';
            $value = (int)$value;
        } else {
            $type = 'value_s';
            $value = (string)$value;
        }

        // Check if setting exists
        $existing = Database::fetchOne(
            "SELECT arg FROM avps WHERE arg = :key",
            ['key' => $key]
        );

        if ($existing) {
            // Update existing
            $sql = "UPDATE avps SET {$type} = :value, value_s = NULL, value_u = NULL, value_i = NULL WHERE arg = :key";
            Database::execute($sql, ['key' => $key, 'value' => $value]);
        } else {
            // Insert new
            $sql = "INSERT INTO avps (arg, {$type}) VALUES (:key, :value)";
            Database::execute($sql, ['key' => $key, 'value' => $value]);
        }

        return true;
    }

    /**
     * Get all site settings
     */
    public static function getAll(): array
    {
        $settings = Database::fetchAll(
            "SELECT arg, value_s, value_u, value_i FROM avps WHERE arg LIKE 'site_%' OR arg LIKE 'theme_%' OR arg LIKE 'social_%' OR arg LIKE 'meta_%' ORDER BY arg"
        );

        $result = [];
        foreach ($settings as $setting) {
            $value = $setting['value_s'] ?? $setting['value_u'] ?? $setting['value_i'] ?? null;
            $result[$setting['arg']] = $value;
        }

        return $result;
    }

    /**
     * Set multiple settings at once
     */
    public static function setMultiple(array $settings): bool
    {
        foreach ($settings as $key => $value) {
            self::set($key, $value);
        }
        return true;
    }

    /**
     * Get default settings
     */
    public static function getDefaults(): array
    {
        return [
            'site_name' => 'TorrentBits',
            'site_tagline' => 'Modern BitTorrent Tracker',
            'site_description' => 'A modern BitTorrent tracker with vintage charm',
            'site_keywords' => 'torrent, bittorrent, tracker, filesharing',
            'site_logo_url' => '',
            'site_favicon_url' => '',
            'site_footer_text' => '© 2025 TorrentBits. All rights reserved.',
            'theme_primary_color' => '#8b2635',
            'theme_secondary_color' => '#1a2332',
            'theme_accent_color' => '#d4af37',
            'social_facebook' => '',
            'social_twitter' => '',
            'social_discord' => '',
            'social_telegram' => '',
            'social_reddit' => '',
            'meta_og_image' => '',
            'meta_twitter_card' => 'summary_large_image',
            'site_email' => '',
            'site_contact_email' => '',
            'site_maintenance_mode' => '0',
            'site_maintenance_message' => 'Site is currently under maintenance. Please check back soon.',
        ];
    }
}

