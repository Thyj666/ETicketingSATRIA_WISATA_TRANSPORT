<?php

declare(strict_types=1);

namespace Shared\Transaction\Pemesanan\Queries\GetByList;

class GetPemesananByListResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message = '',
        public readonly array $data = [],
    ) {}
}
