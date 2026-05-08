<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Queries\GetByList;

class GetArmadaByListRequest
{
    public function __construct(public readonly string $search = '', public readonly string $tipeSeat = '', public readonly string $status = '') {}
}
