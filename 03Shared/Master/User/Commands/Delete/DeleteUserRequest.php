<?php

declare(strict_types=1);

namespace Shared\Master\User\Commands\Delete;

class DeleteUserRequest
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $userId = null,
    ) {}
}
