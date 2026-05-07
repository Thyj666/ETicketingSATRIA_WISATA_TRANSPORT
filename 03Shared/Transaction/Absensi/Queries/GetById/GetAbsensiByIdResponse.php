<?php

declare(strict_types=1);

namespace Shared\Transaction\Absensi\Queries\GetById;

use Domain\Entities\Transaction\Absensi\AbsensiEntity;

class GetAbsensiByIdResponse
{
    public function __construct(public readonly ?AbsensiEntity $data, public readonly bool $found) {}
}
