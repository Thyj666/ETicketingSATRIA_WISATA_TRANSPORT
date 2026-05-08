<?php

declare(strict_types=1);

namespace Shared\Transaction\Pemesanan\Commands\Create;

class CreatePemesananRequest
{
    public function __construct(
        public readonly int $armadaId,
        public readonly int $userId,
        public readonly string $noPemesanan,
        public readonly string $noSeat,
        public readonly ?string $tanggalPemesanan = null,
        public readonly ?string $jamPemesanan = null,
        public readonly ?string $statusPemesanan = null,
    ) {}
}
