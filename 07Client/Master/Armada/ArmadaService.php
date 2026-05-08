<?php

declare(strict_types=1);

namespace Client\Master\Armada;

use Infrastructure\AppDbContext;
use Domain\Entities\Master\Armada\ArmadaEntity;

class ArmadaService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): ArmadaEntity
    {
        $e = new ArmadaEntity();
        $e->setId((int) $row['id']);
        $e->setPlatNomor($row['plat_nomor']);
        $e->setNamaArmada($row['nama_armada']);
        $e->setTipeSeat($row['tipe_seat']);
        $e->setJumlahSeat((int) $row['jumlah_seat']);
        $e->setStatus($row['status']);
        $e->setIsDeleted((bool)($row['is_deleted'] ?? 0));
        $e->setCreatedAt($row['created_at'] ?? '');
        $e->setUpdatedAt($row['updated_at'] ?? '');
        return $e;
    }

    public function getAll(string $search = '', string $status = ''): array
    {
        $sql = "SELECT * FROM armada WHERE is_deleted = 0";
        $params = [];
        if ($search) {
            $sql .= " AND (plat_nomor LIKE ? OR nama_armada LIKE ?)";
            $params = ["%$search%", "%$search%"];
        }
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY nama_armada ASC";
        return array_map(fn($r) => $this->mapRow($r), $this->db->fetchAll($sql, $params));
    }

    public function getById(int $id): ?ArmadaEntity
    {
        $row = $this->db->fetchOne("SELECT * FROM armada WHERE id = ? AND is_deleted = 0", [$id]);
        return $row ? $this->mapRow($row) : null;
    }

    public function existsByPlatNomor(string $platNomor, int $excludeId = 0): bool
    {
        $sql = "SELECT id FROM armada WHERE plat_nomor = ? AND is_deleted = 0";
        $params = [$platNomor];
        if ($excludeId > 0) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        return (bool) $this->db->fetchOne($sql, $params);
    }

    public function save(ArmadaEntity $e): int
    {
        $this->db->execute(
            "INSERT INTO armada (plat_nomor, nama_armada, tipe_seat, jumlah_seat, status, is_deleted, created_by, created_at)
             VALUES (?,?,?,?,?,0,?,NOW())",
            [$e->getPlatNomor(), $e->getNamaArmada(), $e->getTipeSeat(), $e->getJumlahSeat(), $e->getStatus(), $e->getCreatedBy()]
        );
        return $this->db->lastInsertId();
    }

    public function update(ArmadaEntity $e): bool
    {
        return $this->db->execute(
            "UPDATE armada SET plat_nomor=?, nama_armada=?, tipe_seat=?, jumlah_seat=?, status=?, updated_by=?, updated_at=NOW()
             WHERE id=? AND is_deleted=0",
            [$e->getPlatNomor(), $e->getNamaArmada(), $e->getTipeSeat(), $e->getJumlahSeat(), $e->getStatus(), $e->getUpdatedBy(), $e->getId()]
        );
    }

    public function delete(int $id, int $deletedBy): bool
    {
        return $this->db->execute(
            "UPDATE armada SET is_deleted=1, updated_by=?, updated_at=NOW() WHERE id=?",
            [$deletedBy, $id]
        );
    }
}
