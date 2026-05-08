<?php

declare(strict_types=1);

namespace Shared\Transaction\Tiket\Commands\Create;

class CreateTiketRequest
{
    public function __construct(
        public readonly int $armadaId,
        public readonly string $tujuan,
        public readonly ?string $tanggalBerangkat = null,
        public readonly ?string $jamBerangkat = null,
        public readonly ?float $harga = null,
        public readonly bool $isFull = true,
    ) {}
}
