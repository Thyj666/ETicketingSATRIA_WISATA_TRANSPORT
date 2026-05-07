<?php

declare(strict_types=1);

namespace Shared\Master\User\Queries\GetByList;

class GetUserByListRequest
{
    public function __construct(public readonly string $search = '', public readonly string $role = '') {}
}
