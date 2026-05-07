<?php

declare(strict_types=1);

namespace Shared\Master\Jabatan\Commands\Create;

class CreateJabatanResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
