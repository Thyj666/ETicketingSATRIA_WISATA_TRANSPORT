<?php

declare(strict_types=1);

namespace Application\Master\Admin\Queries;

use Client\Master\Admin\AdminService;

class GetAdminByIdQuery
{
    public function __construct(private AdminService $service) {}

    public function execute(int $id): ?object
    {
        return $this->service->getById($id);
    }
}
