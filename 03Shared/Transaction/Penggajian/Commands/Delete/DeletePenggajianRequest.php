<?php

declare(strict_types=1);

namespace Shared\Transaction\Penggajian\Commands\Delete;

class DeletePenggajianRequest
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $actorId = null,
    ) {}
}
