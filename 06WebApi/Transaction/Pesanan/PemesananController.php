<?php

declare(strict_types=1);

namespace WebApi\Transaction\Pemesanan;

use Base\Auth\Auth;
use Application\Transaction\Pemesanan\Commands\Create\CreatePemesananCommand;
use Application\Transaction\Pemesanan\Queries\GetPemesananByIdQuery;
use Application\Transaction\Pemesanan\Queries\GetPemesananByListQuery;
use Application\Transaction\Tiket\Queries\GetTiketByIdQuery;
use Shared\Transaction\Pemesanan\Commands\Create\CreatePemesananRequest;
use Shared\Transaction\Pemesanan\Queries\GetById\GetPemesananByIdRequest;
use Shared\Transaction\Pemesanan\Queries\GetByList\GetPemesananByListRequest;
use Shared\Transaction\Tiket\Queries\GetById\GetTiketByIdRequest;
use Client\Transaction\Pemesanan\PemesananService;

class PemesananController
{
    public function __construct(
        private CreatePemesananCommand  $createCmd,
        private GetPemesananByIdQuery   $getById,
        private GetPemesananByListQuery $getList,
        private GetTiketByIdQuery       $getTiketById,
        private PemesananService        $pemesananService,
    ) {}

    public function index(): void
    {
        Auth::requireAuth();
        $sessionUser = Auth::user();
        $role        = $sessionUser['role'] ?? '';
        $isPelanggan = $role === 'pelanggan';

        $tiketId = (int)($_GET['tiket_id'] ?? 0);
        $status  = $_GET['status'] ?? '';
        $userId  = $isPelanggan ? Auth::id() : (int)($_GET['user_id'] ?? 0);

        $list  = $this->getList->execute(new GetPemesananByListRequest($userId, $tiketId, $status))->data;
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        require BASE_PATH . '/08Bsui/transaction/pemesanan/index.php';
    }

    public function create(): void
    {
        Auth::requireAuth();
        $userId  = Auth::id();
        $tiketId = (int)($_POST['tiket_id'] ?? 0);
        $noSeat  = trim($_POST['no_seat'] ?? '');

        if (!$tiketId || !$noSeat) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Data pemesanan tidak lengkap.'];
            header('Location: ' . url('/transaksi/tiket'));
            exit;
        }

        $req = new CreatePemesananRequest($userId, $tiketId, $noSeat);
        $res = $this->createCmd->execute($req, $userId);

