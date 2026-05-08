<?php

declare(strict_types=1);

namespace Shared\Transaction\Pemesanan\Queries\GetById;

class GetPemesananByIdRequest
{
    public function __construct(public readonly int $id) {}
}
