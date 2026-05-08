<?php

declare(strict_types=1);

namespace Shared\Master\Pimpinan\Commands\Update;

class UpdatePimpinanResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
