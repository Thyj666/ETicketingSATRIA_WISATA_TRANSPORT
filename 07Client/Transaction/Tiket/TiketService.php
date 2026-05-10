<?php

declare(strict_types=1);

namespace Client\Transaction\Tiket;

use Infrastructure\AppDbContext;
use Domain\Entities\Transaction\Tiket\TiketEntity;
use Domain\Entities\Master\Armada\ArmadaEntity;

class TiketService
{
    public function __construct(private AppDbContext $db) {}

    private function mapRow(array $row): TiketEntity
    {
        $e = new TiketEntity();
        $e->setId((int) $row['id']);
        $e->setArmadaId((int) $row['armada_id']);
        $e->setTujuan($row['tujuan']);
        $e->setTanggalBerangkat($row['tanggal_berangkat'] ?? null);
        $e->setJamBerangkat($row['jam_berangkat'] ?? null);
        $e->setHarga(isset($row['harga']) ? (float) $row['harga'] : null);
        $e->setIsFull((bool)($row['is_full'] ?? 1));
        $e->setStatusPerjalanan($row['status_perjalanan'] ?? 'berlangsung');
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

        return $e;
    }

    /**
     * Cek apakah armada masih memiliki tiket aktif (status_perjalanan = 'berlangsung')
     */
    public function hasActiveTicket(int $armadaId): bool
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM tikets WHERE armada_id = ? AND status_perjalanan = 'berlangsung' AND is_deleted = 0 LIMIT 1",
            [$armadaId]
        );
        return $row !== null;
    }

    /**
     * Ambil tiket aktif (berlangsung) untuk armada tertentu
     */
    public function getActiveTicketByArmada(int $armadaId): ?TiketEntity
    {
        $row = $this->db->fetchOne(
            "SELECT t.*, a.nama_armada, a.plat_nomor, a.tipe_seat, a.jumlah_seat, a.status as armada_status
             FROM tikets t LEFT JOIN armada a ON t.armada_id = a.id
             WHERE t.armada_id = ? AND t.status_perjalanan = 'berlangsung' AND t.is_deleted = 0
             LIMIT 1",
            [$armadaId]
        );
        return $row ? $this->mapRow($row) : null;
    }

    /**
     * Dapatkan daftar armada_id yang masih memiliki tiket berlangsung
     */
    public function getArmadaIdsWithActiveTicket(): array
    {
        $rows = $this->db->fetchAll(
            "SELECT DISTINCT armada_id FROM tikets WHERE status_perjalanan = 'berlangsung' AND is_deleted = 0"
        );
        return array_column($rows, 'armada_id');
    }

    public function getAll(int $armadaId = 0, string $search = '', string $tujuan = '', string $tanggal = ''): array
    {
        $sql = "SELECT t.*, a.nama_armada, a.plat_nomor, a.tipe_seat, a.jumlah_seat, a.status as armada_status
                FROM tikets t
                LEFT JOIN armada a ON t.armada_id = a.id
                WHERE t.is_deleted = 0";
        $params = [];
        if ($armadaId > 0) {
            $sql .= " AND t.armada_id = ?";
            $params[] = $armadaId;
        }
        if ($search) {
            $sql .= " AND t.tujuan LIKE ?";
            $params[] = "%$search%";
        }
        if ($tujuan) {
            $sql .= " AND t.tujuan LIKE ?";
            $params[] = "%$tujuan%";
        }
        if ($tanggal) {
            $sql .= " AND t.tanggal_berangkat = ?";
            $params[] = $tanggal;
        }
        $sql .= " ORDER BY t.tanggal_berangkat DESC, t.jam_berangkat ASC";
        return array_map(fn($r) => $this->mapRow($r), $this->db->fetchAll($sql, $params));
    }

    public function getById(int $id): ?TiketEntity
    {
        $row = $this->db->fetchOne(
            "SELECT t.*, a.nama_armada, a.plat_nomor, a.tipe_seat, a.jumlah_seat, a.status as armada_status
             FROM tikets t LEFT JOIN armada a ON t.armada_id = a.id
             WHERE t.id = ? AND t.is_deleted = 0",
            [$id]
        );
        return $row ? $this->mapRow($row) : null;
    }

    /**
     * Get taken seat numbers for a specific tiket
     */
    public function getTakenSeats(int $tiketId): array
    {
        $rows = $this->db->fetchAll(
            "SELECT no_seat FROM pemesanans WHERE tiket_id = ? AND status_pemesanan NOT IN ('cancelled','expired') AND is_deleted = 0",
            [$tiketId]
        );
        return array_column($rows, 'no_seat');
    }

    public function save(TiketEntity $e): int
    {
        $this->db->execute(
            "INSERT INTO tikets (armada_id, tujuan, tanggal_berangkat, jam_berangkat, harga, is_full, status_perjalanan, is_deleted, created_by, created_at)
             VALUES (?,?,?,?,?,0,'berlangsung',0,?,NOW())",
            [$e->getArmadaId(), $e->getTujuan(), $e->getTanggalBerangkat(), $e->getJamBerangkat(), $e->getHarga(), $e->getCreatedBy()]
        );
        return $this->db->lastInsertId();
    }

    public function update(TiketEntity $e): bool
    {
        return $this->db->execute(
            "UPDATE tikets SET armada_id=?, tujuan=?, tanggal_berangkat=?, jam_berangkat=?, harga=?, is_full=?, status_perjalanan=?, updated_by=?, updated_at=NOW()
             WHERE id=? AND is_deleted=0",
            [$e->getArmadaId(), $e->getTujuan(), $e->getTanggalBerangkat(), $e->getJamBerangkat(), $e->getHarga(), $e->getIsFull() ? 1 : 0, $e->getStatusPerjalanan(), $e->getUpdatedBy(), $e->getId()]
        );
    }

    public function updateFullStatus(int $id, bool $isFull): bool
    {
        return $this->db->execute(
            "UPDATE tikets SET is_full=?, updated_at=NOW() WHERE id=? AND is_deleted=0",
            [$isFull ? 1 : 0, $id]
        );
    }

    public function delete(int $id, int $deletedBy): bool
    {
        return $this->db->execute(
            "UPDATE tikets SET is_deleted=1, updated_by=?, updated_at=NOW() WHERE id=?",
            [$deletedBy, $id]
        );
    }

    /**
     * Mark tickets as expired: tanggal+jam sudah lewat atau is_full
     */
    public function expireOldTickets(): int
    {
        $result = $this->db->query(
            "UPDATE tikets SET is_full=1, updated_at=NOW()
             WHERE is_deleted=0 AND is_full=0
             AND CONCAT(tanggal_berangkat,' ',jam_berangkat) < NOW()"
        );
        return $result->rowCount();
    }
}
