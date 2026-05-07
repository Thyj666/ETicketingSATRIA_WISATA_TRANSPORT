<?php

declare(strict_types=1);

namespace WebApi\Transaction\Penggajian;

use Base\Auth\Auth;
use Application\Transaction\Penggajian\Commands\Create\CreatePenggajianCommand;
use Application\Transaction\Penggajian\Commands\BulkCreate\BulkCreatePenggajianCommand;
use Application\Transaction\Penggajian\Commands\Update\UpdatePenggajianCommand;
use Application\Transaction\Penggajian\Commands\Delete\DeletePenggajianCommand;
use Application\Transaction\Penggajian\Queries\GetPenggajianByIdQuery;
use Application\Transaction\Penggajian\Queries\GetPenggajianByListQuery;
use Application\Master\User\Queries\GetUserByListQuery;
use Shared\Transaction\Penggajian\Commands\Create\CreatePenggajianRequest;
use Shared\Transaction\Penggajian\Commands\Update\UpdatePenggajianRequest;
use Shared\Transaction\Penggajian\Commands\Delete\DeletePenggajianRequest;
use Shared\Transaction\Penggajian\Queries\GetById\GetPenggajianByIdRequest;
use Shared\Transaction\Penggajian\Queries\GetByList\GetPenggajianByListRequest;
use Shared\Master\User\Queries\GetByList\GetUserByListRequest;

class PenggajianController
{
    public function __construct(
        private CreatePenggajianCommand     $createCmd,
        private BulkCreatePenggajianCommand $bulkCreateCmd,
        private UpdatePenggajianCommand     $updateCmd,
        private DeletePenggajianCommand     $deleteCmd,
        private GetPenggajianByIdQuery      $getById,
        private GetPenggajianByListQuery    $getList,
        private GetUserByListQuery          $getUserList,
    ) {}

    public function index(): void
    {
        Auth::requireAuth();
        $sessionUser = Auth::user();
        $role        = $sessionUser['role'];
        $isPegawai   = !in_array($role, ['admin_tu', 'kepala_sekolah']);

        $periode = $_GET['periode'] ?? date('Y-m');
        // Pegawai biasa hanya bisa melihat data dirinya sendiri
        $userId  = $isPegawai
            ? Auth::id()
            : (isset($_GET['user_id']) ? (int)$_GET['user_id'] : 0);
        $status  = $_GET['status'] ?? '';

        $req   = new GetPenggajianByListRequest($userId, $periode, $status);
        $list  = $this->getList->execute($req)->data;
        $users = $isPegawai ? [] : $this->getUserList->execute(new GetUserByListRequest('', ''))->data;
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require BASE_PATH . '/08Bsui/transaction/penggajian/index.php';
    }

    public function create(): void
    {
        Auth::requireRole(['admin_tu']);

        $userId  = (int)($_POST['user_id'] ?? 0);
        $periode = trim($_POST['periode'] ?? date('Y-m'));

        $db               = \Infrastructure\AppDbContext::getInstance();
        $penggajianService = new \Client\Transaction\Penggajian\PenggajianService($db);

        $salaryData        = $penggajianService->getUserSalaryData($userId);
        $attendanceSummary = $penggajianService->getAttendanceSummary($userId, $periode);

        if ($penggajianService->existsByUserPeriode($userId, $periode)) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Penggajian untuk pegawai ini pada periode tersebut sudah ada.'];
            $this->redirect('/transaksi/penggajian?periode=' . $periode);
            return;
        }

        $req = new CreatePenggajianRequest(
            userId: $userId,
            periode: $periode,
            gajiPokok: $salaryData['gaji_pokok'],
            tunjangan: $salaryData['tunjangan'],
            potonganAbsensi: (float)($attendanceSummary['total_potongan_absensi'] ?? 0),
            potonganLain: (float)($_POST['potongan_lain'] ?? 0),
            keterangan: trim($_POST['keterangan'] ?? ''),
            actorId: Auth::id(),
        );

