<?php

declare(strict_types=1);

namespace Application\Master\Armada\Queries;

use Shared\Master\Armada\Queries\GetByList\GetArmadaByListRequest;
use Shared\Master\Armada\Queries\GetByList\GetArmadaByListResponse;
use Client\Master\Armada\ArmadaService;

class GetArmadaByListQuery
{
    public function __construct(private ArmadaService $service) {}
    public function execute(GetArmadaByListRequest $req): GetArmadaByListResponse
    {
        $data = $this->service->getAll($req->search, $req->status);
        return new GetArmadaByListResponse(true, '', $data);
    }
}
