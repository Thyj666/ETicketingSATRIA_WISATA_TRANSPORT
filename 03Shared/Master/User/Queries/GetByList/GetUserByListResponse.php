<?php

declare(strict_types=1);

namespace Shared\Master\User\Queries\GetByList;

class GetUserByListResponse
{
    public function __construct(public readonly array $data) {}
}
