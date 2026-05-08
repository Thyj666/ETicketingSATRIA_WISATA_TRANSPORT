<?php

declare(strict_types=1);

namespace Shared\Transaction\Tiket\Queries\GetById;

use Domain\Entities\Transaction\Tiket\TiketEntity;

class GetTiketByIdResponse
{
    public function __construct(public readonly ?TiketEntity $data, public readonly bool $found) {}
}
