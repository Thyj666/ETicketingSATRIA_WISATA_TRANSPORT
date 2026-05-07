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
        // Sudah login → langsung ke dashboard
        if (Auth::check()) {
            $this->redirect('/dashboard');
            return;
        }
        $error = $_SESSION['flash_error'] ?? null;
        unset($_SESSION['flash_error']);
        require BASE_PATH . '/08Bsui/auth/login.php';
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

        // Terbitkan JWT dan simpan di HttpOnly cookie (8 jam)
        Auth::issue([
            'id'       => $user->getId(),
            'nama'     => $user->getNama(),
            'username' => $user->getUsername(),
            'role'     => $user->getRole(),
            'nama_jabatan'  => $user->getNamaJabatan(),
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
