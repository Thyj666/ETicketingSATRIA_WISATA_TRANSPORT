<?php

declare(strict_types=1);

namespace Application\Master\Admin\Queries;

use Client\Master\Admin\AdminService;

class GetAdminByListQuery
{
    public function __construct(private AdminService $service) {}

    public function execute(string $search = ''): array
    {
        return $this->service->getAll($search);
    }
}
