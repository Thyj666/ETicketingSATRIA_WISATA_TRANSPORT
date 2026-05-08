<?php

declare(strict_types=1);

namespace Shared\Transaction\Pemesanan\Queries\GetById;

use Domain\Entities\Transaction\Pemesanan\PemesananEntity;

class GetPemesananByIdResponse
{
    public function __construct(public readonly ?PemesananEntity $data, public readonly bool $found) {}
}
