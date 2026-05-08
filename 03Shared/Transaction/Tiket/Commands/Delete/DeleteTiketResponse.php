<?php

declare(strict_types=1);

namespace Shared\Transaction\Tiket\Commands\Delete;

class DeleteTiketResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
