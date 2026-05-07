<?php

declare(strict_types=1);

namespace Shared\Master\Jabatan\Queries\GetById;

class GetJabatanByIdRequest
{
    public function __construct(public readonly int $id) {}
}
