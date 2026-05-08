<?php

declare(strict_types=1);

namespace Shared\Master\Pimpinan\Queries\GetById;

use Domain\Entities\Master\Pimpinan\PimpinanEntity;

class GetPimpinanByIdResponse
{
    public function __construct(
        public readonly ?PimpinanEntity $data,
        public readonly bool $found,
    ) {}
}
