<?php

declare(strict_types=1);

namespace Client\Master\Pimpinan;

use Infrastructure\AppDbContext;
use Domain\Entities\Master\Pimpinan\PimpinanEntity;
use Domain\Entities\Master\User\UserEntity;

class PimpinanService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): PimpinanEntity
    {
        $e = new PimpinanEntity();
        $e->setId((int) $row['id']);
        $e->setUserId((int) $row['user_id']);
        $e->setNama($row['nama'] ?? '');
        $e->setEmail($row['email'] ?? null);
        $e->setNoTelp($row['no_telp'] ?? null);
        $e->setAlamat($row['alamat'] ?? null);
        $e->setFoto($row['foto'] ?? null);
        $e->setIsActive((bool)($row['is_active'] ?? 1));
        $e->setIsDeleted((bool)($row['is_deleted'] ?? 0));
        $e->setCreatedAt($row['created_at'] ?? '');
        $e->setUpdatedAt($row['updated_at'] ?? '');
        if (isset($row['username'])) {
            $u = new UserEntity();
            $u->setId((int) $row['user_id']);
            $u->setUsername($row['username']);
            $u->setRole('pimpinan');
            $u->setIsActive((bool)($row['u_is_active'] ?? 1));
            $e->setUser($u);
        }
        return $e;
    }

    public function getAll(string $search = ''): array
    {
        $sql = "SELECT p.*, u.username, u.is_active as u_is_active
                FROM pimpinan p JOIN users u ON p.user_id = u.id
                WHERE p.is_deleted = 0 AND u.is_deleted = 0";
        $params = [];
        if ($search) {
            $sql .= " AND (p.nama LIKE ? OR p.email LIKE ? OR u.username LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        $sql .= " ORDER BY p.nama ASC";
        return array_map(fn($r) => $this->mapRow($r), $this->db->fetchAll($sql, $params));
    }

    public function getById(int $id): ?PimpinanEntity
    {
        $row = $this->db->fetchOne(
            "SELECT p.*, u.username, u.is_active as u_is_active
             FROM pimpinan p JOIN users u ON p.user_id = u.id
             WHERE p.id = ? AND p.is_deleted = 0",
            [$id]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function getByUserId(int $userId): ?PimpinanEntity
    {
        $row = $this->db->fetchOne(
            "SELECT p.*, u.username, u.is_active as u_is_active
             FROM pimpinan p JOIN users u ON p.user_id = u.id
             WHERE p.user_id = ? AND p.is_deleted = 0",
            [$userId]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function save(PimpinanEntity $e): int
    {
        if ($e->isNew()) {
            $this->db->execute(
                "INSERT INTO pimpinan (user_id,nama,email,no_telp,alamat,foto,is_active,is_deleted,created_at,updated_at,created_by,updated_by)
                 VALUES (?,?,?,?,?,?,1,0,NOW(),NOW(),?,?)",
                [$e->getUserId(), $e->getNama(), $e->getEmail(), $e->getNoTelp(), $e->getAlamat(), $e->getFoto(), $e->getCreatedBy(), $e->getUpdatedBy()]
            );
            return $this->db->lastInsertId();
        }
        $this->db->execute(
            "UPDATE pimpinan SET nama=?,email=?,no_telp=?,alamat=?,is_active=?,updated_at=NOW(),updated_by=? WHERE id=?",
            [$e->getNama(), $e->getEmail(), $e->getNoTelp(), $e->getAlamat(), $e->getIsActive() ? 1 : 0, $e->getUpdatedBy(), $e->getId()]
        );
        if ($e->getFoto()) $this->db->execute("UPDATE pimpinan SET foto=? WHERE id=?", [$e->getFoto(), $e->getId()]);
        return $e->getId();
    }

    public function delete(int $id, ?int $userId = null): bool
    {
        $p = $this->getById($id);
        if ($p) $this->db->execute("UPDATE users SET is_deleted=1,updated_at=NOW() WHERE id=?", [$p->getUserId()]);
        return $this->db->execute("UPDATE pimpinan SET is_deleted=1,updated_at=NOW(),updated_by=? WHERE id=?", [$userId, $id]);
    }
}
