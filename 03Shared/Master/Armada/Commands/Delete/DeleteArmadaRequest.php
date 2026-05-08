<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Commands\Delete;

class DeleteArmadaRequest
{
    public function __construct(
        public readonly int $id,
    ) {}
}
