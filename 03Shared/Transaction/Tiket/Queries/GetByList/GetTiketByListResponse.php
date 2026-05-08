<?php

declare(strict_types=1);

namespace Shared\Transaction\Tiket\Queries\GetByList;

class GetTiketByListResponse
{
    public function __construct(public readonly array $data) {}
}
