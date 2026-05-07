<?php

declare(strict_types=1);

namespace Domain\Abstracts;

abstract class Entity
{
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function isNew(): bool
    {
        return $this->id === null;
    }
}
