<?php

declare(strict_types=1);

namespace Shared\Master\Admin\Queries\GetById;

use Domain\Entities\Master\Admin\AdminEntity;

class GetAdminByIdResponse
{
    public function __construct(
        public readonly ?AdminEntity $data,
        public readonly bool $found,
    ) {}
}
