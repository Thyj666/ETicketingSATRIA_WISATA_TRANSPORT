<?php

declare(strict_types=1);

namespace Domain\Entities\Transaction\Penggajian;

use Domain\Abstracts\AuditableEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\ForeignKey;
use Base\Schema\Attributes\Ignore;
use Base\Schema\Attributes\Table;
use Base\Penggajian\Enums\StatusPenggajian;

#[Table(
    name: 'penggajian',
    indexes: ['user_id', 'periode'],
)]
class PenggajianEntity extends AuditableEntity
{
    #[Column(type: 'int', unsigned: true, nullable: false)]
    private int $userId;

    #[Column(type: 'varchar', length: 7, nullable: false)]
    private string $periode;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false)]
    private float $gajiPokok;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false)]
    private float $tunjangan;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false)]
    private float $potonganAbsensi;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false)]
    private float $potonganLain;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false)]
    private float $totalGaji;

    #[Column(type: 'enum', nullable: false, enumClass: StatusPenggajian::class, default: StatusPenggajian::Pending->value)]
    private string $status;

    #[Column(type: 'text', nullable: true, default: null)]
    private string $keterangan;

    #[Column(type: 'date', nullable: true, default: null)]
    private ?string $tanggalBayar;

    // ------------------------------------------------------------------
    // Join-fields
    // ------------------------------------------------------------------
    #[Ignore]
    private ?string $namaUser = null;

    #[Ignore]
    private ?string $nip = null;

    #[Ignore]
    private ?string $namaJabatan = null;

    #[Ignore]
    private ?string $namaGolongan = null;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------
    private array $summaryAbsensi = [];

    public function getUserId(): int
    {
        return $this->userId;
    }
    public function getPeriode(): string
    {
        return $this->periode;
    }
    public function getGajiPokok(): float
    {
        return $this->gajiPokok;
    }
    public function getTunjangan(): float
    {
        return $this->tunjangan;
    }
    public function getPotonganAbsensi(): float
    {
        return $this->potonganAbsensi;
    }
    public function getPotonganLain(): float
    {
        return $this->potonganLain;
    }
    public function getTotalGaji(): float
    {
        return $this->totalGaji;
    }
    public function getStatus(): string
    {
        return $this->status;
    }
    public function getKeterangan(): string
    {
        return $this->keterangan;
    }
    public function getTanggalBayar(): ?string
    {
        return $this->tanggalBayar;
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
    public function getNamaGolongan(): ?string
    {
        return $this->namaGolongan;
    }
    public function getSummaryAbsensi(): array
    {
        return $this->summaryAbsensi;
    }

    // ------------------------------------------------------------------
    // Setters
    // ------------------------------------------------------------------
    public function setUserId(int $v): void
    {
        $this->userId = $v;
    }
    public function setPeriode(string $v): void
    {
        $this->periode = $v;
    }
    public function setGajiPokok(float $v): void
    {
        $this->gajiPokok = $v;
    }
    public function setTunjangan(float $v): void
    {
        $this->tunjangan = $v;
    }
    public function setPotonganAbsensi(float $v): void
    {
        $this->potonganAbsensi = $v;
    }
    public function setPotonganLain(float $v): void
    {
        $this->potonganLain = $v;
    }
    public function setStatus(string $v): void
    {
        $this->status = $v;
    }
    public function setKeterangan(string $v): void
    {
        $this->keterangan = $v;
    }
    public function setTanggalBayar(?string $v): void
    {
        $this->tanggalBayar = $v;
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
    public function setNamaGolongan(?string $v): void
    {
        $this->namaGolongan = $v;
    }
    public function setSummaryAbsensi(array $v): void
    {
        $this->summaryAbsensi = $v;
    }

    // ------------------------------------------------------------------
    // Domain methods
    // ------------------------------------------------------------------
    public function kalkulasiTotalGaji(): void
    {
        $potongan = $this->potonganAbsensi + $this->potonganLain;
        $this->totalGaji = max(0, $this->gajiPokok + $this->tunjangan - $potongan);
    }

    public function setTotalGaji(float $v): void
    {
        $this->totalGaji = $v;
    }

    public function getPotonganTotal(): float
    {
        return $this->potonganAbsensi + $this->potonganLain;
    }

    public static function create(
        int    $userId,
        string $periode,
        float  $gajiPokok,
        float  $tunjangan,
        float  $potonganAbsensi,
        float  $potonganLain,
        string $keterangan = '',
        ?int   $createdBy  = null
    ): self {
        $entity = new self();
        $entity->setUserId($userId);
        $entity->setPeriode($periode);
        $entity->setGajiPokok($gajiPokok);
        $entity->setTunjangan($tunjangan);
        $entity->setPotonganAbsensi($potonganAbsensi);
        $entity->setPotonganLain($potonganLain);
        $entity->setKeterangan($keterangan);
        $entity->kalkulasiTotalGaji();
        $entity->markCreated($createdBy);
        return $entity;
    }

    public function update(
        float   $tunjangan,
        float   $potonganAbsensi,
        float   $potonganLain,
        float   $totalGaji,
        string  $status,
        string  $keterangan,
        ?string $tanggalBayar,
        ?int    $updatedBy = null
    ): void {
        $this->setTunjangan($tunjangan);
        $this->setPotonganAbsensi($potonganAbsensi);
        $this->setPotonganLain($potonganLain);
        $this->setTotalGaji($totalGaji);
        $this->setStatus($status);
        $this->setKeterangan($keterangan);
        $this->setTanggalBayar($tanggalBayar);
        $this->markUpdated($updatedBy);
    }
}
