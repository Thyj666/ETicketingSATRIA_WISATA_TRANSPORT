<?php

declare(strict_types=1);

namespace Shared\Master\User\Queries\GetById;

use Domain\Entities\Master\User\UserEntity;

class GetUserByIdResponse
{
    public function __construct(public readonly ?UserEntity $data, public readonly bool $found) {}
}
