<?php

declare(strict_types=1);

namespace WebApi\Transaction\Laporan;

use Base\Auth\Auth;
use Application\Transaction\Laporan\Queries\GetLaporanQuery;
use Client\Transaction\Tiket\TiketService;
use Client\Transaction\Pemesanan\PemesananService;

class LaporanController
{
    public function __construct(
        private GetLaporanQuery     $getLaporan,
        private TiketService        $tiketService,
        private PemesananService    $pemesananService,
    ) {}

    public function index(): void
    {
        // Laporan hanya untuk admin dan pimpinan
        Auth::requireRole(['admin', 'pimpinan']);

        $jenis   = $_GET['jenis']   ?? 'pemesanan';
        $tanggal = $_GET['tanggal'] ?? '';
        $status  = $_GET['status']  ?? '';
        $search  = $_GET['search']  ?? '';

        $laporanPemesanan = $this->getLaporanPemesanan($tanggal, $status, $search);
        $laporanTiket     = $this->getLaporanTiket($tanggal, $search);
        $summary          = $this->getSummary();

        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require BASE_PATH . '/08Bsui/transaction/laporan/index.php';
    }

    public function export(): void
    {
        // Export hanya untuk admin dan pimpinan
        Auth::requireRole(['admin', 'pimpinan']);

        $jenis   = $_GET['jenis']   ?? 'pemesanan';
        $tanggal = $_GET['tanggal'] ?? '';
        $status  = $_GET['status']  ?? '';

        if ($jenis === 'tiket') {
            $data = $this->getLaporanTiket($tanggal, '');
            $this->exportTiketCsv($data, $tanggal);
        } else {
            $data = $this->getLaporanPemesanan($tanggal, $status, '');
            $this->exportPemesananCsv($data, $tanggal);
        }
    }

    // ---------------------------------------------------------------
    // Data helpers
    // ---------------------------------------------------------------

