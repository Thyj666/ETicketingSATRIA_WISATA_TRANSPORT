<?php

declare(strict_types=1);

namespace Shared\Transaction\Tiket\Queries\GetById;

class GetTiketByIdRequest
{
    public function __construct(public readonly int $id) {}
}
