<?php

declare(strict_types=1);

namespace Shared\Transaction\Pemesanan\Commands\Delete;

class DeletePemesananRequest
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $actorId = null,
    ) {}
}
