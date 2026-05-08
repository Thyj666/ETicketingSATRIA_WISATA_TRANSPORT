<?php

declare(strict_types=1);

namespace Shared\Master\User\Commands\Update;

class UpdateUserRequest
{
    public function __construct(
        public readonly int $id,
        public readonly string $role = 'penumapang',
        public readonly bool $isActive = true,
        public readonly string $password = '',
        public readonly ?int $userId = null,
    ) {}
}
