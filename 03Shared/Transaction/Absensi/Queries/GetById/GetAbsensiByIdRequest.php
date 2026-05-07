<?php

declare(strict_types=1);

namespace Shared\Transaction\Absensi\Queries\GetById;

class GetAbsensiByIdRequest
{
    public function __construct(public readonly int $id) {}
}
