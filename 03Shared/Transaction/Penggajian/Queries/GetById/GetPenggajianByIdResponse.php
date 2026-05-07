<?php

declare(strict_types=1);

namespace Shared\Transaction\Penggajian\Queries\GetById;

use Domain\Entities\Transaction\Penggajian\PenggajianEntity;

class GetPenggajianByIdResponse
{
    public function __construct(public readonly ?PenggajianEntity $data, public readonly bool $found) {}
}
