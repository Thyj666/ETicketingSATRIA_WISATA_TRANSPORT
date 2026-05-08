<?php
declare(strict_types=1);
namespace WebApi;

use Base\Auth\Auth;
use Client\Master\User\UserService;
use Client\Master\Admin\AdminService;
use Client\Master\Pelanggan\PelangganService;
use Client\Master\Pimpinan\PimpinanService;

class ProfileController
{
    public function __construct(
        private UserService      $userService,
        private AdminService     $adminService,
        private PelangganService $pelangganService,
        private PimpinanService  $pimpinanService,
    ) {}

    public function index(): void
    {
        Auth::requireAuth();
        $userId  = Auth::id();
        $role    = Auth::getRole();
        $user    = $this->userService->getById($userId);
        $profile = $this->getProfile($userId, $role);
        $flash   = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/profile/index.php';
    }

    public function update(): void
    {
        Auth::requireAuth();
        $userId   = Auth::id();
        $role     = Auth::getRole();
        $nama     = trim($_POST['nama'] ?? '');
        $email    = trim($_POST['email'] ?? '') ?: null;
        $noTelp   = trim($_POST['no_telp'] ?? '') ?: null;
        $alamat   = trim($_POST['alamat'] ?? '') ?: null;
        $password = $_POST['password'] ?? '';

        // Update user password if provided
        if ($password) {
            $user = $this->userService->getById($userId);
            if ($user) {
                $user->hashPassword($password);
                $this->userService->save($user);
            }
        }

        // Update role-specific profile
        $profile = $this->getProfile($userId, $role);
        if ($profile) {
            $profile->update($nama ?: $profile->getNama(), $email, $noTelp, $alamat, true, $userId);
            $this->saveProfile($profile, $role);
        } else {
            // Create profile if not exists
            $this->createProfile($userId, $role, $nama, $email, $noTelp, $alamat);
        }

        // Reissue JWT with updated name
        if ($nama) {
            $currentUser = Auth::user();
            $currentUser['nama'] = $nama;
            Auth::issue($currentUser);
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Profil berhasil diperbarui.'];
        $this->redirect('/profile');
    }

    private function getProfile(int $userId, ?string $role): ?object
    {
        return match($role) {
            'admin'     => $this->adminService->getByUserId($userId),
            'pelanggan' => $this->pelangganService->getByUserId($userId),
            'pimpinan'  => $this->pimpinanService->getByUserId($userId),
            default     => null,
        };
    }

    private function saveProfile(object $profile, ?string $role): void
    {
        match($role) {
            'admin'     => $this->adminService->save($profile),
            'pelanggan' => $this->pelangganService->save($profile),
            'pimpinan'  => $this->pimpinanService->save($profile),
            default     => null,
        };
    }

    private function createProfile(int $userId, ?string $role, string $nama, ?string $email, ?string $noTelp, ?string $alamat): void
    {
        switch ($role) {
            case 'admin':
                $e = \Domain\Entities\Master\Admin\AdminEntity::create($userId, $nama ?: 'Admin', $email, $noTelp, $alamat, $userId);
                $this->adminService->save($e);
                break;
            case 'pelanggan':
                $e = \Domain\Entities\Master\Pelanggan\PelangganEntity::create($userId, $nama ?: 'Pelanggan', $email, $noTelp, $alamat, $userId);
                $this->pelangganService->save($e);
                break;
            case 'pimpinan':
                $e = \Domain\Entities\Master\Pimpinan\PimpinanEntity::create($userId, $nama ?: 'Pimpinan', $email, $noTelp, $alamat, $userId);
                $this->pimpinanService->save($e);
                break;
        }
    }

    private function redirect(string $path): void
    {
        $base = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        header('Location: ' . rtrim($base, '/') . $path);
        exit;
    }
}
