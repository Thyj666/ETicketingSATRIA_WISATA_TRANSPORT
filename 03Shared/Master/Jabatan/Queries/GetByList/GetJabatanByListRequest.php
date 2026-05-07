<?php

declare(strict_types=1);

namespace Shared\Master\Jabatan\Queries\GetByList;

class GetJabatanByListRequest
{
    public function __construct(public readonly string $search = '', public readonly string $jenis = '') {}
}
