<?php

declare(strict_types=1);

namespace Shared\Transaction\Absensi\Queries\GetByList;

class GetAbsensiByListResponse
{
    public function __construct(public readonly array $data) {}
}
