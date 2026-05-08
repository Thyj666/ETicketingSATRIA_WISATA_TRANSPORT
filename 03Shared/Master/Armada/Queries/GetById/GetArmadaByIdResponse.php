<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Queries\GetById;

use Domain\Entities\Master\Armada\ArmadaEntity;

class GetArmadaByIdResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message = '',
        public readonly ?ArmadaEntity $data = null,
    ) {}
}
