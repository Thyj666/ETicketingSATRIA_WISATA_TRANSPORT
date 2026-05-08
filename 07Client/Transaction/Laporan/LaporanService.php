<?php

declare(strict_types=1);

namespace Client\Transaction\Laporan;

use Infrastructure\AppDbContext;

class LaporanService
{
    public function __construct(private AppDbContext $db) {}
}
