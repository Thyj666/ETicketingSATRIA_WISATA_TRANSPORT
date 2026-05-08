<?php

declare(strict_types=1);

namespace Shared\Transaction\Tiket\Queries\GetByList;

class GetTiketByListResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message = '',
        public readonly array $data = [],
    ) {}
}
