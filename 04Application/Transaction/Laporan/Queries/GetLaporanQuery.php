<?php

declare(strict_types=1);

namespace Application\Transaction\Laporan\Queries;

use Client\Transaction\Laporan\LaporanService;

class GetLaporanQuery
{
    public function __construct(private LaporanService $service) {}
}
