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
        $e->setNama($row['nama']);
        $e->setUsername($row['username']);
        $e->setPassword($row['password']);
        $e->setEmail($row['email'] ?? '');
        $e->setNip($row['nip'] ?? '');
        $e->setNoTelp($row['no_telp'] ?? '');
        $e->setAlamat($row['alamat'] ?? '');
        $e->setRole($row['role']);
        $e->setJabatanId($row['jabatan_id'] ? (int)$row['jabatan_id'] : null);
        $e->setGajiPokok((float)($row['gaji_pokok'] ?? 0));
        $e->setJenisKelamin($row['jenis_kelamin'] ?? 'L');
        $e->setFoto($row['foto'] ?? null);
        $e->setIsActive((bool)($row['is_active'] ?? 1));
        $e->setIsDeleted((bool)($row['is_deleted'] ?? 0));
        $e->setCreatedAt($row['created_at'] ?? '');
        $e->setUpdatedAt($row['updated_at'] ?? '');
        if (isset($row['nama_jabatan'])) $e->setNamaJabatan($row['nama_jabatan']);
        if (isset($row['nama_golongan'])) $e->setNamaGolongan($row['nama_golongan']);
        if (isset($row['tunjangan'])) $e->setTunjangan((float)$row['tunjangan']);
        if (isset($row['kode_golongan'])) $e->setKodeGolongan($row['kode_golongan']);
        return $e;
    }

    public function getAll(string $search = '', string $role = ''): array
    {
        $sql = "SELECT u.*, j.nama_jabatan, g.nama_golongan, g.tunjangan, g.kode_golongan
                FROM users u
                LEFT JOIN jabatan j ON u.jabatan_id = j.id
                LEFT JOIN golongan g ON j.golongan_id = g.id
                WHERE u.is_deleted = 0";
        $params = [];
        if ($search) {
            $sql .= " AND (u.nama LIKE ? OR u.nip LIKE ? OR u.username LIKE ?)";
            $params = ["%$search%", "%$search%", "%$search%"];
        }
        if ($role) {
            $sql .= " AND u.role = ?";
            $params[] = $role;
        }
        $sql .= " ORDER BY u.nama ASC";
        return array_map(fn($r) => $this->mapRow($r), $this->db->fetchAll($sql, $params));
    }

    public function getPegawai(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT u.*, j.nama_jabatan, g.nama_golongan, g.tunjangan, g.kode_golongan
             FROM users u
             LEFT JOIN jabatan j ON u.jabatan_id = j.id
             LEFT JOIN golongan g ON j.golongan_id = g.id
             WHERE u.is_deleted = 0 AND u.role IN ('guru','staff') AND u.is_active = 1
             ORDER BY u.nama ASC"
        );
        return array_map(fn($r) => $this->mapRow($r), $rows);
    }

    public function getById(int $id): ?UserEntity
    {
        $row = $this->db->fetchOne(
            "SELECT u.*, j.nama_jabatan, g.nama_golongan, g.tunjangan, g.kode_golongan
             FROM users u
             LEFT JOIN jabatan j ON u.jabatan_id = j.id
             LEFT JOIN golongan g ON j.golongan_id = g.id
             WHERE u.id = ? AND u.is_deleted = 0",
            [$id]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function getByUsername(string $username): ?UserEntity
    {
        $row = $this->db->fetchOne(
            "SELECT u.*, j.nama_jabatan, g.nama_golongan, g.tunjangan, g.kode_golongan
             FROM users u
             LEFT JOIN jabatan j ON u.jabatan_id = j.id
             LEFT JOIN golongan g ON j.golongan_id = g.id
             WHERE u.username = ? AND u.is_deleted = 0",
            [$username]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function save(UserEntity $e): int
    {
        if ($e->isNew()) {
            $this->db->execute(
                "INSERT INTO users (nama, username, password, email, nip, no_telp, alamat, role, jabatan_id, gaji_pokok, jenis_kelamin, foto, is_active, is_deleted, created_at, updated_at, created_by, updated_by)
                 VALUES (?,?,?,?,?,?,?,?,?,?,?,?,1,0,?,?,?,?)",
                [
                    $e->getNama(),
                    $e->getUsername(),
                    $e->getPassword(),
                    $e->getEmail(),
                    $e->getNip(),
                    $e->getNoTelp(),
                    $e->getAlamat(),
                    $e->getRole(),
                    $e->getJabatanId(),
                    $e->getGajiPokok(),
                    $e->getJenisKelamin(),
                    $e->getFoto(),
                    $e->getCreatedAt(),
                    $e->getUpdatedAt(),
                    $e->getCreatedBy(),
                    $e->getUpdatedBy()
                ]
            );
            return $this->db->lastInsertId();
        }
        $this->db->execute(
            "UPDATE users SET nama=?, email=?, nip=?, no_telp=?, alamat=?, role=?, jabatan_id=?, gaji_pokok=?, jenis_kelamin=?, is_active=?, updated_at=?, updated_by=? WHERE id=?",
            [
                $e->getNama(),
                $e->getEmail(),
                $e->getNip(),
                $e->getNoTelp(),
                $e->getAlamat(),
                $e->getRole(),
                $e->getJabatanId(),
                $e->getGajiPokok(),
                $e->getJenisKelamin(),
                $e->getIsActive() ? 1 : 0,
                $e->getUpdatedAt(),
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
        $sql = "SELECT COUNT(*) as cnt FROM users WHERE username=? AND is_deleted=0";
        $params = [$username];
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
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
