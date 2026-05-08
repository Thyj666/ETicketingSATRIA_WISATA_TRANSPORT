<?php

declare(strict_types=1);

namespace Shared\Master\Pelanggan\Queries\GetByList;

class GetPelangganByListResponse
{
    public function __construct(public readonly array $data) {}
}
