<?php

declare(strict_types=1);

namespace Domain\Entities\Master\Armada;

use Domain\Abstracts\AuditableEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\Table;
use Base\Armada\Enums\Status;

#[Table(
    name: 'armada',
    uniques: ['plat_nomor'],
    indexes: ['plat_nomor', 'nama_armada', 'tipe_seat', 'status'],
)]
class ArmadaEntity extends AuditableEntity
{
    // ------------------------------------------------------------------
    // Column
    // ------------------------------------------------------------------

    #[Column(type: 'varchar', length: 60, nullable: false)]
    private string $platNomor;

    #[Column(type: 'varchar', length: 100, nullable: false)]
    private string $namaArmada;

    #[Column(type: 'varchar', length: 255, nullable: false)]
    private string $tipeSeat;

    #[Column(type: 'int', unsigned: true, nullable: true, default: 0)]
    private int $jumlahSeat;

    #[Column(type: 'enum', nullable: false, enumClass: Status::class)]
    private string $status;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------

    public function getPlatNomor(): string
    {
        return $this->platNomor;
    }

    public function getNamaArmada(): string
    {
        return $this->namaArmada;
    }

    public function getTipeSeat(): string
    {
        return $this->tipeSeat;
    }

    public function getJumlahSeat(): int
    {
        return $this->jumlahSeat;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    // ------------------------------------------------------------------
    // Setters
    // ------------------------------------------------------------------

    public function setPlatNomor(string $v): void
    {
        $this->platNomor = $v;
    }

    public function setNamaArmada(string $v): void
    {
        $this->namaArmada = $v;
    }

    public function setTipeSeat(string $v): void
    {
        $this->tipeSeat = $v;
    }

    public function setJumlahSeat(int $v): void
    {
        $this->jumlahSeat = $v;
    }

    public function setStatus(string $v): void
    {
        $this->status = $v;
    }

    // ------------------------------------------------------------------
    // Domain methods
    // ------------------------------------------------------------------

    public function getStatusEnum(): Status
    {
        return Status::from($this->status);
    }

    public static function create(
        string $platNomor,
        string $namaArmada,
        string $tipeSeat,
        int $jumlahSeat,
        string $status,
        ?int $createdBy = null
    ): self {
        $entity = new self();

        $entity->setPlatNomor($platNomor);
        $entity->setNamaArmada($namaArmada);
        $entity->setTipeSeat($tipeSeat);
        $entity->setJumlahSeat($jumlahSeat);
        $entity->setStatus($status);

        $entity->markCreated($createdBy);

        return $entity;
    }

    public function update(
        string $platNomor,
        string $namaArmada,
        string $tipeSeat,
        int $jumlahSeat,
        string $status,
        ?int $updatedBy = null
    ): void {
        $this->setPlatNomor($platNomor);
        $this->setNamaArmada($namaArmada);
        $this->setTipeSeat($tipeSeat);
        $this->setJumlahSeat($jumlahSeat);
        $this->setStatus($status);

        $this->markUpdated($updatedBy);
    }
}
