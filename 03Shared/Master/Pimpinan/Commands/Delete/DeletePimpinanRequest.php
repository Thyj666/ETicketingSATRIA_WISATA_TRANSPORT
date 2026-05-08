<?php

declare(strict_types=1);

namespace Shared\Master\Pimpinan\Commands\Delete;

class DeletePimpinanRequest
{
    public function __construct(
        public readonly int $id,
    ) {}
}
