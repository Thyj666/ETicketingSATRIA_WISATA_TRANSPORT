<?php

declare(strict_types=1);

namespace Domain\Entities\Master\Golongan;

use Domain\Abstracts\AuditableEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\ForeignKey;
use Base\Schema\Attributes\Ignore;
use Base\Schema\Attributes\Table;

#[Table(
    name: 'golongan',
    uniques: ['kode_golongan'],
    indexes: ['kode_golongan', 'nama_golongan'],
)]
class GolonganEntity extends AuditableEntity
{
    // ------------------------------------------------------------------
    // Column
    // ------------------------------------------------------------------
    #[Column(type: 'varchar', length: 20, nullable: false)]
    private string $kodeGolongan;

    #[Column(type: 'varchar', length: 255, nullable: false)]
    private string $namaGolongan;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false, default: 0)]
    private float $gajiPokok;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: true, default: 0)]
    private ?float $tunjangan = 0;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------
    public function getKodeGolongan(): string
    {
        return $this->kodeGolongan;
    }
    public function getNamaGolongan(): string
    {
        return $this->namaGolongan;
    }
    public function getGajiPokok(): float
    {
        return $this->gajiPokok;
    }
    public function getTunjangan(): float
    {
        return $this->tunjangan;
    }

    // ------------------------------------------------------------------
    // Setters
    // ------------------------------------------------------------------
    public function setKodeGolongan(string $v): void
    {
        $this->kodeGolongan = $v;
    }
    public function setNamaGolongan(string $v): void
    {
        $this->namaGolongan = $v;
    }
    public function setGajiPokok(float $v): void
    {
        $this->gajiPokok = $v;
    }
    public function setTunjangan(float $v): void
    {
        $this->tunjangan = $v;
    }

    // ------------------------------------------------------------------
    // Domain methods
    // ------------------------------------------------------------------
    public static function create(
        string $kodeGolongan,
        string $namaGolongan,
        float  $gajiPokok,
        float  $tunjangan,
        ?int   $userId = null
    ): self {
        $entity = new self();
        $entity->setKodeGolongan($kodeGolongan);
        $entity->setNamaGolongan($namaGolongan);
        $entity->setGajiPokok($gajiPokok);
        $entity->setTunjangan($tunjangan);
        $entity->markCreated($userId);
        return $entity;
    }

    public function update(
        string $kodeGolongan,
        string $namaGolongan,
        float  $gajiPokok,
        float  $tunjangan,
        ?int   $userId = null
    ): void {
        $this->setKodeGolongan($kodeGolongan);
        $this->setNamaGolongan($namaGolongan);
        $this->setGajiPokok($gajiPokok);
        $this->setTunjangan($tunjangan);
        $this->markUpdated($userId);
    }
}
