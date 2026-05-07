<?php

declare(strict_types=1);

namespace Shared\Master\Jabatan\Commands\Delete;

class DeleteJabatanRequest
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $userId = null,
    ) {}
}
