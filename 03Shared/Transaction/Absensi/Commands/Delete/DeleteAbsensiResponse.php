<?php

declare(strict_types=1);

namespace Shared\Transaction\Absensi\Commands\Delete;

class DeleteAbsensiResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