    private function getLaporanPemesanan(string $tanggal, string $status, string $search): array
    {
        $db   = \Infrastructure\AppDbContext::getInstance();
        $sql  = "SELECT p.*, 
                    t.tujuan, t.tanggal_berangkat, t.jam_berangkat, t.harga as harga_tiket,
                    a.nama_armada, a.plat_nomor,
                    u.nama as nama_penumpang, u.email as email_penumpang, u.no_telp as no_telp_penumpang
                 FROM pemesanans p
                 LEFT JOIN tikets t ON p.tiket_id = t.id
                 LEFT JOIN armada a ON t.armada_id = a.id
                 LEFT JOIN users u ON p.user_id = u.id
                 WHERE p.is_deleted = 0";
        $params = [];

        if ($tanggal) {
            $sql .= " AND t.tanggal_berangkat = ?";
            $params[] = $tanggal;
        }
        if ($status) {
            $sql .= " AND p.status_pemesanan = ?";
            $params[] = $status;
        }
        if ($search) {
            $sql .= " AND (u.nama LIKE ? OR t.tujuan LIKE ? OR p.no_pemesanan LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " ORDER BY p.created_at DESC";
        return $db->fetchAll($sql, $params);
    }

    private function getLaporanTiket(string $tanggal, string $search): array
    {
        $db   = \Infrastructure\AppDbContext::getInstance();
        $sql  = "SELECT t.*,
                    a.nama_armada, a.plat_nomor, a.jumlah_seat,
                    COUNT(CASE WHEN p.status_pemesanan='confirmed' THEN 1 END) as seat_terjual,
                    COUNT(CASE WHEN p.status_pemesanan='pending'   THEN 1 END) as seat_pending,
                    SUM(CASE WHEN p.status_pemesanan='confirmed' THEN p.total_harga ELSE 0 END) as total_pendapatan
                 FROM tikets t
                 LEFT JOIN armada a ON t.armada_id = a.id
                 LEFT JOIN pemesanans p ON p.tiket_id = t.id AND p.is_deleted = 0
                 WHERE t.is_deleted = 0";
        $params = [];

        if ($tanggal) {
            $sql .= " AND t.tanggal_berangkat = ?";
            $params[] = $tanggal;
        }
        if ($search) {
            $sql .= " AND (t.tujuan LIKE ? OR a.nama_armada LIKE ?)";
            $params[] = "%$search%";
            $params[] = "%$search%";
        }

        $sql .= " GROUP BY t.id ORDER BY t.tanggal_berangkat DESC, t.jam_berangkat ASC";
        return $db->fetchAll($sql, $params);
    }

    private function getSummary(): array
    {
        $db = \Infrastructure\AppDbContext::getInstance();
        return [
            'total_tiket'         => $db->fetchOne("SELECT COUNT(*) as c FROM tikets WHERE is_deleted=0")['c'] ?? 0,
            'tiket_aktif'         => $db->fetchOne("SELECT COUNT(*) as c FROM tikets WHERE is_deleted=0 AND is_full=0")['c'] ?? 0,
            'total_pemesanan'     => $db->fetchOne("SELECT COUNT(*) as c FROM pemesanans WHERE is_deleted=0")['c'] ?? 0,
            'pemesanan_confirmed' => $db->fetchOne("SELECT COUNT(*) as c FROM pemesanans WHERE is_deleted=0 AND status_pemesanan='confirmed'")['c'] ?? 0,
            'pemesanan_pending'   => $db->fetchOne("SELECT COUNT(*) as c FROM pemesanans WHERE is_deleted=0 AND status_pemesanan='pending'")['c'] ?? 0,
            'total_pendapatan'    => $db->fetchOne("SELECT COALESCE(SUM(total_harga),0) as s FROM pemesanans WHERE is_deleted=0 AND status_pemesanan='confirmed'")['s'] ?? 0,
        ];
    }

    // ---------------------------------------------------------------
    // CSV Export
    // ---------------------------------------------------------------

    private function exportPemesananCsv(array $data, string $tanggal): void
    {
        $filename = "laporan_pemesanan" . ($tanggal ? "_{$tanggal}" : '_' . date('Y-m-d')) . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['No', 'No Pemesanan', 'Penumpang', 'Telepon', 'Tujuan', 'Tgl Berangkat', 'Jam', 'No Seat', 'Total Harga', 'Status', 'Status Midtrans', 'Tgl Pesan']);
        foreach ($data as $i => $row) {
            fputcsv($out, [
                $i + 1,
                $row['no_pemesanan'],
                $row['nama_penumpang'] ?? '-',
                $row['no_telp_penumpang'] ?? '-',
                $row['tujuan'] ?? '-',
                $row['tanggal_berangkat'] ?? '-',
                $row['jam_berangkat'] ?? '-',
                $row['no_seat'],
                number_format((float)($row['total_harga'] ?? 0), 0, ',', '.'),
                $row['status_pemesanan'],
                $row['midtrans_status'] ?? '-',
                $row['created_at'],
            ]);
        }
        fclose($out);
        exit;
    }

    private function exportTiketCsv(array $data, string $tanggal): void
    {
        $filename = "laporan_tiket" . ($tanggal ? "_{$tanggal}" : '_' . date('Y-m-d')) . ".csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['No', 'Tujuan', 'Armada', 'Plat', 'Tgl Berangkat', 'Jam', 'Harga', 'Kap. Total', 'Terjual', 'Pending', 'Total Pendapatan', 'Status']);
        foreach ($data as $i => $row) {
            fputcsv($out, [
                $i + 1,
                $row['tujuan'],
                $row['nama_armada'] ?? '-',
                $row['plat_nomor'] ?? '-',
                $row['tanggal_berangkat'] ?? '-',
                $row['jam_berangkat'] ?? '-',
                number_format((float)($row['harga'] ?? 0), 0, ',', '.'),
                $row['jumlah_seat'] ?? 0,
                $row['seat_terjual'] ?? 0,
                $row['seat_pending'] ?? 0,
                number_format((float)($row['total_pendapatan'] ?? 0), 0, ',', '.'),
                $row['is_full'] ? 'Penuh' : 'Tersedia',
            ]);
        }
        fclose($out);
        exit;
    }
}
