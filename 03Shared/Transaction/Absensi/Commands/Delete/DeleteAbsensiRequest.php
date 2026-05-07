<?php

declare(strict_types=1);

namespace Shared\Transaction\Absensi\Commands\Delete;

class DeleteAbsensiRequest
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $actorId = null,
    ) {}
}
