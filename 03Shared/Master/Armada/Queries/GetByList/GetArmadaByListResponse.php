<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Queries\GetByList;

class GetArmadaByListResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message = '',
        public readonly array $data = [],
    ) {}
}
