<?php

declare(strict_types=1);

namespace Client\Transaction\Pemesanan;

use Infrastructure\AppDbContext;
use Domain\Entities\Transaction\Pemesanan\PemesananEntity;
use Domain\Entities\Master\Armada\ArmadaEntity;
use Domain\Entities\Master\User\UserEntity;

class PemesananService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): PemesananEntity
    {
        $e = new PemesananEntity();
        $e->setId((int) $row['id']);
        $e->setArmadaId((int) $row['armada_id']);
        $e->setUserId((int) $row['user_id']);
        $e->setTiketId(isset($row['tiket_id']) ? (int) $row['tiket_id'] : 0);
        $e->setNoPemesanan($row['no_pemesanan']);
        $e->setNoSeat($row['no_seat']);
        $e->setTanggalPemesanan($row['tanggal_pemesanan'] ?? null);
        $e->setJamPemesanan($row['jam_pemesanan'] ?? null);
        $e->setStatusPemesanan($row['status_pemesanan'] ?? null);
        $e->setTotalHarga(isset($row['total_harga']) ? (float) $row['total_harga'] : 0);
        $e->setMidtransOrderId($row['midtrans_order_id'] ?? null);
        $e->setMidtransToken($row['midtrans_token'] ?? null);
        $e->setMidtransStatus($row['midtrans_status'] ?? null);
        $e->setIsDeleted((bool)($row['is_deleted'] ?? 0));
        $e->setCreatedAt($row['created_at'] ?? '');
        $e->setUpdatedAt($row['updated_at'] ?? '');

        if (isset($row['nama_armada'])) {
            $armada = new ArmadaEntity();
            $armada->setId((int) $row['armada_id']);
            $armada->setNamaArmada($row['nama_armada']);
            $armada->setPlatNomor($row['plat_nomor'] ?? '');
            $armada->setTipeSeat($row['tipe_seat'] ?? '');
            $armada->setJumlahSeat((int)($row['jumlah_seat'] ?? 0));
            $armada->setStatus($row['armada_status'] ?? 'tersedia');
            $e->setArmada($armada);
        }

        if (isset($row['nama_penumpang'])) {
            $user = new UserEntity();
            $user->setId((int) $row['user_id']);
            $user->setNama($row['nama_penumpang']);
            $user->setEmail($row['email_penumpang'] ?? '');
            $user->setNoTelp($row['no_telp_penumpang'] ?? '');
            $e->setUser($user);
        }

        return $e;
    }

    public function getAll(int $userId = 0, int $tiketId = 0, string $status = ''): array
    {
        $sql = "SELECT p.*, 
                    a.nama_armada, a.plat_nomor, a.tipe_seat, a.jumlah_seat, a.status as armada_status,
                    COALESCE(pl.nama, u.nama) as nama_penumpang, pl.email as email_penumpang, pl.no_telp as no_telp_penumpang
                FROM pemesanans p
                LEFT JOIN armada a ON p.armada_id = a.id
                LEFT JOIN users u ON p.user_id = u.id
                LEFT JOIN pelanggan pl ON pl.user_id = u.id AND pl.is_deleted = 0
                WHERE p.is_deleted = 0";
        $params = [];
        if ($userId > 0) {
            $sql .= " AND p.user_id = ?";
            $params[] = $userId;
        }
        if ($tiketId > 0) {
            $sql .= " AND p.tiket_id = ?";
            $params[] = $tiketId;
        }
        if ($status) {
            $sql .= " AND p.status_pemesanan = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY p.created_at DESC";
        return array_map(fn($r) => $this->mapRow($r), $this->db->fetchAll($sql, $params));
    }

    public function getById(int $id): ?PemesananEntity
    {
        $row = $this->db->fetchOne(
            "SELECT p.*, 
                a.nama_armada, a.plat_nomor, a.tipe_seat, a.jumlah_seat, a.status as armada_status,
                COALESCE(pl.nama, u.nama) as nama_penumpang, pl.email as email_penumpang, pl.no_telp as no_telp_penumpang
             FROM pemesanans p
             LEFT JOIN armada a ON p.armada_id = a.id
             LEFT JOIN users u ON p.user_id = u.id
             LEFT JOIN pelanggan pl ON pl.user_id = u.id AND pl.is_deleted = 0
             WHERE p.id = ? AND p.is_deleted = 0",
            [$id]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function getByOrderId(string $orderId): ?PemesananEntity
    {
        $row = $this->db->fetchOne(
            "SELECT p.*, 
                a.nama_armada, a.plat_nomor, a.tipe_seat, a.jumlah_seat, a.status as armada_status,
                COALESCE(pl.nama, u.nama) as nama_penumpang, pl.email as email_penumpang, pl.no_telp as no_telp_penumpang
             FROM pemesanans p
             LEFT JOIN armada a ON p.armada_id = a.id
             LEFT JOIN users u ON p.user_id = u.id
             LEFT JOIN pelanggan pl ON pl.user_id = u.id AND pl.is_deleted = 0
             WHERE p.midtrans_order_id = ? AND p.is_deleted = 0",
            [$orderId]
        );
        return $row ? $this->mapRow($row) : null;
    }

    public function isSeatTaken(int $tiketId, string $noSeat): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM pemesanans WHERE tiket_id=? AND no_seat=? AND status_pemesanan NOT IN ('cancelled','expired') AND is_deleted=0",
            [$tiketId, $noSeat]
        );
        return (bool)$row;
    }

    public function save(PemesananEntity $e): int
    {
        $this->db->execute(
            "INSERT INTO pemesanans (armada_id, user_id, tiket_id, no_pemesanan, no_seat, tanggal_pemesanan, jam_pemesanan, status_pemesanan, total_harga, midtrans_order_id, midtrans_token, midtrans_status, is_deleted, created_by, created_at)
             VALUES (?,?,?,?,?,?,?,?,?,?,?,?,0,?,NOW())",
            [
                $e->getArmadaId(),
                $e->getUserId(),
                $e->getTiketId(),
                $e->getNoPemesanan(),
                $e->getNoSeat(),
                $e->getTanggalPemesanan(),
                $e->getJamPemesanan(),
                $e->getStatusPemesanan(),
                $e->getTotalHarga(),
                $e->getMidtransOrderId(),
                $e->getMidtransToken(),
                $e->getMidtransStatus(),
                $e->getCreatedBy()
            ]
        );
        return $this->db->lastInsertId();
    }

    public function updateStatus(int $id, string $status, ?string $midtransStatus = null): bool
    {
        $sql = "UPDATE pemesanans SET status_pemesanan=?, updated_at=NOW()";
        $params = [$status];
        if ($midtransStatus !== null) {
            $sql .= ", midtrans_status=?";
            $params[] = $midtransStatus;
        }
        $sql .= " WHERE id=? AND is_deleted=0";
        $params[] = $id;
        return $this->db->execute($sql, $params);
    }

    public function updateMidtransToken(int $id, string $token): bool
    {
        return $this->db->execute(
            "UPDATE pemesanans SET midtrans_token=?, updated_at=NOW() WHERE id=? AND is_deleted=0",
            [$token, $id]
        );
    }

    public function expirePendingPemesanan(): int
    {
        $result = $this->db->query(
            "UPDATE pemesanans p
             INNER JOIN tikets t ON p.tiket_id = t.id
             SET p.status_pemesanan='expired', p.updated_at=NOW()
             WHERE p.status_pemesanan='pending'
             AND p.is_deleted=0
             AND CONCAT(t.tanggal_berangkat,' ',t.jam_berangkat) < NOW()"
        );
        return $result->rowCount();
    }

    public function delete(int $id, int $deletedBy): bool
    {
        return $this->db->execute(
            "UPDATE pemesanans SET is_deleted=1, updated_by=?, updated_at=NOW() WHERE id=?",
            [$deletedBy, $id]
        );
    }

    public function generateNoPemesanan(): string
    {
        return 'ORD-' . strtoupper(date('Ymd')) . '-' . strtoupper(substr(uniqid(), -6));
    }

    public function countByTiket(int $tiketId, string $status = ''): int
    {
        $sql = "SELECT COUNT(*) as cnt FROM pemesanans WHERE tiket_id=? AND is_deleted=0";
        $params = [$tiketId];
        if ($status) {
            $sql .= " AND status_pemesanan=?";
            $params[] = $status;
        }
        $row = $this->db->fetchOne($sql, $params);
        return (int)($row['cnt'] ?? 0);
    }
}
