<?php

declare(strict_types=1);

namespace Shared\Transaction\Pemesanan\Commands\Delete;

class DeletePemesananResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message = '',
    ) {}
}
