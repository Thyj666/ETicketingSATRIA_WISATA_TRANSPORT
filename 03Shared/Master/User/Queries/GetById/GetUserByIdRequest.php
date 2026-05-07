<?php

declare(strict_types=1);

namespace Shared\Master\User\Queries\GetById;

class GetUserByIdRequest
{
    public function __construct(public readonly int $id) {}
}
