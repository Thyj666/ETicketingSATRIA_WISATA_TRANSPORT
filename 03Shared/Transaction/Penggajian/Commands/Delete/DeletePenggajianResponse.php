<?php

declare(strict_types=1);

namespace Shared\Transaction\Penggajian\Commands\Delete;

class DeletePenggajianResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
