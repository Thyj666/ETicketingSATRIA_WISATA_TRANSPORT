<?php

declare(strict_types=1);

namespace Base\Auth;

class JwtHelper
{
    private static function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private static function base64UrlDecode(string $data): string
    {
        $padded = $data . str_repeat('=', 4 - strlen($data) % 4);
        return base64_decode(strtr($padded, '-_', '+/'));
    }

    /**
     * Encode payload menjadi JWT token (HS256).
     *
     * @param array  $payload        Data yang akan disimpan di token
     * @param string $secret         Secret key untuk signing
     * @param int    $expireSeconds  Durasi token (default 8 jam)
     */
    public static function encode(array $payload, string $secret, int $expireSeconds = 28800): string
    {
        $header  = self::base64UrlEncode((string)json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload['iat'] = time();
        $payload['exp'] = time() + $expireSeconds;
        $body      = self::base64UrlEncode((string)json_encode($payload));
        $signature = self::base64UrlEncode(hash_hmac('sha256', "$header.$body", $secret, true));
        return "$header.$body.$signature";
    }

    /**
     * Decode dan validasi JWT token.
     * Mengembalikan payload array jika valid, null jika tidak valid / expired.
     *
     * @param string $token
     * @param string $secret
     * @return array<string,mixed>|null
     */
    public static function decode(string $token, string $secret): ?array
    {
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            return null;
        }

        [$header, $body, $signature] = $parts;

        // Verifikasi signature
        $expectedSig = self::base64UrlEncode(hash_hmac('sha256', "$header.$body", $secret, true));
        if (!hash_equals($expectedSig, $signature)) {
            return null;
        }

        // Decode payload
        $data = json_decode(self::base64UrlDecode($body), true);
        if (!is_array($data)) {
            return null;
        }

        // Cek expiry
        if (!isset($data['exp']) || $data['exp'] < time()) {
            return null;
        }

        return $data;
    }
}
