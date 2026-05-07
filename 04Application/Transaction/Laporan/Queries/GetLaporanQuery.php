<?php

declare(strict_types=1);

namespace Application\Transaction\Laporan\Queries;

use Client\Transaction\Laporan\LaporanService;

class GetLaporanQuery
{
    public function __construct(private LaporanService $service) {}
    public function getLaporanGaji(string $periode): array
    {
        return $this->service->getLaporanGaji($periode);
    }
    public function getLaporanAbsensi(string $periode): array
    {
        return $this->service->getLaporanAbsensi($periode);
    }
    public function getSummaryDashboard(): array
    {
        return $this->service->getSummaryDashboard();
    }
}
