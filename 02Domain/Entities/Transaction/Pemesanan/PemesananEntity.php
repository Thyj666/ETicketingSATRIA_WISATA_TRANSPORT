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
    indexes: ['no_seat', 'tanggal_pemesanan', 'jam_pemesanan', 'status_pemesanan'],
)]
class PemesananEntity extends AuditableEntity
{
    // ------------------------------------------------------------------
    // Column
    // ------------------------------------------------------------------

    #[Column(type: 'int', unsigned: true, nullable: false)]
    #[ForeignKey(references: 'armadas', on: 'id', onDelete: 'CASCADE')]
    private int $armadaId;

    #[Column(type: 'int', unsigned: true, nullable: false)]
    #[ForeignKey(references: 'users', on: 'id', onDelete: 'CASCADE')]
    private int $userId;

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

    // ------------------------------------------------------------------
    // Join-fields
    // ------------------------------------------------------------------

    #[Ignore]
    private ?ArmadaEntity $armada = null;

    #[Ignore]
    private ?UserEntity $user = null;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------

    public function getArmadaId(): int
    {
        return $this->armadaId;
    }

    public function getUserId(): int
    {
        return $this->userId;
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

    public function getArmada(): ?ArmadaEntity
    {
        return $this->armada;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    // ------------------------------------------------------------------
    // Setters
    // ------------------------------------------------------------------

    public function setArmadaId(int $v): void
    {
        $this->armadaId = $v;
    }

    public function setUserId(int $v): void
    {
        $this->userId = $v;
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

    public function setArmada(?ArmadaEntity $armada): void
    {
        $this->armada = $armada;
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }

    // ------------------------------------------------------------------
    // Domain methods
    // ------------------------------------------------------------------

    public static function create(
        int $armadaId,
        int $userId,
        string $noPemesanan,
        string $noSeat,
        ?string $tanggalPemesanan = null,
        ?string $jamPemesanan = null,
        ?string $statusPemesanan = null,
        ?int $createdBy = null
    ): self {
        $entity = new self();

        $entity->setArmadaId($armadaId);
        $entity->setUserId($userId);
        $entity->setNoPemesanan($noPemesanan);
        $entity->setNoSeat($noSeat);
        $entity->setTanggalPemesanan($tanggalPemesanan);
        $entity->setJamPemesanan($jamPemesanan);
        $entity->setStatusPemesanan($statusPemesanan);

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
