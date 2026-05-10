<?php

declare(strict_types=1);

namespace Domain\Entities\Transaction\Pemesanan;

use Domain\Abstracts\AuditableEntity;
use Domain\Entities\Master\Armada\ArmadaEntity;
use Domain\Entities\Master\User\UserEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\ForeignKey;
use Base\Schema\Attributes\Ignore;
use Base\Schema\Attributes\Table;

#[Table(
    name: 'pemesanans',
    indexes: ['no_seat', 'tanggal_pemesanan', 'jam_pemesanan', 'status_pemesanan', 'tiket_id'],
)]
class PemesananEntity extends AuditableEntity
{
    #[Column(type: 'int', unsigned: true, nullable: false)]
    #[ForeignKey(references: 'armada', on: 'id', onDelete: 'CASCADE')]

    private int $armadaId;

    #[Column(type: 'int', unsigned: true, nullable: false)]
    #[ForeignKey(references: 'users', on: 'id', onDelete: 'CASCADE')]
    private int $userId;

    #[Column(type: 'int', unsigned: true, nullable: true, default: null)]
    #[ForeignKey(references: 'tikets', on: 'id', onDelete: 'SET NULL')]
    private ?int $tiketId = null;

    #[Column(type: 'varchar', length: 150, nullable: false)]
    private string $noPemesanan;

    #[Column(type: 'varchar', length: 150, nullable: false)]
    private string $noSeat;

    #[Column(type: 'date', nullable: true, default: null)]
    private ?string $tanggalPemesanan = null;

    #[Column(type: 'time', nullable: true, default: null)]
    private ?string $jamPemesanan = null;

    #[Column(type: 'varchar', length: 150, nullable: false)]
    private ?string $statusPemesanan = null;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false, default: 0)]
    private float $totalHarga = 0;

    #[Column(type: 'varchar', length: 200, nullable: true, default: null)]
    private ?string $midtransOrderId = null;

    #[Column(type: 'text', nullable: true, default: null)]
    private ?string $midtransToken = null;

    #[Column(type: 'varchar', length: 50, nullable: true, default: null)]
    private ?string $midtransStatus = null;

    #[Ignore]
    private ?ArmadaEntity $armada = null;

    #[Ignore]
    private ?UserEntity $user = null;

    #[Ignore]
    private ?\Domain\Entities\Transaction\Tiket\TiketEntity $tiket = null;

    // Getters
    public function getArmadaId(): int
    {
        return $this->armadaId;
    }
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getTiketId(): ?int
    {
        return $this->tiketId ?? null;
    }
    public function getNoPemesanan(): string
    {
        return $this->noPemesanan;
    }
    public function getNoSeat(): string
    {
        return $this->noSeat;
    }
    public function getTanggalPemesanan(): ?string
    {
        return $this->tanggalPemesanan;
    }
    public function getJamPemesanan(): ?string
    {
        return $this->jamPemesanan;
    }
    public function getStatusPemesanan(): ?string
    {
        return $this->statusPemesanan;
    }
    public function getTotalHarga(): float
    {
        return $this->totalHarga ?? 0;
    }
    public function getMidtransOrderId(): ?string
    {
        return $this->midtransOrderId ?? null;
    }
    public function getMidtransToken(): ?string
    {
        return $this->midtransToken ?? null;
    }
    public function getMidtransStatus(): ?string
    {
        return $this->midtransStatus ?? null;
    }
    public function getArmada(): ?ArmadaEntity
    {
        return $this->armada;
    }
    public function getUser(): ?UserEntity
    {
        return $this->user;
    }
    public function getTiket(): ?\Domain\Entities\Transaction\Tiket\TiketEntity
    {
        return $this->tiket;
    }

    // Setters
    public function setArmadaId(int $v): void
    {
        $this->armadaId = $v;
    }
    public function setUserId(int $v): void
    {
        $this->userId = $v;
    }
    public function setTiketId(?int $v): void
    {
        $this->tiketId = $v;
    }
    public function setNoPemesanan(string $v): void
    {
        $this->noPemesanan = $v;
    }
    public function setNoSeat(string $v): void
    {
        $this->noSeat = $v;
    }
    public function setTanggalPemesanan(?string $v): void
    {
        $this->tanggalPemesanan = $v;
    }
    public function setJamPemesanan(?string $v): void
    {
        $this->jamPemesanan = $v;
    }
    public function setStatusPemesanan(?string $v): void
    {
        $this->statusPemesanan = $v;
    }
    public function setTotalHarga(float $v): void
    {
        $this->totalHarga = $v;
    }
    public function setMidtransOrderId(?string $v): void
    {
        $this->midtransOrderId = $v;
    }
    public function setMidtransToken(?string $v): void
    {
        $this->midtransToken = $v;
    }
    public function setMidtransStatus(?string $v): void
    {
        $this->midtransStatus = $v;
    }
    public function setArmada(?ArmadaEntity $armada): void
    {
        $this->armada = $armada;
    }
    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }
    public function setTiket(?\Domain\Entities\Transaction\Tiket\TiketEntity $tiket): void
    {
        $this->tiket = $tiket;
    }

    // Domain methods
    public static function create(
        int $armadaId,
        int $userId,
        ?int $tiketId,
        string $noPemesanan,
        string $noSeat,
        float $totalHarga = 0,
        ?string $tanggalPemesanan = null,
        ?string $jamPemesanan = null,
        ?string $statusPemesanan = null,
        ?string $midtransOrderId = null,
        ?int $createdBy = null
    ): self {
        $entity = new self();
        $entity->setArmadaId($armadaId);
        $entity->setUserId($userId);
        $entity->setTiketId($tiketId);
        $entity->setNoPemesanan($noPemesanan);
        $entity->setNoSeat($noSeat);
        $entity->setTotalHarga($totalHarga);
        $entity->setTanggalPemesanan($tanggalPemesanan);
        $entity->setJamPemesanan($jamPemesanan);
        $entity->setStatusPemesanan($statusPemesanan ?? 'pending');
        $entity->setMidtransOrderId($midtransOrderId);
        $entity->markCreated($createdBy);
        return $entity;
    }

    public function update(
        string $noPemesanan,
        string $noSeat,
        ?string $statusPemesanan,
        ?int $updatedBy = null
    ): void {
        $this->setNoPemesanan($noPemesanan);
        $this->setNoSeat($noSeat);
        $this->setStatusPemesanan($statusPemesanan);
        $this->markUpdated($updatedBy);
    }
}
