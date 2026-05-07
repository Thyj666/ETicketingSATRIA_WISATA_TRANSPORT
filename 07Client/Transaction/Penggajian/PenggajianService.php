<?php

declare(strict_types=1);

namespace Client\Transaction\Penggajian;

use Infrastructure\AppDbContext;
use Domain\Entities\Transaction\Penggajian\PenggajianEntity;

class PenggajianService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): PenggajianEntity
    {
        $e = new PenggajianEntity();
        $e->setId((int)$row['id']);
        $e->setUserId((int)$row['user_id']);
        $e->setPeriode($row['periode']);
        $e->setGajiPokok((float)$row['gaji_pokok']);
        $e->setTunjangan((float)$row['tunjangan']);
        $e->setPotonganAbsensi((float)$row['potongan_absensi']);
        $e->setPotonganLain((float)$row['potongan_lain']);
        $e->setTotalGaji((float)$row['total_gaji']);
        $e->setStatus($row['status']);
        $e->setKeterangan($row['keterangan'] ?? '');
        $e->setTanggalBayar($row['tanggal_bayar'] ?? null);
        $e->setIsDeleted((bool)$row['is_deleted']);
        $e->setCreatedAt($row['created_at'] ?? '');
        $e->setUpdatedAt($row['updated_at'] ?? '');
        if (isset($row['nama']))         $e->setNamaUser($row['nama']);
        if (isset($row['nip']))          $e->setNip($row['nip']);
        if (isset($row['nama_jabatan'])) $e->setNamaJabatan($row['nama_jabatan']);
        if (isset($row['nama_golongan'])) $e->setNamaGolongan($row['nama_golongan']);
        return $e;
    }

    public function getAll(array $filter = []): array
    {
        $sql = "SELECT p.*, u.nama, u.nip, j.nama_jabatan, g.nama_golongan
                FROM penggajian p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN jabatan j ON u.jabatan_id = j.id
                LEFT JOIN golongan g ON j.golongan_id = g.id
                WHERE p.is_deleted = 0";
        $params = [];
        if (!empty($filter['user_id'])) {
            $sql .= " AND p.user_id = ?";
            $params[] = $filter['user_id'];
        }
        if (!empty($filter['periode'])) {
            $sql .= " AND p.periode = ?";
            $params[] = $filter['periode'];
        }
        if (!empty($filter['status'])) {
            $sql .= " AND p.status = ?";
            $params[] = $filter['status'];
        }
        $sql .= " ORDER BY p.periode DESC, u.nama ASC";
        return array_map(fn($r) => $this->mapRow($r), $this->db->fetchAll($sql, $params));
    }

    public function getById(int $id): ?PenggajianEntity
    {
        $row = $this->db->fetchOne(
            "SELECT p.*, u.nama, u.nip, j.nama_jabatan, g.nama_golongan
             FROM penggajian p 
             JOIN users u ON p.user_id = u.id
             LEFT JOIN jabatan j ON u.jabatan_id = j.id
             LEFT JOIN golongan g ON j.golongan_id = g.id
             WHERE p.id=? AND p.is_deleted=0",
            [$id]
        );
        return $row ? $this->mapRow($row) : null;
    }

    /**
     * Get user's salary data from jabatan and golongan
     */
    public function getUserSalaryData(int $userId): ?array
    {
        $sql = "SELECT 
                    u.gaji_pokok as manual_gaji_pokok, 
                    COALESCE(g.gaji_pokok, 0) as golongan_gaji_pokok,
                    COALESCE(g.tunjangan, 0) as golongan_tunjangan,
                    COALESCE(j.nama_jabatan, '-') as nama_jabatan,
                    COALESCE(g.nama_golongan, '-') as nama_golongan
                FROM users u
                LEFT JOIN jabatan j ON u.jabatan_id = j.id
                LEFT JOIN golongan g ON j.golongan_id = g.id
                WHERE u.id = ? AND u.is_deleted = 0";

        $row = $this->db->fetchOne($sql, [$userId]);

        if (!$row) {
            return null;
        }

        // Prioritaskan gaji pokok dari golongan, jika tidak ada pakai manual dari user
        $gajiPokok = $row['golongan_gaji_pokok'] > 0 ? (float)$row['golongan_gaji_pokok'] : (float)$row['manual_gaji_pokok'];
        $tunjangan = (float)$row['golongan_tunjangan'];

        return [
            'gaji_pokok' => $gajiPokok,
            'tunjangan' => $tunjangan,
            'nama_jabatan' => $row['nama_jabatan'],
            'nama_golongan' => $row['nama_golongan']
        ];
    }

    /**
     * Get attendance summary for a user in a period
     */
    public function getAttendanceSummary(int $userId, string $periode): array
    {
        $sql = "SELECT 
                    COUNT(*) as total_hari_kerja,
                    SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN status='izin' THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status='sakit' THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN status='alpha' THEN 1 ELSE 0 END) as alpha,
                    COALESCE(SUM(potongan_gaji), 0) as total_potongan_absensi
                FROM absensi
                WHERE user_id = ? 
                    AND DATE_FORMAT(tanggal, '%Y-%m') = ? 
                    AND is_deleted = 0";

        $row = $this->db->fetchOne($sql, [$userId, $periode]);

        $default = [
            'total_hari_kerja' => 0,
            'hadir' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpha' => 0,
            'total_potongan_absensi' => 0
        ];

        return $row ?: $default;
    }

    /**
     * Check if payroll already exists for user and period
     */
    public function existsByUserPeriode(int $userId, string $periode, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM penggajian WHERE user_id=? AND periode=? AND is_deleted=0";
        $params = [$userId, $periode];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $row = $this->db->fetchOne($sql, $params);
        return ($row['cnt'] ?? 0) > 0;
    }

    /**
     * Create payroll with auto calculation from integrated data
     */
    public function createFromIntegration(PenggajianEntity $e): int
    {
        // Calculate total from integrated data
        $e->kalkulasiTotalGaji();

        $this->db->execute(
            "INSERT INTO penggajian (user_id, periode, gaji_pokok, tunjangan, potongan_absensi, potongan_lain, total_gaji, status, keterangan, is_deleted, created_at, updated_at, created_by, updated_by)
             VALUES (?,?,?,?,?,?,?,'pending',?,0,?,?,?,?)
             ON DUPLICATE KEY UPDATE
                gaji_pokok      = VALUES(gaji_pokok),
                tunjangan       = VALUES(tunjangan),
                potongan_absensi= VALUES(potongan_absensi),
                potongan_lain   = VALUES(potongan_lain),
                total_gaji      = VALUES(total_gaji),
                status          = 'pending',
                keterangan      = VALUES(keterangan),
                is_deleted      = 0,
                updated_at      = VALUES(updated_at),
                updated_by      = VALUES(updated_by)",
            [
                $e->getUserId(),
                $e->getPeriode(),
                $e->getGajiPokok(),
                $e->getTunjangan(),
                $e->getPotonganAbsensi(),
                $e->getPotonganLain(),
                $e->getTotalGaji(),
                $e->getKeterangan(),
                $e->getCreatedAt(),
                $e->getUpdatedAt(),
                $e->getCreatedBy(),
                $e->getUpdatedBy()
            ]
        );
        $insertedId = $this->db->lastInsertId();
        if ($insertedId === 0) {
            $row = $this->db->fetchOne(
                "SELECT id FROM penggajian WHERE user_id=? AND periode=?",
                [$e->getUserId(), $e->getPeriode()]
            );
            $insertedId = $row ? (int)$row['id'] : 0;
        }
        return $insertedId;
    }

    public function update(PenggajianEntity $e): int
    {
        $e->kalkulasiTotalGaji();

        $this->db->execute(
            "UPDATE penggajian SET tunjangan=?, potongan_absensi=?, potongan_lain=?, total_gaji=?, status=?, keterangan=?, tanggal_bayar=?, updated_at=?, updated_by=? WHERE id=?",
            [
                $e->getTunjangan(),
                $e->getPotonganAbsensi(),
                $e->getPotonganLain(),
                $e->getTotalGaji(),
                $e->getStatus(),
                $e->getKeterangan(),
                $e->getTanggalBayar(),
                $e->getUpdatedAt(),
                $e->getUpdatedBy(),
                $e->getId()
            ]
        );
        return $e->getId();
    }

    public function delete(int $id, ?int $userId = null): bool
    {
        return $this->db->execute(
            "UPDATE penggajian SET is_deleted=1, updated_at=NOW(), updated_by=? WHERE id=?",
            [$userId, $id]
        );
    }

    public function getStatsByPeriode(string $periode): array
    {
        return $this->db->fetchAll(
            "SELECT 
                p.status,
                COUNT(*) as total,
                SUM(p.total_gaji) as total_gaji
             FROM penggajian p
             WHERE p.periode = ? AND p.is_deleted = 0
             GROUP BY p.status",
            [$periode]
        );
    }
}
