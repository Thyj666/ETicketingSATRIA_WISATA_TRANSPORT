<?php

declare(strict_types=1);

namespace WebApi;

use Base\Auth\Auth;
use Client\Master\User\UserService;

class AuthController
{
    public function __construct(private UserService $userService) {}

    public function loginPage(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
            return;
        }
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        require BASE_PATH . '/08Bsui/auth/login.php';
    }

    public function registerPage(): void
    {
        if (Auth::check()) {
            $this->redirect('/dashboard');
            return;
        }
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        require BASE_PATH . '/08Bsui/auth/register.php';
    }

    public function register(): void
    {
        $nama     = trim($_POST['nama'] ?? '');
        $username = trim($_POST['username'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $noTelp   = trim($_POST['no_telp'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (!$nama || !$username || !$password) {
            $_SESSION['flash_error'] = 'Nama, username, dan password wajib diisi.';
            $this->redirect('/register');
            return;
        }

        if (strlen($password) < 6) {
            $_SESSION['flash_error'] = 'Password minimal 6 karakter.';
            $this->redirect('/register');
            return;
        }

        if ($password !== $confirm) {
            $_SESSION['flash_error'] = 'Password dan konfirmasi password tidak cocok.';
            $this->redirect('/register');
            return;
        }

        // Check existing username
        $existing = $this->userService->getByUsername($username);
        if ($existing) {
            $_SESSION['flash_error'] = 'Username sudah digunakan, pilih username lain.';
            $this->redirect('/register');
            return;
        }

        // Create new pelanggan user
        try {
            $db = \Infrastructure\AppDbContext::getInstance();
            $hash = password_hash($password, PASSWORD_BCRYPT);
            $now  = date('Y-m-d H:i:s');

            $db->execute(
                "INSERT INTO users (nama, username, password, email, no_telp, role, is_active, created_at, updated_at)
                 VALUES (?, ?, ?, ?, ?, 'pelanggan', 1, ?, ?)",
                [$nama, $username, $hash, $email ?: null, $noTelp ?: null, $now, $now]
            );
            $userId = $db->lastInsertId();

            $_SESSION['flash_success'] = 'Registrasi berhasil! Silakan login.';
            $this->redirect('/login');
        } catch (\Throwable $e) {
            $_SESSION['flash_error'] = 'Registrasi gagal: ' . $e->getMessage();
            $this->redirect('/register');
        }
    }

    public function login(): void
    {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$username || !$password) {
            $_SESSION['flash_error'] = 'Username dan password wajib diisi.';
            $this->redirect('/login');
            return;
        }

        $user = $this->userService->getByUsername($username);

        if (!$user || !password_verify($password, $user->getPassword()) || !$user->getIsActive()) {
            $_SESSION['flash_error'] = 'Username atau password salah.';
            $this->redirect('/login');
            return;
        }

        Auth::issue([
            'id'           => $user->getId(),
            'nama'         => $user->getNama(),
            'username'     => $user->getUsername(),
            'role'         => $user->getRole(),
        ]);

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Auth::clearToken();
        session_destroy();
        $this->redirect('/login');
    }

    private function redirect(string $path): void
    {
        $base = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        header('Location: ' . rtrim($base, '/') . $path);
        exit;
    }
}
