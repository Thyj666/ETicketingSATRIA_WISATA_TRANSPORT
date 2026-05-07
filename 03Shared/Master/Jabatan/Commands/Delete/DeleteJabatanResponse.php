<?php

declare(strict_types=1);

namespace Shared\Master\Jabatan\Commands\Delete;

class DeleteJabatanResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
