<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Commands\Delete;

class DeleteArmadaResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message = '',
    ) {}
}
