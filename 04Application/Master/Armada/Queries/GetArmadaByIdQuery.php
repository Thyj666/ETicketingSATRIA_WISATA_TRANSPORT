<?php

declare(strict_types=1);

namespace Application\Master\Armada\Queries;

use Shared\Master\Armada\Queries\GetById\GetArmadaByIdRequest;
use Shared\Master\Armada\Queries\GetById\GetArmadaByIdResponse;
use Client\Master\Armada\ArmadaService;

class GetArmadaByIdQuery
{
    public function __construct(private ArmadaService $service) {}
    public function execute(GetArmadaByIdRequest $req): GetArmadaByIdResponse
    {
        $data = $this->service->getById($req->id);
        return new GetArmadaByIdResponse(true, '', $data);
    }
}
