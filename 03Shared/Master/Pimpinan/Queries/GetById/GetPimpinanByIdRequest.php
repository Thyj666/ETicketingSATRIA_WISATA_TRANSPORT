<?php

declare(strict_types=1);

namespace Shared\Master\Pimpinan\Queries\GetById;

class GetPimpinanByIdRequest
{
    public function __construct(public readonly int $id) {}
}
