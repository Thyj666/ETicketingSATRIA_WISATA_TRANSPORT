<?php

declare(strict_types=1);

namespace WebApi;

use Base\Auth\Auth;
use Application\Master\User\Commands\Update\UpdateUserCommand;
use Application\Master\User\Queries\GetUserByIdQuery;
use Shared\Master\User\Commands\Update\UpdateUserRequest;
use Shared\Master\User\Queries\GetById\GetUserByIdRequest;

class ProfileController
{
    public function __construct(
        private UpdateUserCommand $updateCommand,
        private GetUserByIdQuery  $getByIdQuery,
    ) {}

    public function index(): void
    {
        Auth::requireAuth();
        $id    = Auth::id();
        $res   = $this->getByIdQuery->execute(new GetUserByIdRequest($id));
        $user  = $res->data;
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/profile/index.php';
    }

    public function update(): void
    {
        Auth::requireAuth();
        $id  = Auth::id();
        $req = new UpdateUserRequest(
            id: $id,
            nama: trim($_POST['nama'] ?? ''),
            email: trim($_POST['email'] ?? ''),
            nip: trim($_POST['nip'] ?? ''),
            noTelp: trim($_POST['no_telp'] ?? ''),
            alamat: trim($_POST['alamat'] ?? ''),
            role: Auth::getRole(),
            jabatanId: null,
            gajiPokok: 0,
            jenisKelamin: $_POST['jenis_kelamin'] ?? 'L',
            isActive: true,
            password: $_POST['password_baru'] ?? '',
            userId: $id,
        );
        $res = $this->updateCommand->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];

        // Jika nama berubah, terbitkan ulang token JWT dengan nama baru
        if ($res->success) {
            $currentUser = Auth::user();
            $currentUser['nama'] = $req->nama;
            Auth::issue($currentUser);
        }

        $this->redirect('/profile');
    }

    private function redirect(string $path): void
    {
        $base = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        header('Location: ' . rtrim($base, '/') . $path);
        exit;
    }
}
