<?php

declare(strict_types=1);

namespace WebApi\Transaction\Absensi;

use Base\Auth\Auth;
use Application\Transaction\Absensi\Commands\Create\CreateAbsensiCommand;
use Application\Transaction\Absensi\Commands\BulkCreate\BulkCreateAbsensiCommand;
use Application\Transaction\Absensi\Commands\Update\UpdateAbsensiCommand;
use Application\Transaction\Absensi\Commands\Delete\DeleteAbsensiCommand;
use Application\Transaction\Absensi\Queries\GetAbsensiByIdQuery;
use Application\Transaction\Absensi\Queries\GetAbsensiByListQuery;
use Application\Master\User\Queries\GetUserByListQuery;
use Shared\Transaction\Absensi\Commands\Create\CreateAbsensiRequest;
use Shared\Transaction\Absensi\Commands\Update\UpdateAbsensiRequest;
use Shared\Transaction\Absensi\Commands\Delete\DeleteAbsensiRequest;
use Shared\Transaction\Absensi\Queries\GetById\GetAbsensiByIdRequest;
use Shared\Transaction\Absensi\Queries\GetByList\GetAbsensiByListRequest;
use Shared\Master\User\Queries\GetByList\GetUserByListRequest;

class AbsensiController
{
    public function __construct(
        private CreateAbsensiCommand     $createCmd,
        private BulkCreateAbsensiCommand $bulkCreateCmd,
        private UpdateAbsensiCommand     $updateCmd,
        private DeleteAbsensiCommand     $deleteCmd,
        private GetAbsensiByIdQuery      $getById,
        private GetAbsensiByListQuery    $getList,
        private GetUserByListQuery       $getUserList,
    ) {}

    public function index(): void
    {
        Auth::requireAuth();
        $sessionUser = Auth::user();
        $role        = $sessionUser['role'];
        $isPegawai   = !in_array($role, ['admin_tu', 'kepala_sekolah']);

        $bulan  = $_GET['bulan'] ?? date('Y-m');
        // Pegawai biasa hanya bisa melihat data dirinya sendiri
        $userId = $isPegawai
            ? Auth::id()
            : (isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0);
        $status = $_GET['status'] ?? '';

        $req   = new GetAbsensiByListRequest($userId, $bulan, $status);
        $list  = $this->getList->execute($req)->data;
        $users = $isPegawai ? [] : $this->getUserList->execute(new GetUserByListRequest('', ''))->data;
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require BASE_PATH . '/08Bsui/transaction/absensi/index.php';
    }

    public function create(): void
    {
        Auth::requireRole(['admin_tu']);

        if (empty($_POST['user_id']) || empty($_POST['tanggal'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'User ID dan Tanggal wajib diisi.'];
            $this->redirect('/transaksi/absensi');
            return;
        }

        $req = new CreateAbsensiRequest(
            userId: (int)($_POST['user_id'] ?? 0),
            tanggal: trim($_POST['tanggal'] ?? ''),
            status: $_POST['status'] ?? 'hadir',
            jamMasuk: trim($_POST['jam_masuk'] ?? ''),
            jamKeluar: trim($_POST['jam_keluar'] ?? ''),
            keterangan: trim($_POST['keterangan'] ?? ''),
            potonganGaji: (float)($_POST['potongan_gaji'] ?? 0),
            actorId: Auth::id(),
        );

        $res = $this->createCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $bulan = substr($req->tanggal, 0, 7);
        $this->redirect('/transaksi/absensi?bulan=' . $bulan);
    }

    /**
     * Absensi massal: satu tanggal, banyak pegawai sekaligus.
     */
    public function createBulk(): void
    {
        Auth::requireRole(['admin_tu']);

        $tanggal = trim($_POST['tanggal'] ?? '');
        if (!$tanggal) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Tanggal wajib diisi.'];
            $this->redirect('/transaksi/absensi');
            return;
        }

        $rows = $_POST['rows'] ?? [];
        if (empty($rows)) {
            $_SESSION['flash'] = ['type' => 'warning', 'msg' => 'Tidak ada pegawai yang dipilih.'];
            $this->redirect('/transaksi/absensi?bulan=' . substr($tanggal, 0, 7));
            return;
        }

        $defaultJamMasuk  = trim($_POST['jam_masuk_bulk']  ?? '07:00');
        $defaultJamKeluar = trim($_POST['jam_keluar_bulk'] ?? '15:00');

        foreach ($rows as &$row) {
            if (empty($row['jam_masuk']))  $row['jam_masuk']  = $defaultJamMasuk;
            if (empty($row['jam_keluar'])) $row['jam_keluar'] = $defaultJamKeluar;
        }
        unset($row);

        $result = $this->bulkCreateCmd->execute($tanggal, $rows, Auth::id());

        $msg = "Berhasil mencatat {$result['success']} absensi.";
        if ($result['skipped'] > 0) $msg .= " {$result['skipped']} pegawai dilewati (sudah absen).";
        if (!empty($result['errors'])) $msg .= ' Error: ' . implode('; ', $result['errors']);

        $_SESSION['flash'] = ['type' => $result['success'] > 0 ? 'success' : 'warning', 'msg' => $msg];
        $this->redirect('/transaksi/absensi?bulan=' . substr($tanggal, 0, 7));
    }

