<?php

declare(strict_types=1);

namespace WebApi\Master\Jabatan;

use Base\Auth\Auth;
use Application\Master\Jabatan\Commands\Create\CreateJabatanCommand;
use Application\Master\Jabatan\Commands\Update\UpdateJabatanCommand;
use Application\Master\Jabatan\Commands\Delete\DeleteJabatanCommand;
use Application\Master\Jabatan\Queries\GetJabatanByIdQuery;
use Application\Master\Jabatan\Queries\GetJabatanByListQuery;
use Application\Master\Golongan\Queries\GetGolonganByListQuery;
use Shared\Master\Jabatan\Commands\Create\CreateJabatanRequest;
use Shared\Master\Jabatan\Commands\Update\UpdateJabatanRequest;
use Shared\Master\Jabatan\Commands\Delete\DeleteJabatanRequest;
use Shared\Master\Jabatan\Queries\GetById\GetJabatanByIdRequest;
use Shared\Master\Jabatan\Queries\GetByList\GetJabatanByListRequest;
use Shared\Master\Golongan\Queries\GetByList\GetGolonganByListRequest;

class JabatanController
{
    public function __construct(
        private CreateJabatanCommand  $createCmd,
        private UpdateJabatanCommand  $updateCmd,
        private DeleteJabatanCommand  $deleteCmd,
        private GetJabatanByIdQuery   $getById,
        private GetJabatanByListQuery $getList,
        private GetGolonganByListQuery $getGolonganList,
    ) {}

    public function index(): void
    {
        Auth::requireRole(['admin_tu']);
        $search       = trim($_GET['search'] ?? '');
        $jenis        = $_GET['jenis'] ?? '';
        $list         = $this->getList->execute(new GetJabatanByListRequest($search, $jenis))->data;
        $golonganList = $this->getGolonganList->execute(new GetGolonganByListRequest())->data;
        $flash        = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/master/jabatan/index.php';
    }

    public function create(): void
    {
        Auth::requireRole(['admin_tu']);
        $req = new CreateJabatanRequest(
            namaJabatan: trim($_POST['nama_jabatan'] ?? ''),
            jenis: $_POST['jenis'] ?? 'guru',
            golonganId: $_POST['golongan_id'] ? (int)$_POST['golongan_id'] : null,
            keterangan: trim($_POST['keterangan'] ?? ''),
            userId: Auth::id(),
        );
        $res = $this->createCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/jabatan');
    }

    public function update(): void
    {
        Auth::requireRole(['admin_tu']);
        $req = new UpdateJabatanRequest(
            id: (int)($_POST['id'] ?? 0),
            namaJabatan: trim($_POST['nama_jabatan'] ?? ''),
            jenis: $_POST['jenis'] ?? 'guru',
            golonganId: $_POST['golongan_id'] ? (int)$_POST['golongan_id'] : null,
            keterangan: trim($_POST['keterangan'] ?? ''),
            userId: Auth::id(),
        );
        $res = $this->updateCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/jabatan');
    }

    public function delete(): void
    {
        Auth::requireRole(['admin_tu']);
        $res = $this->deleteCmd->execute(new DeleteJabatanRequest((int)($_POST['id'] ?? 0), Auth::id()));
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/jabatan');
    }

    public function getById(): void
    {
        $res = $this->getById->execute(new GetJabatanByIdRequest((int)($_GET['id'] ?? 0)));
        header('Content-Type: application/json');
        echo json_encode($res->found ? [
            'id'          => $res->data->getId(),
            'nama_jabatan'=> $res->data->getNamaJabatan(),
            'jenis'       => $res->data->getJenis(),
            'golongan_id' => $res->data->getGolonganId(),
            'keterangan'  => $res->data->getKeterangan(),
        ] : null);
    }

    private function redirect(string $p): void
    {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . $p);
        exit;
    }
}
