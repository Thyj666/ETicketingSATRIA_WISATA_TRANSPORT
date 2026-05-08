<?php

declare(strict_types=1);

namespace WebApi\Master\Armada;

use Base\Auth\Auth;
use Application\Master\Armada\Commands\Create\CreateArmadaCommand;
use Application\Master\Armada\Commands\Update\UpdateArmadaCommand;
use Application\Master\Armada\Commands\Delete\DeleteArmadaCommand;
use Application\Master\Armada\Queries\GetArmadaByIdQuery;
use Application\Master\Armada\Queries\GetArmadaByListQuery;
use Shared\Master\Armada\Commands\Create\CreateArmadaRequest;
use Shared\Master\Armada\Commands\Update\UpdateArmadaRequest;
use Shared\Master\Armada\Commands\Delete\DeleteArmadaRequest;
use Shared\Master\Armada\Queries\GetById\GetArmadaByIdRequest;
use Shared\Master\Armada\Queries\GetByList\GetArmadaByListRequest;

class ArmadaController
{
    public function __construct(
        private CreateArmadaCommand  $createCmd,
        private UpdateArmadaCommand  $updateCmd,
        private DeleteArmadaCommand  $deleteCmd,
        private GetArmadaByIdQuery   $getById,
        private GetArmadaByListQuery $getList,
    ) {}

    public function index(): void
    {
        Auth::requireRole(['admin_tu']);
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        $list   = $this->getList->execute(new GetArmadaByListRequest($search, $status))->data;
        $flash  = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/master/armada/index.php';
    }

    public function create(): void
    {
        Auth::requireRole(['admin_tu']);
        $platNomor  = trim($_POST['plat_nomor'] ?? '');
        $namaArmada = trim($_POST['nama_armada'] ?? '');
        $tipeSeat   = trim($_POST['tipe_seat'] ?? '2-2');
        $jumlahSeat = (int)($_POST['jumlah_seat'] ?? 0);
        $status     = trim($_POST['status'] ?? 'tersedia');
        $actorId    = Auth::id();

        $req = new CreateArmadaRequest($platNomor, $namaArmada, $tipeSeat, $jumlahSeat, $status);
        $res = $this->createCmd->execute($req, $actorId);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        header('Location: ' . url('/master/armada'));
        exit;
    }

    public function update(): void
    {
        Auth::requireRole(['admin_tu']);
        $id         = (int)($_POST['id'] ?? 0);
        $platNomor  = trim($_POST['plat_nomor'] ?? '');
        $namaArmada = trim($_POST['nama_armada'] ?? '');
        $tipeSeat   = trim($_POST['tipe_seat'] ?? '2-2');
        $jumlahSeat = (int)($_POST['jumlah_seat'] ?? 0);
        $status     = trim($_POST['status'] ?? 'tersedia');
        $actorId    = Auth::id();

        $req = new UpdateArmadaRequest($id, $platNomor, $namaArmada, $tipeSeat, $jumlahSeat, $status);
        $res = $this->updateCmd->execute($req, $actorId);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        header('Location: ' . url('/master/armada'));
        exit;
    }

    public function delete(): void
    {
        Auth::requireRole(['admin_tu']);
        $id = (int)($_POST['id'] ?? 0);
        $actorId = Auth::id();
        $res = $this->deleteCmd->execute(new DeleteArmadaRequest($id), $actorId);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        header('Location: ' . url('/master/armada'));
        exit;
    }

    public function getById(): void
    {
        Auth::requireRole(['admin_tu']);
        $id  = (int)($_GET['id'] ?? 0);
        $res = $this->getById->execute(new GetArmadaByIdRequest($id));
        header('Content-Type: application/json');
        echo json_encode($res->data ? [
            'id' => $res->data->getId(),
            'plat_nomor' => $res->data->getPlatNomor(),
            'nama_armada' => $res->data->getNamaArmada(),
            'tipe_seat' => $res->data->getTipeSeat(),
            'jumlah_seat' => $res->data->getJumlahSeat(),
            'status' => $res->data->getStatus(),
        ] : null);
    }
}
