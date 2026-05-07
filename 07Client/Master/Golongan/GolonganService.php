<?php

declare(strict_types=1);

namespace Client\Master\Golongan;

use Infrastructure\AppDbContext;
use Domain\Entities\Master\Golongan\GolonganEntity;

class GolonganService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): GolonganEntity
    {
        $e = new GolonganEntity();
        $e->setId((int) $row['id']);
        $e->setKodeGolongan($row['kode_golongan']);
        $e->setNamaGolongan($row['nama_golongan']);
        $e->setGajiPokok((float) $row['gaji_pokok']);
        $e->setTunjangan((float) $row['tunjangan']);
        $e->setIsDeleted((bool) $row['is_deleted']);
        $e->setCreatedAt($row['created_at'] ?? '');
        $e->setUpdatedAt($row['updated_at'] ?? '');
        return $e;
    }

    public function getAll(string $search = ''): array
    {
        $sql = "SELECT * FROM golongan WHERE is_deleted = 0";
        $params = [];
        if ($search) {
            $sql .= " AND (nama_golongan LIKE ? OR kode_golongan LIKE ?)";
            $params = ["%$search%", "%$search%"];
        }
        $sql .= " ORDER BY kode_golongan ASC";
        $rows = $this->db->fetchAll($sql, $params);
        return array_map(fn($r) => $this->mapRow($r), $rows);
    }

    public function getById(int $id): ?GolonganEntity
    {
        $row = $this->db->fetchOne("SELECT * FROM golongan WHERE id = ? AND is_deleted = 0", [$id]);
        return $row ? $this->mapRow($row) : null;
    }

    public function save(GolonganEntity $e): int
    {
        if ($e->isNew()) {
            $this->db->execute(
                "INSERT INTO golongan (kode_golongan, nama_golongan, gaji_pokok, tunjangan, is_deleted, created_at, updated_at, created_by, updated_by)
                 VALUES (?,?,?,?,0,?,?,?,?)",
                [
                    $e->getKodeGolongan(),
                    $e->getNamaGolongan(),
                    $e->getGajiPokok(),
                    $e->getTunjangan(),
                    $e->getCreatedAt(),
                    $e->getUpdatedAt(),
                    $e->getCreatedBy(),
                    $e->getUpdatedBy()
                ]
            );
            return $this->db->lastInsertId();
        }

        $this->db->execute(
            "UPDATE golongan SET kode_golongan=?, nama_golongan=?, gaji_pokok=?, tunjangan=?, updated_at=?, updated_by=? WHERE id=?",
            [
                $e->getKodeGolongan(),
                $e->getNamaGolongan(),
                $e->getGajiPokok(),
                $e->getTunjangan(),
                $e->getUpdatedAt(),
                $e->getUpdatedBy(),
                $e->getId()
            ]
        );
        return $e->getId();
    }

    public function isUsedByJabatan(int $golonganId): bool
    {
        $row = $this->db->fetchOne(
            "SELECT COUNT(*) as cnt FROM jabatan WHERE golongan_id=? AND is_deleted=0",
            [$golonganId]
        );
        return ($row['cnt'] ?? 0) > 0;
    }

    public function delete(int $id, ?int $userId = null): bool
    {
        return $this->db->execute(
            "UPDATE golongan SET is_deleted=1, updated_at=NOW(), updated_by=? WHERE id=?",
            [$userId, $id]
        );
    }

    public function existsByKode(string $kode, ?int $excludeId = null): bool
    {
        $sql = "SELECT COUNT(*) as cnt FROM golongan WHERE kode_golongan=? AND is_deleted=0";
        $params = [$kode];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        $row = $this->db->fetchOne($sql, $params);
        return ($row['cnt'] ?? 0) > 0;
    }
}
