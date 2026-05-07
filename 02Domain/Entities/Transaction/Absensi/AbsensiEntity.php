<?php

declare(strict_types=1);

namespace Domain\Entities\Transaction\Absensi;

use Domain\Abstracts\AuditableEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\ForeignKey;
use Base\Schema\Attributes\Ignore;
use Base\Schema\Attributes\Table;
use Base\Absensi\Enums\Status;

#[Table(
    name: 'absensi',
    indexes: ['user_id', 'tanggal'],
)]
class AbsensiEntity extends AuditableEntity
{
    // ------------------------------------------------------------------
    // Column
    // ------------------------------------------------------------------
    #[Column(type: 'int', unsigned: true, nullable: false)]
    private int $userId;

    #[Column(type: 'date', nullable: false)]
    private string $tanggal;

    #[Column(type: 'time', nullable: false)]
    private string $jamMasuk;

    #[Column(type: 'time', nullable: false)]
    private string $jamKeluar;

    #[Column(type: 'enum', nullable: false, enumClass: Status::class)]
    private string $status;

    #[Column(type: 'text', nullable: true, default: null)]
    private ?string $keterangan = null;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false, default: 0)]
    private float $potonganGaji;

    // ------------------------------------------------------------------
    // Join-fields
    // ------------------------------------------------------------------
    #[Ignore]
    private ?string $namaUser = null;

    #[Ignore]
    private ?string $nip = null;

    #[Ignore]
    private ?string $namaJabatan = null;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------
    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getTanggal(): string
    {
        return $this->tanggal;
    }
    public function getJamMasuk(): string
    {
        return $this->jamMasuk;
    }
    public function getJamKeluar(): string
    {
        return $this->jamKeluar;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getKeterangan(): string
    {
        return $this->keterangan;
    }
    public function getPotonganGaji(): float
    {
        return $this->potonganGaji;
    }
    public function getNamaUser(): ?string
    {
        return $this->namaUser;
    }
    public function getNip(): ?string
    {
        return $this->nip;
    }
    public function getNamaJabatan(): ?string
    {
        return $this->namaJabatan;
    }

    // ------------------------------------------------------------------
    // Setters
    // ------------------------------------------------------------------
    public function setUserId(int $v): void
    {
        $this->userId = $v;
    }
    public function setTanggal(string $v): void
    {
        $this->tanggal = $v;
    }
    public function setJamMasuk(string $v): void
    {
        $this->jamMasuk = $v;
    }
    public function setJamKeluar(string $v): void
    {
        $this->jamKeluar = $v;
    }
    public function setStatus(string $v): void
    {
        $this->status = $v;
    }
    public function setKeterangan(string $v): void
    {
        $this->keterangan = $v;
    }
    public function setPotonganGaji(float $v): void
    {
        $this->potonganGaji = $v;
    }
    public function setNamaUser(?string $v): void
    {
        $this->namaUser = $v;
    }
    public function setNip(?string $v): void
    {
        $this->nip = $v;
    }
    public function setNamaJabatan(?string $v): void
    {
        $this->namaJabatan = $v;
    }

    // ------------------------------------------------------------------
    // Domain methods
    // ------------------------------------------------------------------
    public function getStatusLabel(): string
    {
        return match ($this->status) {
            'hadir' => 'Hadir',
            'izin'  => 'Izin',
            'sakit' => 'Sakit',
            'alpha' => 'Alpha/Tidak Hadir',
            default => $this->status,
        };
    }

    public static function create(
        int    $userId,
        string $tanggal,
        string $status,
        string $jamMasuk     = '',
        string $jamKeluar    = '',
        string $keterangan   = '',
        float  $potonganGaji = 0,
        ?int   $createdBy    = null
    ): self {
        $entity = new self();
        $entity->setUserId($userId);
        $entity->setTanggal($tanggal);
        $entity->setStatus($status);
        $entity->setJamMasuk($jamMasuk ?? '');
        $entity->setJamKeluar($jamKeluar ?? '');
        $entity->setKeterangan($keterangan ?? '');
        $entity->setPotonganGaji($potonganGaji);
        $entity->markCreated($createdBy);
        return $entity;
    }

    public function update(
        string $jamMasuk,
        string $jamKeluar,
        string $status,
        string $keterangan,
        float  $potonganGaji,
        ?int   $updatedBy = null
    ): void {
        $this->setJamMasuk($jamMasuk);
        $this->setJamKeluar($jamKeluar);
        $this->setStatus($status);
        $this->setKeterangan($keterangan);
        $this->setPotonganGaji($potonganGaji);
        $this->markUpdated($updatedBy);
    }
}
