<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Queries\GetById;

use Domain\Entities\Master\Armada\ArmadaEntity;

class GetArmadaByIdResponse
{
    public function __construct(public readonly ?ArmadaEntity $data, public readonly bool $found) {}
}
