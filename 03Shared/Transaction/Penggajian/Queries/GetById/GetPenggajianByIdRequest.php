<?php

declare(strict_types=1);

namespace Shared\Transaction\Penggajian\Queries\GetById;

class GetPenggajianByIdRequest
{
    public function __construct(public readonly int $id) {}
}
