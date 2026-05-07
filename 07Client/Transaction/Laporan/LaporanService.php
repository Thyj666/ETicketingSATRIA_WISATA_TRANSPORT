<?php

declare(strict_types=1);

namespace Client\Transaction\Laporan;

use Infrastructure\AppDbContext;

class LaporanService
{
    public function __construct(private AppDbContext $db) {}
    public function getLaporanGaji(string $periode): array
    {
        return $this->db->fetchAll(
            "SELECT p.*, u.nama, u.nip, u.role, j.nama_jabatan, g.kode_golongan, g.nama_golongan
             FROM penggajian p
             JOIN users u ON p.user_id = u.id
             LEFT JOIN jabatan j ON u.jabatan_id = j.id
             LEFT JOIN golongan g ON j.golongan_id = g.id
             WHERE p.is_deleted=0 AND p.periode=?
             ORDER BY u.nama ASC",
            [$periode]
        );
    }
    public function getLaporanAbsensi(string $periode): array
    {
        return $this->db->fetchAll(
            "SELECT u.id, u.nama, u.nip, u.role, j.nama_jabatan,
                SUM(CASE WHEN a.status='hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.status='izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.status='sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.status='alpha' THEN 1 ELSE 0 END) as alpha,
                COUNT(a.id) as total_hari,
                COALESCE(SUM(a.potongan_gaji),0) as total_potongan
             FROM users u
             LEFT JOIN absensi a ON u.id=a.user_id AND DATE_FORMAT(a.tanggal,'%Y-%m')=? AND a.is_deleted=0
             LEFT JOIN jabatan j ON u.jabatan_id=j.id
             WHERE u.is_deleted=0 AND u.role IN ('guru','staff')
             GROUP BY u.id, u.nama, u.nip, u.role, j.nama_jabatan
             ORDER BY u.nama",
            [$periode]
        );
    }
    public function getSummaryDashboard(): array
    {
        $totalPegawai = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users WHERE is_deleted=0 AND is_active=1 AND role IN ('guru','staff')")['cnt'] ?? 0;
        $totalGuru    = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users WHERE is_deleted=0 AND is_active=1 AND role='guru'")['cnt'] ?? 0;
        $totalStaff   = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM users WHERE is_deleted=0 AND is_active=1 AND role='staff'")['cnt'] ?? 0;
        $periode      = date('Y-m');
        $totalGajiBulanIni = $this->db->fetchOne("SELECT COALESCE(SUM(total_gaji),0) as total FROM penggajian WHERE is_deleted=0 AND periode=?", [$periode])['total'] ?? 0;
        $absensiHariIni    = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM absensi WHERE is_deleted=0 AND tanggal=CURDATE()")['cnt'] ?? 0;
        $penggajianPending = $this->db->fetchOne("SELECT COUNT(*) as cnt FROM penggajian WHERE is_deleted=0 AND status='pending' AND periode=?", [$periode])['cnt'] ?? 0;
        return compact('totalPegawai', 'totalGuru', 'totalStaff', 'totalGajiBulanIni', 'absensiHariIni', 'penggajianPending', 'periode');
    }
}
