<?php

declare(strict_types=1);

namespace Shared\Master\Admin\Queries\GetById;

class GetAdminByIdRequest
{
    public function __construct(public readonly int $id) {}
}
