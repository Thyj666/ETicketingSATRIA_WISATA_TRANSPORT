<?php

declare(strict_types=1);

namespace Shared\Transaction\Pemesanan\Commands\Create;

class CreatePemesananRequest
{
    public function __construct(
        public readonly int $userId,
        public readonly int $tiketId,
        public readonly string $noSeat,
    ) {}
}
