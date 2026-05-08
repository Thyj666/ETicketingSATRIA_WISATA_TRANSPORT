<?php

declare(strict_types=1);

namespace Base\Auth;

/**
 * Auth facade — diinisialisasi sekali di bootstrap (index.php),
 * lalu dipakai di seluruh controller dan view.
 *
 * Contoh pemakaian di controller:
 *   Auth::check()           → bool (sudah login atau belum)
 *   Auth::user()            → ?array (seluruh payload JWT)
 *   Auth::id()              → ?int
 *   Auth::getName()          → ?string
 *   Auth::getRole()            → ?string
 *   Auth::requireAuth()     → redirect ke /login jika belum login
 *   Auth::requireRole([])   → 403 jika role tidak sesuai
 */
class Auth
{
    private static ?array $user = null;
    private static string $secret = '';

    /**
     * Inisialisasi: baca & validasi JWT dari cookie.
     * Dipanggil satu kali di index.php setelah JWT_SECRET didefinisikan.
     */
    public static function init(string $secret): void
    {
        self::$secret = $secret;
        $token = $_COOKIE['jwt_token'] ?? '';
        if ($token !== '') {
            self::$user = JwtHelper::decode($token, $secret);
        }
    }

    /** Kembalikan seluruh payload JWT (atau null jika belum login) */
    public static function user(): ?array
    {
        return self::$user;
    }

    /** Apakah user sudah terautentikasi? */
    public static function check(): bool
    {
        return self::$user !== null;
    }

    /** ID user dari payload JWT */
    public static function id(): ?int
    {
        return isset(self::$user['id']) ? (int)self::$user['id'] : null;
    }

    public static function getName(): ?string
    {
        return self::$user['nama'] ?? null;
    }

    /** Role user dari payload JWT */
    public static function getRole(): ?string
    {
        return self::$user['role'] ?? null;
    }

    /**
     * Set JWT cookie setelah login berhasil.
     *
     * @param array $payload        Data user yang akan disimpan di token
     * @param int   $expireSeconds  Durasi token (default 8 jam)
     */
    public static function issue(array $payload, int $expireSeconds = 28800): void
    {
        $token = JwtHelper::encode($payload, self::$secret, $expireSeconds);
        setcookie('jwt_token', $token, [
            'expires'  => time() + $expireSeconds,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Strict',
        ]);
        // Populate in-memory user agar langsung bisa dipakai di request ini
        self::$user = JwtHelper::decode($token, self::$secret);
    }

    /** Hapus JWT cookie saat logout */
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
     * Guard: redirect ke /login jika belum terautentikasi.
     * Dipanggil di awal action controller yang butuh login.
     */
    public static function requireAuth(): void
    {
        if (!self::check()) {
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/');
            header('Location: ' . $base . '/login');
            exit;
        }
    }

    /**
     * Guard: tampilkan 403 jika role tidak termasuk dalam $allowedRoles.
     * Secara implisit juga memanggil requireAuth().
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
