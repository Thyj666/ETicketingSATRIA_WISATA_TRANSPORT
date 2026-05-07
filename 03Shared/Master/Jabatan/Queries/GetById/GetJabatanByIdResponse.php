<?php

declare(strict_types=1);

namespace Shared\Master\Jabatan\Queries\GetById;

use Domain\Entities\Master\Jabatan\JabatanEntity;

class GetJabatanByIdResponse
{
    public function __construct(
        public readonly ?JabatanEntity $data,
        public readonly bool $found,
    ) {}
}
