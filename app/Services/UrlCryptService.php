<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

class UrlCryptService
{
    /**
     * Encrypt an integer ID or string into a URL-safe encrypted token
     * using Laravel's encrypt/decrypt.
     */
    public static function encrypt($id): string
    {
        $encrypted = Crypt::encryptString((string) $id);

        // Replace Base64 characters that are reserved or problematic in URLs:
        // '+' -> '-', '/' -> '_', '=' -> '~'
        return strtr($encrypted, ['+' => '-', '/' => '_', '=' => '~']);
    }

    /**
     * Decrypt a URL-safe encrypted token back into an integer ID
     * using Laravel's encrypt/decrypt. Returns null if invalid or tampered.
     */
    public static function decrypt(?string $token): ?int
    {
        if (empty($token) || !is_string($token)) {
            return null;
        }

        try {
            // Restore standard Base64 characters
            $standardBase64 = strtr($token, ['-' => '+', '_' => '/', '~' => '=']);
            $decrypted = Crypt::decryptString($standardBase64);

            return is_numeric($decrypted) ? (int) $decrypted : null;
        } catch (DecryptException|\Throwable $e) {
            return null;
        }
    }
}
