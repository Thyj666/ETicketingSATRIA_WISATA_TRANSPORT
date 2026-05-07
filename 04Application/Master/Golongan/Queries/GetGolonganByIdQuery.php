<?php

declare(strict_types=1);

namespace Application\Master\Golongan\Queries;

use Shared\Master\Golongan\Queries\GetById\GetGolonganByIdRequest;
use Shared\Master\Golongan\Queries\GetById\GetGolonganByIdResponse;
use Client\Master\Golongan\GolonganService;

class GetGolonganByIdQuery
{
    public function __construct(private GolonganService $service) {}
    public function execute(GetGolonganByIdRequest $req): GetGolonganByIdResponse
    {
        $data = $this->service->getById($req->id);
        return new GetGolonganByIdResponse($data, $data !== null);
    }
}
