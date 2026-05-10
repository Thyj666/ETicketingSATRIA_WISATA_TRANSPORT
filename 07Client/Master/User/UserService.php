<?php

declare(strict_types=1);

namespace Client\Master\User;

use Infrastructure\AppDbContext;
use Domain\Entities\Master\User\UserEntity;

class UserService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): UserEntity
    {
        $e = new UserEntity();
        $e->setId((int) $row['id']);
        // BUG FIX: mapping field 'nama' yang sebelumnya tidak ada di mapRow
        $e->setNama($row['nama'] ?? null);
        $e->setUsername($row['username'] ?? '');
        $e->setPassword($row['password'] ?? '');
        $e->setRole($row['role'] ?? 'pelanggan');
        $e->setIsActive((bool)($row['is_active'] ?? 1));
        $e->setIsDeleted((bool)($row['is_deleted'] ?? 0));
        $e->setCreatedAt($row['created_at'] ?? '');
        $e->setUpdatedAt($row['updated_at'] ?? '');
        return $e;
    }

    public function getAll(string $search = '', string $role = ''): array
    {
        $sql    = "SELECT u.* FROM users u WHERE u.is_deleted = 0";
        $params = [];
        if ($search) {
            $sql      .= " AND (u.username LIKE ? OR u.role LIKE ?)";
            $params[]  = "%$search%";
            $params[]  = "%$search%";
        }
        if ($role) {
            $sql      .= " AND u.role = ?";
            $params[]  = $role;
        }
        $sql .= " ORDER BY u.id DESC";
        return array_map(fn($r) => $this->mapRow($r), $this->db->fetchAll($sql, $params));
    }

    public function getById(int $id): ?UserEntity
    {
        $row = $this->db->fetchOne(
            "SELECT u.* FROM users u WHERE u.id = ? AND u.is_deleted = 0",
            [$id]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function getByUsername(string $username): ?UserEntity
    {
        $row = $this->db->fetchOne(
            "SELECT u.* FROM users u WHERE u.username = ? AND u.is_deleted = 0",
            [$username]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function save(UserEntity $e): int
    {
        if ($e->isNew()) {
            $this->db->execute(
                "INSERT INTO users (nama, username, password, role, is_active, is_deleted, created_at, updated_at, created_by, updated_by)
                 VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?, ?)",
                [
                    $e->getNama(),
                    $e->getUsername(),
                    $e->getPassword(),
                    $e->getRole(),
                    $e->getIsActive() ? 1 : 0,
                    $e->getCreatedAt() ?? date('Y-m-d H:i:s'),
                    $e->getUpdatedAt() ?? date('Y-m-d H:i:s'),
                    $e->getCreatedBy(),
                    $e->getUpdatedBy(),
                ]
            );
            return $this->db->lastInsertId();
        }

        $this->db->execute(
            "UPDATE users SET role=?, is_active=?, updated_at=?, updated_by=? WHERE id=?",
            [
                $e->getRole(),
                $e->getIsActive() ? 1 : 0,
                date('Y-m-d H:i:s'),
                $e->getUpdatedBy(),
                $e->getId()
            ]
        );
        if ($e->getPassword()) {
            $this->db->execute("UPDATE users SET password=? WHERE id=?", [$e->getPassword(), $e->getId()]);
        }
        return $e->getId();
    }

    public function delete(int $id, ?int $userId = null): bool
    {
        return $this->db->execute(
            "UPDATE users SET is_deleted=1, updated_at=NOW(), updated_by=? WHERE id=?",
            [$userId, $id]
        );
    }

    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $sql    = "SELECT COUNT(*) as cnt FROM users WHERE username=? AND is_deleted=0";
        $params = [$username];
        if ($excludeId) {
            $sql      .= " AND id != ?";
            $params[]  = $excludeId;
        }
        $row = $this->db->fetchOne($sql, $params);
        return ($row['cnt'] ?? 0) > 0;
    }

    public function countByRole(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT role, COUNT(*) as total FROM users WHERE is_deleted=0 AND is_active=1 GROUP BY role"
        );
        $result = [];
        foreach ($rows as $r) $result[$r['role']] = (int)$r['total'];
        return $result;
    }
}