        if (!$res->success) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => $res->message];
            header('Location: ' . url('/transaksi/tiket'));
            exit;
        }

        // Generate Midtrans Snap token
        $snapToken = $this->createMidtransToken($res->id, $res->orderId, $res->totalHarga, $userId);
        if ($snapToken) {
            $this->pemesananService->updateMidtransToken($res->id, $snapToken);
        }

        $_SESSION['flash'] = ['type' => 'success', 'msg' => 'Pemesanan dibuat. Silakan lakukan pembayaran.'];
        header('Location: ' . url('/transaksi/pemesanan/bayar?id=' . $res->id));
        exit;
    }

    public function bayar(): void
    {
        Auth::requireAuth();
        $id    = (int)($_GET['id'] ?? 0);
        $res   = $this->getById->execute(new GetPemesananByIdRequest($id));
        $data  = $res->data;

        if (!$data || $data->getUserId() !== Auth::id()) {
            $_SESSION['flash'] = ['type' => 'danger', 'msg' => 'Pemesanan tidak ditemukan.'];
            header('Location: ' . url('/transaksi/pemesanan'));
            exit;
        }

        $midtransClientKey = getenv('MIDTRANS_CLIENT_KEY') ?: '';
        $isProduction      = filter_var(getenv('MIDTRANS_IS_PRODUCTION'), FILTER_VALIDATE_BOOLEAN);
        require BASE_PATH . '/08Bsui/transaction/pemesanan/bayar.php';
    }

    public function notifikasi(): void
    {
        // Midtrans GET ping — balas 200 OK agar webhook URL terverifikasi
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            http_response_code(200);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'ok', 'message' => 'Midtrans webhook endpoint aktif']);
            exit;
        }

        // Midtrans webhook POST — no auth needed, verify signature
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);

        if (!$data) {
            http_response_code(400);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid payload']);
            exit;
        }

        $serverKey  = getenv('MIDTRANS_SERVER_KEY') ?: '';
        $orderId    = $data['order_id'] ?? '';
        $statusCode = $data['status_code'] ?? '';
        $grossAmt   = $data['gross_amount'] ?? '';
        $signature  = $data['signature_key'] ?? '';

        // Verify signature
        $expectedSig = hash('sha512', $orderId . $statusCode . $grossAmt . $serverKey);
        if (!hash_equals($expectedSig, $signature)) {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
            exit;
        }

        $pemesanan = $this->pemesananService->getByOrderId($orderId);
        if (!$pemesanan) {
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(['status' => 'error', 'message' => 'Order not found']);
            exit;
        }

        $transactionStatus = $data['transaction_status'] ?? '';
        $fraudStatus       = $data['fraud_status'] ?? '';

        $newStatus = match (true) {
            $transactionStatus === 'capture' && $fraudStatus === 'accept' => 'confirmed',
            $transactionStatus === 'settlement'                           => 'confirmed',
            $transactionStatus === 'pending'                              => 'pending',
            in_array($transactionStatus, ['deny', 'expire', 'cancel'])   => 'cancelled',
            default => $pemesanan->getStatusPemesanan(),
        };

        $this->pemesananService->updateStatus($pemesanan->getId(), $newStatus, $transactionStatus);

        http_response_code(200);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'ok']);
    }

    public function statusPembayaran(): void
    {
        Auth::requireAuth();
        $id  = (int)($_GET['id'] ?? 0);
        $res = $this->getById->execute(new GetPemesananByIdRequest($id));
        $d   = $res->data;

        header('Content-Type: application/json');
        if (!$d || $d->getUserId() !== Auth::id()) {
            echo json_encode(['status' => 'not_found']);
            return;
        }
        echo json_encode([
            'status'          => $d->getStatusPemesanan(),
            'midtrans_status' => $d->getMidtransStatus(),
            'no_pemesanan'    => $d->getNoPemesanan(),
        ]);
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    private function createMidtransToken(int $pemesananId, string $orderId, float $amount, int $userId): ?string
    {
        $serverKey    = getenv('MIDTRANS_SERVER_KEY') ?: '';
        $isProduction = filter_var(getenv('MIDTRANS_IS_PRODUCTION'), FILTER_VALIDATE_BOOLEAN);

        if (!$serverKey) return null;

        $db   = \Infrastructure\AppDbContext::getInstance();
        $user = $db->fetchOne("SELECT nama, email, no_telp FROM users WHERE id=?", [$userId]);

        $webhookUrl = rtrim(getenv('APP_URL') ?: (
            (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
            . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
        ), '/');
        $basePath = rtrim(getenv('APP_BASE_PATH') ?: '', '/');

        $payload = [
            'transaction_details' => [
                'order_id'     => $orderId,
                'gross_amount' => (int)$amount,
            ],
            'customer_details' => [
                'first_name' => $user['nama'] ?? 'Penumpang',
                'email'      => $user['email'] ?? '',
                'phone'      => $user['no_telp'] ?? '',
            ],
            'callbacks' => [
                'finish'       => $webhookUrl . $basePath . '/transaksi/pemesanan',
                'unfinish'     => $webhookUrl . $basePath . '/transaksi/pemesanan/bayar?id=' . $pemesananId,
                'error'        => $webhookUrl . $basePath . '/transaksi/pemesanan/bayar?id=' . $pemesananId,
                'notification' => $webhookUrl . $basePath . '/transaksi/pemesanan/notifikasi',
            ],
        ];

        $url = $isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode($serverKey . ':'),
            ],
            CURLOPT_TIMEOUT        => 10,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!$response || $httpCode !== 201) return null;
        $result = json_decode($response, true);
        return $result['token'] ?? null;
    }
}
