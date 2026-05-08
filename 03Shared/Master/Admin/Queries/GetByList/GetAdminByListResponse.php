<?php

declare(strict_types=1);

namespace Shared\Master\Admin\Queries\GetByList;

class GetAdminByListResponse
{
    public function __construct(public readonly array $data) {}
}
