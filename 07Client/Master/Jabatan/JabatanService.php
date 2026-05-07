<?php

declare(strict_types=1);

namespace Client\Master\Jabatan;

use Infrastructure\AppDbContext;
use Domain\Entities\Master\Jabatan\JabatanEntity;

class JabatanService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): JabatanEntity
    {
        $e = new JabatanEntity();
        $e->setId((int) $row['id']);
        $e->setNamaJabatan($row['nama_jabatan']);
        $e->setJenis($row['jenis']);
        $e->setGolonganId($row['golongan_id'] ? (int)$row['golongan_id'] : null);
        $e->setKeterangan($row['keterangan'] ?? '');
        $e->setIsDeleted((bool) $row['is_deleted']);
        $e->setCreatedAt($row['created_at'] ?? '');
        $e->setUpdatedAt($row['updated_at'] ?? '');
        // Join fields
        if (isset($row['nama_golongan'])) $e->setNamaGolongan($row['nama_golongan']);
        if (isset($row['gaji_pokok']))    $e->setGajiPokok((float)$row['gaji_pokok']);
        if (isset($row['tunjangan']))     $e->setTunjangan((float)$row['tunjangan']);
        if (isset($row['kode_golongan'])) $e->setKodeGolongan($row['kode_golongan']);
        return $e;
    }

    public function getAll(string $search = '', string $jenis = ''): array
    {
        $sql = "SELECT j.*, g.nama_golongan, g.gaji_pokok, g.tunjangan, g.kode_golongan
                FROM jabatan j
                LEFT JOIN golongan g ON j.golongan_id = g.id
                WHERE j.is_deleted = 0";
        $params = [];
        if ($search) {
            $sql .= " AND j.nama_jabatan LIKE ?";
            $params[] = "%$search%";
        }
        if ($jenis) {
            $sql .= " AND j.jenis = ?";
            $params[] = $jenis;
        }
        $sql .= " ORDER BY j.jenis, j.nama_jabatan ASC";
        $rows = $this->db->fetchAll($sql, $params);
        return array_map(fn($r) => $this->mapRow($r), $rows);
    }

    public function getById(int $id): ?JabatanEntity
    {
        $row = $this->db->fetchOne(
            "SELECT j.*, g.nama_golongan, g.gaji_pokok, g.tunjangan, g.kode_golongan
             FROM jabatan j
             LEFT JOIN golongan g ON j.golongan_id = g.id
             WHERE j.id = ? AND j.is_deleted = 0",
            [$id]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function save(JabatanEntity $e): int
    {
        if ($e->isNew()) {
            $this->db->execute(
                "INSERT INTO jabatan (nama_jabatan, jenis, golongan_id, keterangan, is_deleted, created_at, updated_at, created_by, updated_by)
                 VALUES (?,?,?,?,0,?,?,?,?)",
                [
                    $e->getNamaJabatan(),
                    $e->getJenis(),
                    $e->getGolonganId(),
                    $e->getKeterangan(),
                    $e->getCreatedAt(),
                    $e->getUpdatedAt(),
                    $e->getCreatedBy(),
                    $e->getUpdatedBy()
                ]
            );
            return $this->db->lastInsertId();
        }
        $this->db->execute(
            "UPDATE jabatan SET nama_jabatan=?, jenis=?, golongan_id=?, keterangan=?, updated_at=?, updated_by=? WHERE id=?",
            [
                $e->getNamaJabatan(),
                $e->getJenis(),
                $e->getGolonganId(),
                $e->getKeterangan(),
                $e->getUpdatedAt(),
                $e->getUpdatedBy(),
                $e->getId()
            ]
        );
        return $e->getId();
    }

    public function isUsedByUser(int $jabatanId): bool
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) as x FROM users WHERE jabatan_id=? AND is_deleted=0",
            [$jabatanId]
        );
        return ($row['x'] ?? 0) > 0;
    }

    public function delete(int $id, ?int $userId = null): bool
    {
        return $this->db->execute(
            "UPDATE jabatan SET is_deleted=1, updated_at=NOW(), updated_by=? WHERE id=?",
            [$userId, $id]
        );
    }
}
