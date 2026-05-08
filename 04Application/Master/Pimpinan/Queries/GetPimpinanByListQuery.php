<?php

declare(strict_types=1);

namespace Application\Master\Pimpinan\Queries;

use Client\Master\Pimpinan\PimpinanService;

class GetPimpinanByListQuery
{
    public function __construct(private PimpinanService $service) {}

    public function execute(string $search = ''): array
    {
        return $this->service->getAll($search);
    }
}
