<?php

declare(strict_types=1);

namespace Shared\Transaction\Pemesanan\Commands\Create;

class CreatePemesananResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message = '',
        public readonly int $id = 0,
        public readonly string $orderId = '',
        public readonly float $totalHarga = 0,
    ) {}
}
