<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Queries\GetByList;

class GetArmadaByListResponse
{
    public function __construct(public readonly array $data) {}
}
