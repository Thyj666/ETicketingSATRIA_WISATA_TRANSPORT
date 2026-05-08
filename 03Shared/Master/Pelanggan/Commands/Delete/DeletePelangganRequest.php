<?php

declare(strict_types=1);

namespace Shared\Master\Pelanggan\Commands\Delete;

class DeletePelangganRequest
{
    public function __construct(
        public readonly int $id,
    ) {}
}
