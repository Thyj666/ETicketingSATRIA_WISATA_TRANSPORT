<?php

declare(strict_types=1);

namespace Application\Transaction\Pemesanan\Queries;

use Shared\Transaction\Pemesanan\Queries\GetById\GetPemesananByIdRequest;
use Shared\Transaction\Pemesanan\Queries\GetById\GetPemesananByIdResponse;
use Client\Transaction\Pemesanan\PemesananService;

class GetPemesananByIdQuery
{
    public function __construct(private PemesananService $service) {}
    public function execute(GetPemesananByIdRequest $req): GetPemesananByIdResponse
    {
        $data = $this->service->getById($req->id);
        return new GetPemesananByIdResponse(true, '', $data);
    }
}
