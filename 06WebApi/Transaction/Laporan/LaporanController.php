<?php

declare(strict_types=1);

namespace WebApi\Transaction\Laporan;

use Base\Auth\Auth;
use Application\Transaction\Laporan\Queries\GetLaporanQuery;

class LaporanController
{
    public function __construct(
        private GetLaporanQuery $getLaporan,
    ) {}

    public function index(): void
    {
        Auth::requireRole(['admin_tu', 'kepala_sekolah']);
        $periode = $_GET['periode'] ?? date('Y-m');
        $jenis   = $_GET['jenis']   ?? 'gaji';

        $laporanGaji    = $this->getLaporan->getLaporanGaji($periode);
        $laporanAbsensi = $this->getLaporan->getLaporanAbsensi($periode);
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);

        require BASE_PATH . '/08Bsui/transaction/laporan/index.php';
    }

    public function absensi(): void
    {
        Auth::requireRole(['admin_tu', 'kepala_sekolah']);
        $periode        = $_GET['periode'] ?? date('Y-m');
        $laporanAbsensi = $this->getLaporan->getLaporanAbsensi($periode);
        require BASE_PATH . '/08Bsui/transaction/laporan/index.php';
    }

    public function export(): void
    {
        Auth::requireRole(['admin_tu', 'kepala_sekolah']);
        $periode = $_GET['periode'] ?? date('Y-m');
        $jenis   = $_GET['jenis']   ?? 'gaji';

        if ($jenis === 'absensi') {
            $data = $this->getLaporan->getLaporanAbsensi($periode);
            $this->exportAbsensiCsv($data, $periode);
        } else {
            $data = $this->getLaporan->getLaporanGaji($periode);
            $this->exportGajiCsv($data, $periode);
        }
    }

    private function exportGajiCsv(array $data, string $periode): void
    {
        $filename = "laporan_gaji_{$periode}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
        fputcsv($out, ['No', 'Nama', 'NIP', 'Jabatan', 'Gaji Pokok', 'Tunjangan', 'Pot. Absensi', 'Pot. Lain', 'Total Gaji', 'Status']);
        foreach ($data as $i => $row) {
            fputcsv($out, [
                $i + 1,
                $row['nama'],
                $row['nip'] ?? '-',
                $row['nama_jabatan'] ?? '-',
                $row['gaji_pokok'],
                $row['tunjangan'],
                $row['potongan_absensi'],
                $row['potongan_lain'],
                $row['total_gaji'],
                $row['status'] === 'dibayar' ? 'Dibayar' : 'Pending',
            ]);
        }
        fclose($out);
        exit;
    }

    private function exportAbsensiCsv(array $data, string $periode): void
    {
        $filename = "laporan_absensi_{$periode}.csv";
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['No', 'Nama', 'NIP', 'Jabatan', 'Hadir', 'Izin', 'Sakit', 'Alpha', 'Total Potongan']);
        foreach ($data as $i => $row) {
            fputcsv($out, [
                $i + 1,
                $row['nama'],
                $row['nip'] ?? '-',
                $row['nama_jabatan'] ?? '-',
                $row['hadir'],
                $row['izin'],
                $row['sakit'],
                $row['alpha'],
                $row['total_potongan'],
            ]);
        }
        fclose($out);
        exit;
    }

    private function redirect(string $p): void
    {
        header('Location: ' . rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') . $p);
        exit;
    }
}
