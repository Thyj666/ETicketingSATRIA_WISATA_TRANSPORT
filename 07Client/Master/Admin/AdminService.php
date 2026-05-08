<?php

declare(strict_types=1);

namespace Client\Master\Admin;

use Infrastructure\AppDbContext;
use Domain\Entities\Master\Admin\AdminEntity;
use Domain\Entities\Master\User\UserEntity;

class AdminService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): AdminEntity
    {
        $e = new AdminEntity();
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
            $u->setRole($row['role'] ?? 'admin');
            $u->setIsActive((bool)($row['u_is_active'] ?? 1));
            $e->setUser($u);
        }
        return $e;
    }

    public function getAll(string $search = ''): array
    {
        $sql = "SELECT a.*, u.username, u.role, u.is_active as u_is_active
                FROM admin a JOIN users u ON a.user_id = u.id
                WHERE a.is_deleted = 0 AND u.is_deleted = 0";
        $params = [];
        if ($search) {
            $sql .= " AND (a.nama LIKE ? OR a.email LIKE ? OR u.username LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        $sql .= " ORDER BY a.nama ASC";
        return array_map(fn($r) => $this->mapRow($r), $this->db->fetchAll($sql, $params));
    }

    public function getById(int $id): ?AdminEntity
    {
        $row = $this->db->fetchOne(
            "SELECT a.*, u.username, u.role, u.is_active as u_is_active
             FROM admin a JOIN users u ON a.user_id = u.id
             WHERE a.id = ? AND a.is_deleted = 0",
            [$id]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function getByUserId(int $userId): ?AdminEntity
    {
        $row = $this->db->fetchOne(
            "SELECT a.*, u.username, u.role, u.is_active as u_is_active
             FROM admin a JOIN users u ON a.user_id = u.id
             WHERE a.user_id = ? AND a.is_deleted = 0",
            [$userId]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function save(AdminEntity $e): int
    {
        if ($e->isNew()) {
            $this->db->execute(
                "INSERT INTO admin (user_id,nama,email,no_telp,alamat,foto,is_active,is_deleted,created_at,updated_at,created_by,updated_by)
                 VALUES (?,?,?,?,?,?,1,0,NOW(),NOW(),?,?)",
                [$e->getUserId(), $e->getNama(), $e->getEmail(), $e->getNoTelp(), $e->getAlamat(), $e->getFoto(), $e->getCreatedBy(), $e->getUpdatedBy()]
            );
            return $this->db->lastInsertId();
        }
        $this->db->execute(
            "UPDATE admin SET nama=?,email=?,no_telp=?,alamat=?,is_active=?,updated_at=NOW(),updated_by=? WHERE id=?",
            [$e->getNama(), $e->getEmail(), $e->getNoTelp(), $e->getAlamat(), $e->getIsActive() ? 1 : 0, $e->getUpdatedBy(), $e->getId()]
        );
        if ($e->getFoto()) $this->db->execute("UPDATE admin SET foto=? WHERE id=?", [$e->getFoto(), $e->getId()]);
        return $e->getId();
    }

    public function delete(int $id, ?int $userId = null): bool
    {
        $admin = $this->getById($id);
        if ($admin) $this->db->execute("UPDATE users SET is_deleted=1,updated_at=NOW() WHERE id=?", [$admin->getUserId()]);
        return $this->db->execute("UPDATE admin SET is_deleted=1,updated_at=NOW(),updated_by=? WHERE id=?", [$userId, $id]);
    }
}
