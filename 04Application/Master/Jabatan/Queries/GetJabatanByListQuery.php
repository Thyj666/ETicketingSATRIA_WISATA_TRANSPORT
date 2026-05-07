<?php

declare(strict_types=1);

namespace Application\Master\Jabatan\Queries;

use Shared\Master\Jabatan\Queries\GetByList\GetJabatanByListRequest;
use Shared\Master\Jabatan\Queries\GetByList\GetJabatanByListResponse;
use Client\Master\Jabatan\JabatanService;

class GetJabatanByListQuery
{
    public function __construct(private JabatanService $service) {}
    public function execute(GetJabatanByListRequest $req): GetJabatanByListResponse
    {
        return new GetJabatanByListResponse($this->service->getAll($req->search, $req->jenis));
    }
}
