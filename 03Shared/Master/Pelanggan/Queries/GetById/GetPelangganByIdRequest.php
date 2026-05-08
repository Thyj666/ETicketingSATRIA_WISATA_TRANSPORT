<?php

declare(strict_types=1);

namespace Shared\Master\Pelanggan\Queries\GetById;

class GetPelangganByIdRequest
{
    public function __construct(public readonly int $id) {}
}
