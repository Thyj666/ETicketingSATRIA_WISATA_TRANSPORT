<?php

declare(strict_types=1);

namespace Shared\Transaction\Absensi\Queries\GetByList;

class GetAbsensiByListRequest
{
    public function __construct(
        public readonly int    $userId = 0,
        public readonly string $bulan = '',
        public readonly string $status = '',
        public readonly string $tanggalDari = '',
        public readonly string $tanggalSampai = '',
    ) {}
}
