<?php

declare(strict_types=1);

namespace Base\Auth;

/**
 * Auth facade — diinisialisasi sekali di bootstrap (index.php),
 * lalu dipakai di seluruh controller dan view.
 */
class Auth
{
    private static ?array $user = null;
    private static string $secret = '';

    public static function init(string $secret): void
    {
        self::$secret = $secret;
        $token = $_COOKIE['jwt_token'] ?? '';
        if ($token !== '') {
            self::$user = JwtHelper::decode($token, $secret);
        }
    }

    public static function user(): ?array
    {
        return self::$user;
    }

    public static function check(): bool
    {
        return self::$user !== null;
    }

    public static function id(): ?int
    {
        return isset(self::$user['id']) ? (int)self::$user['id'] : null;
    }

    public static function getName(): ?string
    {
        return self::$user['nama'] ?? null;
    }

    public static function getRole(): ?string
    {
        return self::$user['role'] ?? null;
    }

    public static function issue(array $payload, int $expireSeconds = 28800): void
    {
        $token = JwtHelper::encode($payload, self::$secret, $expireSeconds);
        setcookie('jwt_token', $token, [
            'expires'  => time() + $expireSeconds,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        self::$user = JwtHelper::decode($token, self::$secret);
    }

    public static function clearToken(): void
    {
        setcookie('jwt_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        self::$user = null;
    }

    /**
     * BUG FIX: Gunakan fungsi url() global dari index.php agar base path konsisten.
     * Sebelumnya: dirname(SCRIPT_NAME) menghasilkan '/' di root → redirect ke '//login'.
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            header('Location: ' . url('/login'));
            exit;
        }
    }

    /**
     * Guard: tampilkan 403 jika role tidak termasuk dalam $allowedRoles.
     *
     * @param string[] $allowedRoles
     */
    public static function requireRole(array $allowedRoles): void
    {
        self::requireAuth();
        if (!in_array(self::getRole(), $allowedRoles, true)) {
            http_response_code(403);
            require BASE_PATH . '/08Bsui/errors/403.php';
            exit;
        }
    }
}