    /**
     * API: kembalikan user_id[] yang sudah absen pada tanggal tertentu.
     * GET /transaksi/absensi/status-hari?tanggal=Y-m-d
     */
    public function statusHari(): void
    {
        Auth::requireRole(['admin_tu']);
        $tanggal = trim($_GET['tanggal'] ?? date('Y-m-d'));
        $req  = new GetAbsensiByListRequest(0, '', '', $tanggal, $tanggal);
        $list = $this->getList->execute($req)->data;
        $sudahAbsen = array_map(fn($a) => $a->getUserId(), $list);
        header('Content-Type: application/json');
        echo json_encode(['sudah_absen' => $sudahAbsen, 'tanggal' => $tanggal]);
    }

    public function update(): void
    {
        Auth::requireRole(['admin_tu']);

        if (empty($_POST['id'])) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'ID Absensi tidak ditemukan.'];
            $this->redirect('/transaksi/absensi');
            return;
        }

        $req = new UpdateAbsensiRequest(
            id: (int)($_POST['id'] ?? 0),
            status: $_POST['status'] ?? 'hadir',
            jamMasuk: trim($_POST['jam_masuk'] ?? ''),
            jamKeluar: trim($_POST['jam_keluar'] ?? ''),
            keterangan: trim($_POST['keterangan'] ?? ''),
            potonganGaji: (float)($_POST['potongan_gaji'] ?? 0),
            actorId: Auth::id(),
        );

        $res = $this->updateCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $bulan = $_POST['bulan'] ?? date('Y-m');
        $this->redirect('/transaksi/absensi?bulan=' . $bulan);
    }

    public function delete(): void
    {
        Auth::requireRole(['admin_tu']);
        $res = $this->deleteCmd->execute(new DeleteAbsensiRequest(
            (int)($_POST['id'] ?? 0),
            Auth::id()
        ));
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $bulan = $_POST['bulan'] ?? date('Y-m');
        $this->redirect('/transaksi/absensi?bulan=' . $bulan);
    }

    public function getById(): void
    {
        $res = $this->getById->execute(new GetAbsensiByIdRequest((int)($_GET['id'] ?? 0)));
        header('Content-Type: application/json');
        if (!$res->found) {
            echo json_encode(null);
            return;
        }
        $a = $res->data;
        echo json_encode([
            'id'            => $a->getId(),
            'user_id'       => $a->getUserId(),
            'nama'          => $a->getNamaUser(),
            'tanggal'       => $a->getTanggal(),
            'jam_masuk'     => $a->getJamMasuk(),
            'jam_keluar'    => $a->getJamKeluar(),
            'status'        => $a->getStatus(),
            'keterangan'    => $a->getKeterangan(),
            'potongan_gaji' => $a->getPotonganGaji(),
        ]);
    }

    private function redirect(string $p): void
    {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . $p);
        exit;
    }
}
