<?php

declare(strict_types=1);

namespace Shared\Transaction\Tiket\Queries\GetByList;

class GetTiketByListRequest
{
    public function __construct(
        public readonly int    $armadaId = 0,
        public readonly string $search = '',
        public readonly string $tujuan = '',
        public readonly string $tanggalBerangkat = '',
        public readonly string $jamBerangkat = '',
        public readonly bool $isFull = true,
        public readonly float $harga = 0,
    ) {}
}
