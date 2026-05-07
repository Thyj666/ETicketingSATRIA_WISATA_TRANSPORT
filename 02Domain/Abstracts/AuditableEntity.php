<?php

declare(strict_types=1);

namespace Domain\Abstracts;

abstract class AuditableEntity extends Entity
{
    protected ?string $createdAt  = null;
    protected ?string $updatedAt  = null;
    protected ?int    $createdBy  = null;
    protected ?int    $updatedBy  = null;
    protected bool    $isDeleted  = false;

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }
    public function getCreatedBy(): ?int
    {
        return $this->createdBy;
    }
    public function getUpdatedBy(): ?int
    {
        return $this->updatedBy;
    }
    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setCreatedAt(string $v): void
    {
        $this->createdAt = $v;
    }
    public function setUpdatedAt(string $v): void
    {
        $this->updatedAt = $v;
    }
    public function setCreatedBy(int $v): void
    {
        $this->createdBy = $v;
    }
    public function setUpdatedBy(int $v): void
    {
        $this->updatedBy = $v;
    }
    public function setIsDeleted(bool $v): void
    {
        $this->isDeleted = $v;
    }

    public function markCreated(?int $userId = null): void
    {
        $this->createdAt = date('Y-m-d H:i:s');
        $this->updatedAt = date('Y-m-d H:i:s');
        if ($userId) {
            $this->createdBy = $userId;
            $this->updatedBy = $userId;
        }
    }

    public function markUpdated(?int $userId = null): void
    {
        $this->updatedAt = date('Y-m-d H:i:s');
        if ($userId) $this->updatedBy = $userId;
    }

    public function softDelete(?int $userId = null): void
    {
        $this->isDeleted = true;
        $this->markUpdated($userId);
    }
}
