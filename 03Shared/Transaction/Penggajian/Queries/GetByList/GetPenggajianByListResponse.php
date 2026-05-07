<?php

declare(strict_types=1);

namespace Shared\Transaction\Penggajian\Queries\GetByList;

class GetPenggajianByListResponse
{
    public function __construct(public readonly array $data) {}
}
