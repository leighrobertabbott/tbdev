<?php

namespace App\Services;

use App\Core\Database;
use App\Core\Security;

class TwoFactorService
{
    /**
     * Generate a secret for 2FA
     */
    public static function generateSecret(): string
    {
        // Generate 16 random bytes and convert to base32
        $randomBytes = random_bytes(16);
        return self::base32Encode($randomBytes);
    }

    /**
     * Get QR code URL for Google Authenticator
     */
    public static function getQRCodeUrl(string $email, string $secret, string $issuer = 'TorrentBits'): string
    {
        $label = urlencode($email);
        $secret = urlencode($secret);
        $issuer = urlencode($issuer);
        
        return "otpauth://totp/{$issuer}:{$label}?secret={$secret}&issuer={$issuer}";
    }

    /**
     * Verify TOTP code
     */
    public static function verifyCode(string $secret, string $code, int $window = 1): bool
    {
        $time = floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $expectedCode = self::generateTOTP($secret, $time + $i);
            if (hash_equals($expectedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate TOTP code
     */
    private static function generateTOTP(string $secret, int $time): string
    {
        $secret = self::base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $time);
        $hash = hash_hmac('sha1', $time, $secret, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;

        return str_pad((string) $code, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Enable 2FA for a user
     */
    public static function enable(int $userId, string $secret, string $backupCode): bool
    {
        $hashedBackup = password_hash($backupCode, PASSWORD_BCRYPT);

        return Database::execute(
            "UPDATE users SET two_factor_secret = :secret, two_factor_backup = :backup, two_factor_enabled = 'yes' 
             WHERE id = :user_id",
            [
                'user_id' => $userId,
                'secret' => $secret,
                'backup' => $hashedBackup,
            ]
        ) > 0;
    }

    /**
     * Disable 2FA for a user
     */
    public static function disable(int $userId): bool
    {
        return Database::execute(
            "UPDATE users SET two_factor_secret = '', two_factor_backup = '', two_factor_enabled = 'no' 
             WHERE id = :user_id",
            ['user_id' => $userId]
        ) > 0;
    }

    /**
     * Generate backup codes
     */
    public static function generateBackupCodes(int $count = 10): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }
        return $codes;
    }

    /**
     * Verify backup code
     */
    public static function verifyBackupCode(int $userId, string $code): bool
    {
        $user = Database::fetchOne(
            "SELECT two_factor_backup FROM users WHERE id = :user_id",
            ['user_id' => $userId]
        );

        if (!$user || empty($user['two_factor_backup'])) {
            return false;
        }

        return password_verify($code, $user['two_factor_backup']);
    }

    /**
     * Base32 encode
     */
    private static function base32Encode(string $data): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $encoded = '';
        $bits = 0;
        $value = 0;

        for ($i = 0; $i < strlen($data); $i++) {
            $value = ($value << 8) | ord($data[$i]);
            $bits += 8;

            while ($bits >= 5) {
                $encoded .= $chars[($value >> ($bits - 5)) & 31];
                $bits -= 5;
            }
        }

        if ($bits > 0) {
            $encoded .= $chars[($value << (5 - $bits)) & 31];
        }

        return $encoded;
    }

    /**
     * Base32 decode
     */
    private static function base32Decode(string $data): string
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $data = strtoupper($data);
        $decoded = '';
        $bits = 0;
        $value = 0;

        for ($i = 0; $i < strlen($data); $i++) {
            $char = $data[$i];
            $pos = strpos($chars, $char);
            if ($pos === false) {
                continue;
            }

            $value = ($value << 5) | $pos;
            $bits += 5;

            if ($bits >= 8) {
                $decoded .= chr(($value >> ($bits - 8)) & 255);
                $bits -= 8;
            }
        }

        return $decoded;
    }
}

