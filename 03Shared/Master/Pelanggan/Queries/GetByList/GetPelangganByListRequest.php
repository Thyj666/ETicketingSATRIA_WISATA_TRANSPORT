<?php

declare(strict_types=1);

namespace Shared\Master\Pelanggan\Queries\GetByList;

class GetPelangganByListRequest
{
    public function __construct(public readonly string $search = '') {}
}
