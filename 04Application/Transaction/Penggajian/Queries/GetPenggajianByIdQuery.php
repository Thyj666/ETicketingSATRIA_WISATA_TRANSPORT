<?php

declare(strict_types=1);

namespace Application\Transaction\Penggajian\Queries;

use Shared\Transaction\Penggajian\Queries\GetById\GetPenggajianByIdRequest;
use Shared\Transaction\Penggajian\Queries\GetById\GetPenggajianByIdResponse;
use Client\Transaction\Penggajian\PenggajianService;

class GetPenggajianByIdQuery
{
    public function __construct(private PenggajianService $service) {}
    public function execute(GetPenggajianByIdRequest $req): GetPenggajianByIdResponse
    {
        $data = $this->service->getById($req->id);
        return new GetPenggajianByIdResponse($data, $data !== null);
    }
}
