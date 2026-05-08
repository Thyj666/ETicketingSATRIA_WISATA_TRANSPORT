<?php

declare(strict_types=1);

namespace Shared\Master\Pelanggan\Queries\GetById;

use Domain\Entities\Master\Pelanggan\PelangganEntity;

class GetPelangganByIdResponse
{
    public function __construct(
        public readonly ?PelangganEntity $data,
        public readonly bool $found,
    ) {}
}
