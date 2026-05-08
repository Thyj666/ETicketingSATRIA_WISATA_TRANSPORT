<?php

declare(strict_types=1);

namespace Shared\Transaction\Pemesanan\Queries\GetByList;

class GetPemesananByListRequest
{
    public function __construct(
        public readonly int    $userId = 0,
        public readonly int    $armadaId = 0,
        public readonly string $noPemesanan = '',
        public readonly string $status = '',
    ) {}
}
