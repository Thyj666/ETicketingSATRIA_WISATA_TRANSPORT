<?php

declare(strict_types=1);

namespace Shared\Transaction\Tiket\Commands\Delete;

class DeleteTiketRequest
{
    public function __construct(
        public readonly int $id,
        public readonly ?int $actorId = null,
    ) {}
}
