<?php

declare(strict_types=1);

namespace Domain\Entities\Master\Jabatan;

use Domain\Abstracts\AuditableEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\ForeignKey;
use Base\Schema\Attributes\Ignore;
use Base\Schema\Attributes\Table;

#[Table(
    name: 'jabatan',
    indexes: ['golongan_id'],
    uniques: [['nama_jabatan', 'golongan_id']]
)]
class JabatanEntity extends AuditableEntity
{
    // ------------------------------------------------------------------
    // Column
    // ------------------------------------------------------------------
    #[Column(type: 'varchar', length: 255, nullable: false)]
    private string $namaJabatan;

    #[Column(type: 'varchar', length: 150, nullable: false)]
    private string $jenis;

    #[Column(type: 'int', unsigned: true, nullable: true, default: 0)]
    #[ForeignKey(references: 'golongan', on: 'id', onDelete: 'SET NULL')]
    private ?int $golonganId = 0;

    #[Column(type: 'text', nullable: true, default: null)]
    private ?string $keterangan = null;

    // ------------------------------------------------------------------
    // Join-fields
    // ------------------------------------------------------------------
    #[Ignore]
    private ?string $namaGolongan = null;

    #[Ignore]
    private ?float  $gajiPokok = null;

    #[Ignore]
    private ?float  $tunjangan = null;

    #[Ignore]
    private ?string $kodeGolongan = null;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------
    public function getNamaJabatan(): string
    {
        return $this->namaJabatan;
    }
    public function getJenis(): string
    {
        return $this->jenis;
    }
    public function getGolonganId(): ?int
    {
        return $this->golonganId;
    }
    public function getKeterangan(): string
    {
        return $this->keterangan;
    }
    public function getNamaGolongan(): ?string
    {
        return $this->namaGolongan;
    }
    public function getGajiPokok(): ?float
    {
        return $this->gajiPokok;
    }
    public function getTunjangan(): ?float
    {
        return $this->tunjangan;
    }
    public function getKodeGolongan(): ?string
    {
        return $this->kodeGolongan;
    }

    // ------------------------------------------------------------------
    // Setters
    // ------------------------------------------------------------------
    public function setNamaJabatan(string $v): void
    {
        $this->namaJabatan = $v;
    }
    public function setJenis(string $v): void
    {
        $this->jenis = $v;
    }
    public function setGolonganId(?int $v): void
    {
        $this->golonganId = $v;
    }
    public function setKeterangan(string $v): void
    {
        $this->keterangan = $v;
    }
    public function setNamaGolongan(?string $v): void
    {
        $this->namaGolongan = $v;
    }
    public function setGajiPokok(?float $v): void
    {
        $this->gajiPokok = $v;
    }
    public function setTunjangan(?float $v): void
    {
        $this->tunjangan = $v;
    }
    public function setKodeGolongan(?string $v): void
    {
        $this->kodeGolongan = $v;
    }

    // ------------------------------------------------------------------
    // Domain methods
    // ------------------------------------------------------------------
    public static function create(
        string $namaJabatan,
        string $jenis,
        ?int   $golonganId,
        string $keterangan = '',
        ?int   $userId = null
    ): self {
        $entity = new self();
        $entity->setNamaJabatan($namaJabatan);
        $entity->setJenis($jenis);
        $entity->setGolonganId($golonganId);
        $entity->setKeterangan($keterangan);
        $entity->markCreated($userId);
        return $entity;
    }

    public function update(
        string $namaJabatan,
        string $jenis,
        ?int   $golonganId,
        string $keterangan = '',
        ?int   $userId = null
    ): void {
        $this->setNamaJabatan($namaJabatan);
        $this->setJenis($jenis);
        $this->setGolonganId($golonganId);
        $this->setKeterangan($keterangan);
        $this->markUpdated($userId);
    }
}
