<?php

declare(strict_types=1);

namespace Domain\Entities\Master\User;

use Domain\Abstracts\AuditableEntity;
use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\Table;
use Base\User\Enums\Role;

#[Table(
    name: 'users',
    uniques: ['username'],
    indexes: ['username', 'role'],
)]
class UserEntity extends AuditableEntity
{
    // ------------------------------------------------------------------
    // Columns
    // ------------------------------------------------------------------

    // BUG FIX: Tambah field 'nama' — dibutuhkan oleh AuthController::login()
    // yang memanggil $user->getNama() untuk JWT payload, dan oleh INSERT register.
    #[Column(type: 'varchar', length: 120, nullable: true)]
    private ?string $nama = null;

    #[Column(type: 'varchar', length: 60, nullable: false)]
    private string $username;

    #[Column(type: 'varchar', length: 255, nullable: false)]
    private string $password;

    #[Column(type: 'enum', nullable: false, enumClass: Role::class)]
    private string $role;

    #[Column(type: 'tinyint', length: 1, nullable: false, default: true)]
    private bool $isActive = true;

    // ------------------------------------------------------------------
    // Getters
    // ------------------------------------------------------------------

    public function getNama(): ?string
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

    public function getRole(): string
    {
        return $this->role;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    // ------------------------------------------------------------------
    // Setters
    // ------------------------------------------------------------------

    public function setNama(?string $v): void
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

    public function setRole(string $v): void
    {
        $this->role = $v;
    }

    public function setIsActive(bool $v): void
    {
        $this->isActive = $v;
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
        string $username,
        string $plainPassword,
        string $role,
        ?string $nama = null,
        ?int $createdBy = null
    ): self {
        $entity = new self();

        $entity->setNama($nama);
        $entity->setUsername($username);
        $entity->hashPassword($plainPassword);
        $entity->setRole($role);
        $entity->markCreated($createdBy);

        return $entity;
    }

    public function update(
        string $role,
        bool $isActive,
        string $plainPassword = '',
        ?int $updatedBy = null
    ): void {
        $this->setRole($role);
        $this->setIsActive($isActive);

        if (!empty($plainPassword)) {
            $this->hashPassword($plainPassword);
        }

        $this->markUpdated($updatedBy);
    }
}
