<?php

declare(strict_types=1);

namespace WebApi\Transaction\Tiket;

use Base\Auth\Auth;
use Application\Transaction\Tiket\Commands\Create\CreateTiketCommand;
use Application\Transaction\Tiket\Commands\Update\UpdateTiketCommand;
use Application\Transaction\Tiket\Commands\Delete\DeleteTiketCommand;
use Application\Transaction\Tiket\Queries\GetTiketByIdQuery;
use Application\Transaction\Tiket\Queries\GetTiketByListQuery;
use Application\Master\Armada\Queries\GetArmadaByListQuery;
use Shared\Transaction\Tiket\Commands\Create\CreateTiketRequest;
use Shared\Transaction\Tiket\Commands\Update\UpdateTiketRequest;
use Shared\Transaction\Tiket\Commands\Delete\DeleteTiketRequest;
use Shared\Transaction\Tiket\Queries\GetById\GetTiketByIdRequest;
use Shared\Transaction\Tiket\Queries\GetByList\GetTiketByListRequest;
use Shared\Master\Armada\Queries\GetByList\GetArmadaByListRequest;
use Client\Transaction\Tiket\TiketService;

class TiketController
{
    public function __construct(
        private CreateTiketCommand   $createCmd,
        private UpdateTiketCommand   $updateCmd,
        private DeleteTiketCommand   $deleteCmd,
        private GetTiketByIdQuery    $getById,
        private GetTiketByListQuery  $getList,
        private GetArmadaByListQuery $getArmadaList,
        private TiketService         $tiketService,
    ) {}

    public function index(): void
    {
        // Semua user yang login bisa melihat daftar tiket
        Auth::requireAuth();
        $search  = $_GET['search']  ?? '';
        $tujuan  = $_GET['tujuan']  ?? '';
        $tanggal = $_GET['tanggal'] ?? '';
        $list    = $this->getList->execute(new GetTiketByListRequest(0, $search, $tujuan, $tanggal))->data;
        $armadas = $this->getArmadaList->execute(new GetArmadaByListRequest('', 'tersedia'))->data;
        $role    = Auth::user()['role'] ?? '';
        $flash   = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/transaction/tiket/index.php';
    }

    public function create(): void
    {
        // Hanya admin yang bisa membuat tiket
        Auth::requireRole(['admin']);
        $armadaId         = (int)($_POST['armada_id'] ?? 0);
        $tujuan           = trim($_POST['tujuan'] ?? '');
        $tanggalBerangkat = trim($_POST['tanggal_berangkat'] ?? '') ?: null;
        $jamBerangkat     = trim($_POST['jam_berangkat'] ?? '') ?: null;
        $harga            = (float)($_POST['harga'] ?? 0);
        $actorId          = Auth::id();

        $req = new CreateTiketRequest($armadaId, $tujuan, $tanggalBerangkat, $jamBerangkat, $harga);
        $res = $this->createCmd->execute($req, $actorId);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        header('Location: ' . url('/transaksi/tiket'));
        exit;
    }

    public function update(): void
    {
        // Hanya admin yang bisa mengubah tiket
        Auth::requireRole(['admin']);
        $id               = (int)($_POST['id'] ?? 0);
        $armadaId         = (int)($_POST['armada_id'] ?? 0);
        $tujuan           = trim($_POST['tujuan'] ?? '');
        $tanggalBerangkat = trim($_POST['tanggal_berangkat'] ?? '') ?: null;
        $jamBerangkat     = trim($_POST['jam_berangkat'] ?? '') ?: null;
        $harga            = (float)($_POST['harga'] ?? 0);
        $isFull           = isset($_POST['is_full']) && $_POST['is_full'] === '1';
        $actorId          = Auth::id();

        $req = new UpdateTiketRequest($id, $armadaId, $tujuan, $tanggalBerangkat, $jamBerangkat, $harga, $isFull);
        $res = $this->updateCmd->execute($req, $actorId);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        header('Location: ' . url('/transaksi/tiket'));
        exit;
    }

    public function delete(): void
    {
        // Hanya admin yang bisa menghapus tiket
        Auth::requireRole(['admin']);
        $id      = (int)($_POST['id'] ?? 0);
        $actorId = Auth::id();
        $res     = $this->deleteCmd->execute(new DeleteTiketRequest($id), $actorId);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        header('Location: ' . url('/transaksi/tiket'));
        exit;
    }

    public function getById(): void
    {
        Auth::requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = $this->getById->execute(new GetTiketByIdRequest($id));
        header('Content-Type: application/json');
        $d = $res->data;
        if (!$d) {
            echo json_encode(null);
            return;
        }
        echo json_encode([
            'id'                => $d->getId(),
            'armada_id'         => $d->getArmadaId(),
            'tujuan'            => $d->getTujuan(),
            'tanggal_berangkat' => $d->getTanggalBerangkat(),
            'jam_berangkat'     => $d->getJamBerangkat(),
            'harga'             => $d->getHarga(),
            'is_full'           => $d->getIsFull(),
            'nama_armada'       => $d->getArmada()?->getNamaArmada(),
            'jumlah_seat'       => $d->getArmada()?->getJumlahSeat(),
            'tipe_seat'         => $d->getArmada()?->getTipeSeat(),
        ]);
    }

    public function getSeats(): void
    {
        Auth::requireAuth();
        $tiketId    = (int)($_GET['tiket_id'] ?? 0);
        $takenSeats = $this->tiketService->getTakenSeats($tiketId);
        $tiket      = $this->tiketService->getById($tiketId);
        $jumlahSeat = $tiket?->getArmada()?->getJumlahSeat() ?? 0;
        $tipeSeat   = $tiket?->getArmada()?->getTipeSeat() ?? '2-2';

        header('Content-Type: application/json');
        echo json_encode([
            'taken'     => $takenSeats,
            'total'     => $jumlahSeat,
            'tipe_seat' => $tipeSeat,
        ]);
    }
}
