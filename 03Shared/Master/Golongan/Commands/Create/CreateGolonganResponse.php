<?php

declare(strict_types=1);

namespace Shared\Master\Golongan\Commands\Create;

class CreateGolonganResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
