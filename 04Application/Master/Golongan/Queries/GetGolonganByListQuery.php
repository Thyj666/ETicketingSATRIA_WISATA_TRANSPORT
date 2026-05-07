<?php

declare(strict_types=1);

namespace Application\Master\Golongan\Queries;

use Shared\Master\Golongan\Queries\GetByList\GetGolonganByListRequest;
use Shared\Master\Golongan\Queries\GetByList\GetGolonganByListResponse;
use Client\Master\Golongan\GolonganService;

class GetGolonganByListQuery
{
    public function __construct(private GolonganService $service) {}
    public function execute(GetGolonganByListRequest $req): GetGolonganByListResponse
    {
        return new GetGolonganByListResponse($this->service->getAll($req->search));
    }
}
