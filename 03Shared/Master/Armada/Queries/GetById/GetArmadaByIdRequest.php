<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Queries\GetById;

class GetArmadaByIdRequest
{
    public function __construct(public readonly int $id) {}
}
