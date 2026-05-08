<?php

declare(strict_types=1);

namespace Shared\Master\Admin\Queries\GetByList;

class GetAdminByListRequest
{
    public function __construct(public readonly string $search = '') {}
}
