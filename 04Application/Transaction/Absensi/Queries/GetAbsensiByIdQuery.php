<?php

declare(strict_types=1);

namespace Application\Transaction\Absensi\Queries;

use Shared\Transaction\Absensi\Queries\GetById\GetAbsensiByIdRequest;
use Shared\Transaction\Absensi\Queries\GetById\GetAbsensiByIdResponse;
use Client\Transaction\Absensi\AbsensiService;

class GetAbsensiByIdQuery
{
    public function __construct(private AbsensiService $service) {}
    public function execute(GetAbsensiByIdRequest $req): GetAbsensiByIdResponse
    {
        $data = $this->service->getById($req->id);
        return new GetAbsensiByIdResponse($data, $data !== null);
    }
}
