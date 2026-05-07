<?php

declare(strict_types=1);

namespace Shared\Transaction\Penggajian\Queries\GetByList;

class GetPenggajianByListRequest
{
    public function __construct(
        public readonly int    $userId = 0,
        public readonly string $periode = '',
        public readonly string $status = '',
    ) {}
}
