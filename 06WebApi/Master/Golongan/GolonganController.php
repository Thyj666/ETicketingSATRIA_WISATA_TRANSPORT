<?php

declare(strict_types=1);

namespace WebApi\Master\Golongan;

use Base\Auth\Auth;
use Application\Master\Golongan\Commands\Create\CreateGolonganCommand;
use Application\Master\Golongan\Commands\Update\UpdateGolonganCommand;
use Application\Master\Golongan\Commands\Delete\DeleteGolonganCommand;
use Application\Master\Golongan\Queries\GetGolonganByIdQuery;
use Application\Master\Golongan\Queries\GetGolonganByListQuery;
use Shared\Master\Golongan\Commands\Create\CreateGolonganRequest;
use Shared\Master\Golongan\Commands\Update\UpdateGolonganRequest;
use Shared\Master\Golongan\Commands\Delete\DeleteGolonganRequest;
use Shared\Master\Golongan\Queries\GetById\GetGolonganByIdRequest;
use Shared\Master\Golongan\Queries\GetByList\GetGolonganByListRequest;

class GolonganController
{
    public function __construct(
        private CreateGolonganCommand  $createCmd,
        private UpdateGolonganCommand  $updateCmd,
        private DeleteGolonganCommand  $deleteCmd,
        private GetGolonganByIdQuery   $getById,
        private GetGolonganByListQuery $getList,
    ) {}

    public function index(): void
    {
        Auth::requireRole(['admin_tu']);
        $search = trim($_GET['search'] ?? '');
        $list   = $this->getList->execute(new GetGolonganByListRequest($search))->data;
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/master/golongan/index.php';
    }

    public function create(): void
    {
        Auth::requireRole(['admin_tu']);
        $req = new CreateGolonganRequest(
            kodeGolongan: trim($_POST['kode_golongan'] ?? ''),
            namaGolongan: trim($_POST['nama_golongan'] ?? ''),
            gajiPokok: (float)($_POST['gaji_pokok'] ?? 0),
            tunjangan: (float)($_POST['tunjangan'] ?? 0),
            userId: Auth::id(),
        );
        $res = $this->createCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/golongan');
    }

    public function update(): void
    {
        Auth::requireRole(['admin_tu']);
        $req = new UpdateGolonganRequest(
            id: (int)($_POST['id'] ?? 0),
            kodeGolongan: trim($_POST['kode_golongan'] ?? ''),
            namaGolongan: trim($_POST['nama_golongan'] ?? ''),
            gajiPokok: (float)($_POST['gaji_pokok'] ?? 0),
            tunjangan: (float)($_POST['tunjangan'] ?? 0),
            userId: Auth::id(),
        );
        $res = $this->updateCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/golongan');
    }

    public function delete(): void
    {
        Auth::requireRole(['admin_tu']);
        $res = $this->deleteCmd->execute(new DeleteGolonganRequest((int)($_POST['id'] ?? 0), Auth::id()));
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/master/golongan');
    }

    public function getById(): void
    {
        $res = $this->getById->execute(new GetGolonganByIdRequest((int)($_GET['id'] ?? 0)));
        header('Content-Type: application/json');
        echo json_encode($res->found ? [
            'id'            => $res->data->getId(),
            'kode_golongan' => $res->data->getKodeGolongan(),
            'nama_golongan' => $res->data->getNamaGolongan(),
            'gaji_pokok'    => $res->data->getGajiPokok(),
            'tunjangan'     => $res->data->getTunjangan(),
        ] : null);
    }

    private function redirect(string $p): void
    {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . $p);
        exit;
    }
}
