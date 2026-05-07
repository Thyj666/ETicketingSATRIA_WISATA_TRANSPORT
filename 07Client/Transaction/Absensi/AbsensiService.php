<?php

declare(strict_types=1);

namespace Client\Transaction\Absensi;

use Infrastructure\AppDbContext;
use Domain\Entities\Transaction\Absensi\AbsensiEntity;

class AbsensiService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): AbsensiEntity
    {
        $e = new AbsensiEntity();
        $e->setId((int)$row['id']);
        $e->setUserId((int)$row['user_id']);
        $e->setTanggal($row['tanggal']);
        $e->setJamMasuk($row['jam_masuk'] ?? '');
        $e->setJamKeluar($row['jam_keluar'] ?? '');
        $e->setStatus($row['status']);
        $e->setKeterangan($row['keterangan'] ?? '');
        $e->setPotonganGaji((float)($row['potongan_gaji'] ?? 0));
        $e->setIsDeleted((bool)($row['is_deleted'] ?? 0));
        $e->setCreatedAt($row['created_at'] ?? '');
        if (isset($row['nama'])) $e->setNamaUser($row['nama']);
        if (isset($row['nip']))  $e->setNip($row['nip']);
        if (isset($row['nama_jabatan'])) $e->setNamaJabatan($row['nama_jabatan']);
        return $e;
    }

    public function getAll(array $filter = []): array
    {
        $sql = "SELECT a.*, u.nama, u.nip, j.nama_jabatan
                FROM absensi a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN jabatan j ON u.jabatan_id = j.id
                WHERE a.is_deleted = 0";
        $params = [];

        if (!empty($filter['user_id'])) {
            $sql .= " AND a.user_id = ?";
            $params[] = $filter['user_id'];
        }
        if (!empty($filter['bulan'])) {
            $sql .= " AND DATE_FORMAT(a.tanggal, '%Y-%m') = ?";
            $params[] = $filter['bulan'];
        }
        if (!empty($filter['status'])) {
            $sql .= " AND a.status = ?";
            $params[] = $filter['status'];
        }
        if (!empty($filter['tanggal_dari'])) {
            $sql .= " AND a.tanggal >= ?";
            $params[] = $filter['tanggal_dari'];
        }
        if (!empty($filter['tanggal_sampai'])) {
            $sql .= " AND a.tanggal <= ?";
            $params[] = $filter['tanggal_sampai'];
        }
        $sql .= " ORDER BY a.tanggal DESC, u.nama ASC";
        return array_map(fn($r) => $this->mapRow($r), $this->db->fetchAll($sql, $params));
    }

    public function getById(int $id): ?AbsensiEntity
    {
        $row = $this->db->fetchOne(
            "SELECT a.*, u.nama, u.nip, j.nama_jabatan
             FROM absensi a JOIN users u ON a.user_id=u.id
             LEFT JOIN jabatan j ON u.jabatan_id=j.id
             WHERE a.id=? AND a.is_deleted=0",
            [$id]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function getSummaryByUserPeriode(int $userId, string $periode): array
    {
        $row = $this->db->fetchOne(
            "SELECT
                COUNT(*) as total_hari,
                SUM(CASE WHEN status='hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN status='izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN status='sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN status='alpha' THEN 1 ELSE 0 END) as alpha,
                SUM(potongan_gaji) as total_potongan
             FROM absensi
             WHERE user_id=? AND DATE_FORMAT(tanggal,'%Y-%m')=? AND is_deleted=0",
            [$userId, $periode]
        );
        return $row ?: ['total_hari' => 0, 'hadir' => 0, 'izin' => 0, 'sakit' => 0, 'alpha' => 0, 'total_potongan' => 0];
    }

    public function save(AbsensiEntity $e): int
    {
        if ($e->isNew()) {
            // Gunakan ON DUPLICATE KEY UPDATE agar baris yang sudah soft-deleted
            // bisa di-restore tanpa melanggar unique constraint (user_id, tanggal).
            $this->db->execute(
                "INSERT INTO absensi (user_id,tanggal,jam_masuk,jam_keluar,status,keterangan,potongan_gaji,is_deleted,created_at,updated_at,created_by,updated_by)
                 VALUES (?,?,?,?,?,?,?,0,?,?,?,?)
                 ON DUPLICATE KEY UPDATE
                    jam_masuk      = VALUES(jam_masuk),
                    jam_keluar     = VALUES(jam_keluar),
                    status         = VALUES(status),
                    keterangan     = VALUES(keterangan),
                    potongan_gaji  = VALUES(potongan_gaji),
                    is_deleted     = 0,
                    updated_at     = VALUES(updated_at),
                    updated_by     = VALUES(updated_by)",
                [
                    $e->getUserId(),
                    $e->getTanggal(),
                    $e->getJamMasuk(),
                    $e->getJamKeluar(),
                    $e->getStatus(),
                    $e->getKeterangan(),
                    $e->getPotonganGaji(),
                    $e->getCreatedAt(),
                    $e->getUpdatedAt(),
                    $e->getCreatedBy(),
                    $e->getUpdatedBy()
                ]
            );
            // ON DUPLICATE KEY UPDATE: lastInsertId() = 0 jika row lama di-update.
            // Ambil id row yang baru/terupdate berdasarkan user_id dan tanggal.
            $insertedId = $this->db->lastInsertId();
            if ($insertedId === 0) {
                $row = $this->db->fetchOne(
                    "SELECT id FROM absensi WHERE user_id=? AND tanggal=?",
                    [$e->getUserId(), $e->getTanggal()]
                );
                $insertedId = $row ? (int)$row['id'] : 0;
            }
            return $insertedId;
        }
        $this->db->execute(
            "UPDATE absensi SET jam_masuk=?,jam_keluar=?,status=?,keterangan=?,potongan_gaji=?,updated_at=?,updated_by=? WHERE id=?",
            [
                $e->getJamMasuk(),
                $e->getJamKeluar(),
                $e->getStatus(),
                $e->getKeterangan(),
                $e->getPotonganGaji(),
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
            "UPDATE absensi SET is_deleted=1,updated_at=NOW(),updated_by=? WHERE id=?",
            [$userId, $id]
        );
    }

    public function existsByUserTanggal(int $userId, string $tanggal, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM absensi WHERE user_id=? AND tanggal=? AND is_deleted=0";
        $params = [$userId, $tanggal];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $row = $this->db->fetchOne($sql, $params);
        return ($row['cnt'] ?? 0) > 0;
    }

    public function getStatsByPeriode(string $periode): array
    {
        return $this->db->fetchAll(
            "SELECT u.id, u.nama, u.nip, j.nama_jabatan,
                SUM(CASE WHEN a.status='hadir' THEN 1 ELSE 0 END) as hadir,
                SUM(CASE WHEN a.status='izin'  THEN 1 ELSE 0 END) as izin,
                SUM(CASE WHEN a.status='sakit' THEN 1 ELSE 0 END) as sakit,
                SUM(CASE WHEN a.status='alpha' THEN 1 ELSE 0 END) as alpha,
                COALESCE(SUM(a.potongan_gaji),0) as total_potongan
             FROM users u
             LEFT JOIN absensi a ON u.id=a.user_id AND DATE_FORMAT(a.tanggal,'%Y-%m')=? AND a.is_deleted=0
             LEFT JOIN jabatan j ON u.jabatan_id=j.id
             WHERE u.is_deleted=0 AND u.role IN ('guru','staff')
             GROUP BY u.id, u.nama, u.nip, j.nama_jabatan
             ORDER BY u.nama",
            [$periode]
        );
    }
}
