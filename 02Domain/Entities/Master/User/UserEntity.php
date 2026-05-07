<?php

declare(strict_types=1);

namespace Domain\Entities\Master\User;

use Domain\Abstracts\AuditableEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\ForeignKey;
use Base\Schema\Attributes\Ignore;
use Base\Schema\Attributes\Table;
use Base\User\Enums\Role;
use Base\User\Enums\JenisKelamin;

#[Table(
    name: 'users',
    uniques: ['username', 'nip'],
    indexes: ['jabatan_id', 'role'],
)]
class UserEntity extends AuditableEntity
{
    // ------------------------------------------------------------------
    // Column
    // ------------------------------------------------------------------
    #[Column(type: 'varchar', length: 150, nullable: false)]
    private string $nama;

    #[Column(type: 'varchar', length: 60, nullable: false)]
    private string $username;

    #[Column(type: 'varchar', length: 255, nullable: false)]
    private string $password;

    #[Column(type: 'varchar', length: 150, nullable: true, default: null)]
    private ?string $email = null;

    #[Column(type: 'varchar', length: 30, nullable: true, default: null)]
    private ?string $nip = null;

    #[Column(type: 'varchar', length: 20, nullable: true, default: null)]
    private ?string $noTelp = null;

    #[Column(type: 'text', nullable: true, default: null)]
    private ?string $alamat = null;

    #[Column(type: 'enum', nullable: false, enumClass: Role::class)]
    private string $role;

    #[Column(type: 'int', unsigned: true, nullable: true, default: 0)]
    #[ForeignKey(references: 'jabatan', on: 'id', onDelete: 'SET NULL')]
    private ?int $jabatanId = 0;

    #[Column(type: 'decimal', precision: 15, scale: 2, nullable: false, default: 0)]
    private float $gajiPokok;

    #[Column(type: 'enum', nullable: false, enumClass: JenisKelamin::class, default: JenisKelamin::LakiLaki->value)]
    private string $jenisKelamin;

    #[Column(type: 'varchar', nullable: true, default: null)]
    private ?string $foto = null;

    #[Column(type: 'tinyint', length: 1, nullable: false, default: true)]
    private bool $isActive;

    // ------------------------------------------------------------------
    // Join-fields
    // ------------------------------------------------------------------
    #[Ignore]
    private ?string $namaJabatan = null;

    #[Ignore]
    private ?string $namaGolongan = null;

    #[Ignore]
    private ?float $tunjangan = null;

    #[Ignore]
    private ?string $kodeGolongan = null;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------
    public function getNama(): string
    {
        return $this->nama;
    }
    public function getUsername(): string
    {
        return $this->username;
    }
    public function getPassword(): string
    {
        return $this->password;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getNip(): ?string
    {
        return $this->nip;
    }
    public function getNoTelp(): ?string
    {
        return $this->noTelp;
    }
    public function getAlamat(): ?string
    {
        return $this->alamat;
    }
    public function getRole(): string
    {
        return $this->role;
    }
    public function getJabatanId(): ?int
    {
        return $this->jabatanId;
    }
    public function getGajiPokok(): float
    {
        return $this->gajiPokok;
    }
    public function getJenisKelamin(): string
    {
        return $this->jenisKelamin;
    }
    public function getFoto(): ?string
    {
        return $this->foto;
    }
    public function getIsActive(): bool
    {
        return $this->isActive;
    }
    public function getNamaJabatan(): ?string
    {
        return $this->namaJabatan;
    }
    public function getNamaGolongan(): ?string
    {
        return $this->namaGolongan;
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
    public function setNama(string $v): void
    {
        $this->nama = $v;
    }
    public function setUsername(string $v): void
    {
        $this->username = $v;
    }
    public function setPassword(string $v): void
    {
        $this->password = $v;
    }
    public function setEmail(?string $v): void
    {
        $this->email = $v;
    }
    public function setNip(?string $v): void
    {
        $this->nip = $v;
    }
    public function setNoTelp(?string $v): void
    {
        $this->noTelp = $v;
    }
    public function setAlamat(?string $v): void
    {
        $this->alamat = $v;
    }
    public function setRole(string $v): void
    {
        $this->role = $v;
    }
    public function setJabatanId(?int $v): void
    {
        $this->jabatanId = $v;
    }
    public function setGajiPokok(float $v): void
    {
        $this->gajiPokok = $v;
    }
    public function setJenisKelamin(string $v): void
    {
        $this->jenisKelamin = $v;
    }
    public function setFoto(?string $v): void
    {
        $this->foto = $v;
    }
    public function setIsActive(bool $v): void
    {
        $this->isActive = $v;
    }
    public function setNamaJabatan(?string $v): void
    {
        $this->namaJabatan = $v;
    }
    public function setNamaGolongan(?string $v): void
    {
        $this->namaGolongan = $v;
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
    public function getRoleEnum(): Role
    {
        return Role::from($this->role);
    }

    public function verifyPassword(string $plain): bool
    {
        return password_verify($plain, $this->password);
    }

    public function hashPassword(string $plain): void
    {
        $this->password = password_hash($plain, PASSWORD_BCRYPT);
    }

    public static function create(
        string  $nama,
        string  $username,
        string  $plainPassword,
        string  $email,
        string  $nip,
        string  $role,
        ?int    $jabatanId,
        float   $gajiPokok,
        string  $jenisKelamin,
        string  $noTelp = '',
        string  $alamat = '',
        ?int    $createdBy = null
    ): self {
        $entity = new self();
        $entity->setNama($nama);
        $entity->setUsername($username);
        $entity->hashPassword($plainPassword);
        $entity->setEmail($email ?: null);
        $entity->setNip($nip ?: null);
        $entity->setRole($role);
        $entity->setJabatanId($jabatanId);
        $entity->setGajiPokok($gajiPokok);
        $entity->setJenisKelamin($jenisKelamin);
        $entity->setNoTelp($noTelp ?: null);
        $entity->setAlamat($alamat ?: null);
        $entity->markCreated($createdBy);
        return $entity;
    }

    public function update(
        string $nama,
        string $email,
        string $nip,
        string $noTelp,
        string $alamat,
        string $role,
        ?int   $jabatanId,
        float  $gajiPokok,
        string $jenisKelamin,
        bool   $isActive,
        string $plainPassword = '',
        ?int   $updatedBy = null
    ): void {
        $this->setNama($nama);
        $this->setEmail($email ?: null);
        $this->setNip($nip ?: null);
        $this->setNoTelp($noTelp ?: null);
        $this->setAlamat($alamat ?: null);
        $this->setRole($role);
        $this->setJabatanId($jabatanId);
        $this->setGajiPokok($gajiPokok);
        $this->setJenisKelamin($jenisKelamin);
        $this->setIsActive($isActive);

        if (!empty($plainPassword)) {
            $this->hashPassword($plainPassword);
        }

        $this->markUpdated($updatedBy);
    }
}
