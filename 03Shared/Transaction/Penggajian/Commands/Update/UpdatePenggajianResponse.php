<?php

declare(strict_types=1);

namespace Shared\Transaction\Penggajian\Commands\Update;

class UpdatePenggajianResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
