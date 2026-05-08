<?php

declare(strict_types=1);

namespace Application\Master\Pelanggan\Queries;

use Client\Master\Pelanggan\PelangganService;

class GetPelangganByIdQuery
{
    public function __construct(private PelangganService $service) {}

    public function execute(int $id): ?object
    {
        return $this->service->getById($id);
    }
}