        $res = $this->createCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $this->redirect('/transaksi/penggajian?periode=' . $req->periode);
    }

    /**
     * Penggajian massal: satu periode, banyak pegawai sekaligus.
     */
    public function createBulk(): void
    {
        Auth::requireRole(['admin_tu']);

        $periode = trim($_POST['periode'] ?? date('Y-m'));
        if (!$periode) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Periode wajib diisi.'];
            $this->redirect('/transaksi/penggajian');
            return;
        }

        $rows = $_POST['rows'] ?? [];
        if (empty($rows)) {
            $_SESSION['flash'] = ['type' => 'warning', 'msg' => 'Tidak ada pegawai yang dipilih.'];
            $this->redirect('/transaksi/penggajian?periode=' . $periode);
            return;
        }

        $result = $this->bulkCreateCmd->execute($periode, $rows, Auth::id());

        $msg = "Berhasil membuat {$result['success']} penggajian.";
        if ($result['skipped'] > 0) $msg .= " {$result['skipped']} pegawai dilewati (sudah digaji periode ini).";
        if (!empty($result['errors'])) $msg .= ' Error: ' . implode('; ', $result['errors']);

        $_SESSION['flash'] = ['type' => $result['success'] > 0 ? 'success' : 'warning', 'msg' => $msg];
        $this->redirect('/transaksi/penggajian?periode=' . $periode);
    }

    /**
     * API endpoint: kembalikan user_id[] yang sudah memiliki penggajian pada periode tertentu.
     * GET /transaksi/penggajian/status-periode?periode=Y-m
     */
    public function statusPeriode(): void
    {
        Auth::requireRole(['admin_tu']);
        $periode = trim($_GET['periode'] ?? date('Y-m'));

        $req         = new GetPenggajianByListRequest(0, $periode, '');
        $list        = $this->getList->execute($req)->data;
        $sudahDigaji = array_map(fn($p) => $p->getUserId(), $list);

        header('Content-Type: application/json');
        echo json_encode(['sudah_digaji' => $sudahDigaji, 'periode' => $periode]);
    }

    public function update(): void
    {
        Auth::requireRole(['admin_tu']);

        $id = (int)($_POST['id'] ?? 0);

        $db               = \Infrastructure\AppDbContext::getInstance();
        $penggajianService = new \Client\Transaction\Penggajian\PenggajianService($db);
        $existing         = $penggajianService->getById($id);

        $req = new UpdatePenggajianRequest(
            id: $id,
            tunjangan: (float)($_POST['tunjangan'] ?? 0),
            potonganAbsensi: $existing ? $existing->getPotonganAbsensi() : 0,
            potonganLain: (float)($_POST['potongan_lain'] ?? 0),
            status: $_POST['status'] ?? 'pending',
            keterangan: trim($_POST['keterangan'] ?? ''),
            tanggalBayar: trim($_POST['tanggal_bayar'] ?? '') ?: null,
            actorId: Auth::id(),
        );
        $res = $this->updateCmd->execute($req);
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $periode = $_POST['periode'] ?? date('Y-m');
        $this->redirect('/transaksi/penggajian?periode=' . $periode);
    }

    public function delete(): void
    {
        Auth::requireRole(['admin_tu']);
        $res = $this->deleteCmd->execute(new DeletePenggajianRequest(
            (int)($_POST['id'] ?? 0),
            Auth::id()
        ));
        $_SESSION['flash'] = ['type' => $res->success ? 'success' : 'danger', 'msg' => $res->message];
        $periode = $_POST['periode'] ?? date('Y-m');
        $this->redirect('/transaksi/penggajian?periode=' . $periode);
    }

    public function getById(): void
    {
        $res = $this->getById->execute(new GetPenggajianByIdRequest((int)($_GET['id'] ?? 0)));
        header('Content-Type: application/json');
        if (!$res->found) {
            echo json_encode(null);
            return;
        }
        $p = $res->data;
        echo json_encode([
            'id'               => $p->getId(),
            'user_id'          => $p->getUserId(),
            'periode'          => $p->getPeriode(),
            'gaji_pokok'       => $p->getGajiPokok(),
            'tunjangan'        => $p->getTunjangan(),
            'potongan_absensi' => $p->getPotonganAbsensi(),
            'potongan_lain'    => $p->getPotonganLain(),
            'total_gaji'       => $p->getTotalGaji(),
            'status'           => $p->getStatus(),
            'keterangan'       => $p->getKeterangan(),
            'tanggal_bayar'    => $p->getTanggalBayar(),
            'nama'             => $p->getNamaUser(),
            'nama_jabatan'     => $p->getNamaJabatan(),
        ]);
    }

    /**
     * API endpoint to get user data for create form.
     */
    public function getUserData(): void
    {
        Auth::requireAuth();

        $userId  = (int)($_GET['user_id'] ?? 0);
        $periode = $_GET['periode'] ?? date('Y-m');

        if ($userId <= 0) {
            http_response_code(400);
            require BASE_PATH . '/08Bsui/errors/400.php';
            return;
        }

        $db               = \Infrastructure\AppDbContext::getInstance();
        $penggajianService = new \Client\Transaction\Penggajian\PenggajianService($db);

        $salaryData        = $penggajianService->getUserSalaryData($userId);
        $attendanceSummary = $penggajianService->getAttendanceSummary($userId, $periode);

        if (!$salaryData) {
            $salaryData = ['gaji_pokok' => 0, 'tunjangan' => 0, 'nama_jabatan' => '-', 'nama_golongan' => '-'];
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success'          => true,
            'gaji_pokok'       => $salaryData['gaji_pokok'],
            'tunjangan'        => $salaryData['tunjangan'],
            'potongan_absensi' => $attendanceSummary['total_potongan_absensi'],
            'summary_absensi'  => [
                'total_hari_kerja'       => $attendanceSummary['total_hari_kerja'],
                'hadir'                  => $attendanceSummary['hadir'],
                'izin'                   => $attendanceSummary['izin'],
                'sakit'                  => $attendanceSummary['sakit'],
                'alpha'                  => $attendanceSummary['alpha'],
                'total_potongan_absensi' => $attendanceSummary['total_potongan_absensi'],
            ],
            'nama_jabatan'  => $salaryData['nama_jabatan']  ?? '-',
            'nama_golongan' => $salaryData['nama_golongan'] ?? '-',
        ]);
    }

    public function slipGaji(): void
    {
        Auth::requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = $this->getById->execute(new GetPenggajianByIdRequest($id));
        if (!$res->found) {
            http_response_code(404);
            require BASE_PATH . '/08Bsui/errors/404.php';
            return;
        }
        $penggajian = $res->data;
        $role       = Auth::getRole();
        // Pegawai hanya boleh lihat slip miliknya sendiri
        if (!in_array($role, ['admin_tu', 'kepala_sekolah']) && $penggajian->getUserId() !== Auth::id()) {
            http_response_code(403);
            require BASE_PATH . '/08Bsui/errors/403.php';
            return;
        }
        require BASE_PATH . '/08Bsui/transaction/penggajian/slip.php';
    }

    private function redirect(string $p): void
    {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . $p);
        exit;
    }
}
