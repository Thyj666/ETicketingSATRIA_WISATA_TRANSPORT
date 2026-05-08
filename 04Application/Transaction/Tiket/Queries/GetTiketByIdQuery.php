<?php

declare(strict_types=1);

namespace Application\Transaction\Tiket\Queries;

use Shared\Transaction\Tiket\Queries\GetById\GetTiketByIdRequest;
use Shared\Transaction\Tiket\Queries\GetById\GetTiketByIdResponse;
use Client\Transaction\Tiket\TiketService;

class GetTiketByIdQuery
{
    public function __construct(private TiketService $service) {}
    public function execute(GetTiketByIdRequest $req): GetTiketByIdResponse
    {
        $data = $this->service->getById($req->id);
        return new GetTiketByIdResponse(true, '', $data);
    }
}
