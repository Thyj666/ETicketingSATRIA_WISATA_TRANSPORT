<?php

declare(strict_types=1);

namespace Shared\Transaction\Tiket\Commands\Create;

class CreateTiketResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
