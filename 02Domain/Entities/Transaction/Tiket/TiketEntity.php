<?php

declare(strict_types=1);

namespace Domain\Entities\Transaction\Tiket;

use Domain\Abstracts\AuditableEntity;
use Domain\Entities\Master\Armada\ArmadaEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\ForeignKey;
use Base\Schema\Attributes\Ignore;
use Base\Schema\Attributes\Table;
use Base\Tiket\Enums\StatusPerjalanan;

#[Table(
    name: 'tikets',
    indexes: ['tujuan', 'tanggal_berangkat', 'jam_berangkat', 'harga'],
)]
class TiketEntity extends AuditableEntity
{
    // ------------------------------------------------------------------
    // Column
    // ------------------------------------------------------------------

    #[Column(type: 'int', unsigned: true, nullable: false)]
    #[ForeignKey(references: 'armada', on: 'id', onDelete: 'CASCADE')]
    private int $armadaId;

    #[Column(type: 'varchar', length: 150, nullable: false)]
    private string $tujuan;

    #[Column(type: 'date', nullable: true, default: null)]
    private ?string $tanggalBerangkat = null;

    #[Column(type: 'time', nullable: true, default: null)]
    private ?string $jamBerangkat = null;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false)]
    private ?float $harga = null;

    #[Column(type: 'tinyint', length: 1, nullable: false, default: true)]
    private bool $isFull = true;

    #[Column(type: 'enum', nullable: false, enumClass: StatusPerjalanan::class)]
    private string $statusPerjalanan = 'berlangsung';

    // ------------------------------------------------------------------
    // Join-fields
    // ------------------------------------------------------------------

    #[Ignore]
    private ?ArmadaEntity $armada = null;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------

    public function getArmadaId(): int
    {
        return $this->armadaId;
    }

    public function getTujuan(): string
    {
        return $this->tujuan;
    }

    public function getTanggalBerangkat(): ?string
    {
        return $this->tanggalBerangkat;
    }

    public function getJamBerangkat(): ?string
    {
        return $this->jamBerangkat;
    }

    public function getHarga(): ?float
    {
        return $this->harga;
    }

    public function getIsFull(): bool
    {
        return $this->isFull;
    }

    public function getStatusPerjalanan(): string
    {
        return $this->statusPerjalanan;
    }

    public function getArmada(): ?ArmadaEntity
    {
        return $this->armada;
    }

    // ------------------------------------------------------------------
    // Setters
    // ------------------------------------------------------------------

    public function setArmadaId(int $v): void
    {
        $this->armadaId = $v;
    }

    public function setTujuan(string $v): void
    {
        $this->tujuan = $v;
    }

    public function setTanggalBerangkat(?string $v): void
    {
        $this->tanggalBerangkat = $v;
    }

    public function setJamBerangkat(?string $v): void
    {
        $this->jamBerangkat = $v;
    }

    public function setHarga(?float $v): void
    {
        $this->harga = $v;
    }

    public function setIsFull(bool $v): void
    {
        $this->isFull = $v;
    }

    public function setStatusPerjalanan(string $v): void
    {
        $this->statusPerjalanan = $v;
    }

    public function setArmada(?ArmadaEntity $armada): void
    {
        $this->armada = $armada;
    }

    // ------------------------------------------------------------------
    // Domain methods
    // ------------------------------------------------------------------

    public static function create(
        int $armadaId,
        string $tujuan,
        ?string $tanggalBerangkat = null,
        ?string $jamBerangkat = null,
        ?float $harga = null,
        ?int $createdBy = null
    ): self {
        $entity = new self();

        $entity->setArmadaId($armadaId);
        $entity->setTujuan($tujuan);
        $entity->setTanggalBerangkat($tanggalBerangkat);
        $entity->setJamBerangkat($jamBerangkat);
        $entity->setHarga($harga);
        $entity->setIsFull(true);
        $entity->setStatusPerjalanan('berlangsung');

        $entity->markCreated($createdBy);

        return $entity;
    }

    public function update(
        string $tujuan,
        ?string $tanggalBerangkat,
        ?string $jamBerangkat,
        ?float $harga,
        bool $isFull,
        string $statusPerjalanan = 'berlangsung',
        ?int $updatedBy = null
    ): void {
        $this->setTujuan($tujuan);
        $this->setTanggalBerangkat($tanggalBerangkat);
        $this->setJamBerangkat($jamBerangkat);
        $this->setHarga($harga);
        $this->setIsFull($isFull);
        $this->setStatusPerjalanan($statusPerjalanan);

        $this->markUpdated($updatedBy);
    }

    public function isPerjalananSelesai(): bool
    {
        return $this->statusPerjalanan === 'selesai';
    }
}
