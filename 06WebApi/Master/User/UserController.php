<?php

declare(strict_types=1);

namespace WebApi\Master\User;

use Base\Auth\Auth;
use Application\Master\User\Commands\Create\CreateUserCommand;
use Application\Master\User\Commands\Update\UpdateUserCommand;
use Application\Master\User\Commands\Delete\DeleteUserCommand;
use Application\Master\User\Queries\GetUserByIdQuery;
use Application\Master\User\Queries\GetUserByListQuery;
use Application\Master\Jabatan\Queries\GetJabatanByListQuery;
use Shared\Master\User\Commands\Create\CreateUserRequest;
use Shared\Master\User\Commands\Update\UpdateUserRequest;
use Shared\Master\User\Commands\Delete\DeleteUserRequest;
use Shared\Master\User\Queries\GetById\GetUserByIdRequest;
use Shared\Master\User\Queries\GetByList\GetUserByListRequest;
use Shared\Master\Jabatan\Queries\GetByList\GetJabatanByListRequest;

class UserController
{
    public function __construct(
        private CreateUserCommand    $createCmd,
        private UpdateUserCommand    $updateCmd,
        private DeleteUserCommand    $deleteCmd,
        private GetUserByIdQuery     $getById,
        private GetUserByListQuery   $getList,
        private GetJabatanByListQuery $getJabatanList,
    ) {}

    public function index(): void
    {
        Auth::requireRole(['admin_tu']);
        $search  = trim($_GET['search'] ?? '');
        $role    = $_GET['role'] ?? '';
        $list    = $this->getList->execute(new GetUserByListRequest($search, $role))->data;
        $jabatan = $this->getJabatanList->execute(new GetJabatanByListRequest())->data;
        $flash   = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/master/user/index.php';
    }

    public function create(): void
    {
        Auth::requireRole(['admin_tu']);
        $req = new CreateUserRequest(
            nama: trim($_POST['nama'] ?? ''),
            username: trim($_POST['username'] ?? ''),
            password: $_POST['password'] ?? 'pegawai123',
            email: trim($_POST['email'] ?? ''),
            nip: trim($_POST['nip'] ?? ''),
            noTelp: trim($_POST['no_telp'] ?? ''),
            alamat: trim($_POST['alamat'] ?? ''),
            role: $_POST['role'] ?? 'guru',
            jabatanId: $_POST['jabatan_id'] ? (int)$_POST['jabatan_id'] : null,
            gajiPokok: (float)($_POST['gaji_pokok'] ?? 0),
            jenisKelamin: $_POST['jenis_kelamin'] ?? 'L',
            isActive: true,
            userId: Auth::id(),
        );
        $res = $this->createCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/user');
    }

    public function update(): void
    {
        Auth::requireRole(['admin_tu']);
        $req = new UpdateUserRequest(
            id: (int)($_POST['id'] ?? 0),
            nama: trim($_POST['nama'] ?? ''),
            email: trim($_POST['email'] ?? ''),
            nip: trim($_POST['nip'] ?? ''),
            noTelp: trim($_POST['no_telp'] ?? ''),
            alamat: trim($_POST['alamat'] ?? ''),
            role: $_POST['role'] ?? 'guru',
            jabatanId: $_POST['jabatan_id'] ? (int)$_POST['jabatan_id'] : null,
            gajiPokok: (float)($_POST['gaji_pokok'] ?? 0),
            jenisKelamin: $_POST['jenis_kelamin'] ?? 'L',
            isActive: isset($_POST['is_active']),
            password: $_POST['password_baru'] ?? '',
            userId: Auth::id(),
        );
        $res = $this->updateCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/user');
    }

    public function delete(): void
    {
        Auth::requireRole(['admin_tu']);
        $res = $this->deleteCmd->execute(new DeleteUserRequest((int)($_POST['id'] ?? 0), Auth::id()));
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/user');
    }

    public function getById(): void
    {
        $res = $this->getById->execute(new GetUserByIdRequest((int)($_GET['id'] ?? 0)));
        header('Content-Type: application/json');
        if (!$res->found) {
            echo json_encode(null);
            return;
        }
        $u = $res->data;
        echo json_encode([
            'id'           => $u->getId(),
            'nama'         => $u->getNama(),
            'username'     => $u->getUsername(),
            'email'        => $u->getEmail(),
            'nip'          => $u->getNip(),
            'no_telp'      => $u->getNoTelp(),
            'alamat'       => $u->getAlamat(),
            'role'         => $u->getRole(),
            'jabatan_id'   => $u->getJabatanId(),
            'gaji_pokok'   => $u->getGajiPokok(),
            'jenis_kelamin'=> $u->getJenisKelamin(),
            'is_active'    => $u->getIsActive(),
        ]);
    }

    private function redirect(string $p): void
    {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . $p);
        exit;
    }
}
