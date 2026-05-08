<?php

declare(strict_types=1);

namespace Shared\Master\Pimpinan\Queries\GetByList;

class GetPimpinanByListRequest
{
    public function __construct(public readonly string $search = '') {}
}
