<?php

declare(strict_types=1);

namespace Application\Master\Pelanggan\Queries;

use Client\Master\Pelanggan\PelangganService;

class GetPelangganByListQuery
{
    public function __construct(private PelangganService $service) {}

    public function execute(string $search = ''): array
    {
        return $this->service->getAll($search);
    }
}
