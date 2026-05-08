<?php

declare(strict_types=1);

namespace Application\Master\Pimpinan\Queries;

use Client\Master\Pimpinan\PimpinanService;

class GetPimpinanByIdQuery
{
    public function __construct(private PimpinanService $service) {}

    public function execute(int $id): ?object
    {
        return $this->service->getById($id);
    }
}
