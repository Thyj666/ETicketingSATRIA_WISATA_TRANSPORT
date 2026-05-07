<?php

declare(strict_types=1);

namespace Domain\Entities\Master\Pimpinan;

use Domain\Abstracts\AuditableEntity;
use Domain\Entities\Master\User\UserEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\ForeignKey;
use Base\Schema\Attributes\Ignore;
use Base\Schema\Attributes\Table;

#[Table(
    name: 'pimpinan',
    uniques: ['user_id'],
    indexes: ['nama', 'email', 'no_telp'],
)]
class PimpinanEntity extends AuditableEntity
{
    // ------------------------------------------------------------------
    // Column
    // ------------------------------------------------------------------

    #[Column(type: 'int', unsigned: true, nullable: false)]
    #[ForeignKey(references: 'users', on: 'id', onDelete: 'CASCADE')]
    private int $userId;

    #[Column(type: 'varchar', length: 150, nullable: false)]
    private string $nama;

    #[Column(type: 'varchar', length: 150, nullable: true, default: null)]
    private ?string $email = null;

    #[Column(type: 'varchar', length: 20, nullable: true, default: null)]
    private ?string $noTelp = null;

    #[Column(type: 'text', nullable: true, default: null)]
    private ?string $alamat = null;

    #[Column(type: 'varchar', length: 255, nullable: true, default: null)]
    private ?string $foto = null;

    #[Column(type: 'tinyint', length: 1, nullable: false, default: true)]
    private bool $isActive = true;

    // ------------------------------------------------------------------
    // Join-fields
    // ------------------------------------------------------------------

    #[Ignore]
    private ?UserEntity $user = null;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getNama(): string
    {
        return $this->nama;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getNoTelp(): ?string
    {
        return $this->noTelp;
    }

    public function getAlamat(): ?string
    {
        return $this->alamat;
    }

    public function getFoto(): ?string
    {
        return $this->foto;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getUser(): ?UserEntity
    {
        return $this->user;
    }

    // ------------------------------------------------------------------
    // Setters
    // ------------------------------------------------------------------

    public function setUserId(int $v): void
    {
        $this->userId = $v;
    }

    public function setNama(string $v): void
    {
        $this->nama = $v;
    }

    public function setEmail(?string $v): void
    {
        $this->email = $v;
    }

    public function setNoTelp(?string $v): void
    {
        $this->noTelp = $v;
    }

    public function setAlamat(?string $v): void
    {
        $this->alamat = $v;
    }

    public function setFoto(?string $v): void
    {
        $this->foto = $v;
    }

    public function setIsActive(bool $v): void
    {
        $this->isActive = $v;
    }

    public function setUser(?UserEntity $user): void
    {
        $this->user = $user;
    }

    // ------------------------------------------------------------------
    // Domain methods
    // ------------------------------------------------------------------

    public static function create(
        int $userId,
        string $nama,
        ?string $email = null,
        ?string $noTelp = null,
        ?string $alamat = null,
        ?int $createdBy = null
    ): self {
        $entity = new self();

        $entity->setUserId($userId);
        $entity->setNama($nama);
        $entity->setEmail($email);
        $entity->setNoTelp($noTelp);
        $entity->setAlamat($alamat);

        $entity->markCreated($createdBy);

        return $entity;
    }

    public function update(
        string $nama,
        ?string $email,
        ?string $noTelp,
        ?string $alamat,
        bool $isActive,
        ?int $updatedBy = null
    ): void {
        $this->setNama($nama);
        $this->setEmail($email);
        $this->setNoTelp($noTelp);
        $this->setAlamat($alamat);
        $this->setIsActive($isActive);

        $this->markUpdated($updatedBy);
    }
}
