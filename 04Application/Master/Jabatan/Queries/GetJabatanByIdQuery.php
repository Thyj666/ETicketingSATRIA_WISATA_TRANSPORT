<?php

declare(strict_types=1);

namespace Application\Master\Jabatan\Queries;

use Shared\Master\Jabatan\Queries\GetById\GetJabatanByIdRequest;
use Shared\Master\Jabatan\Queries\GetById\GetJabatanByIdResponse;
use Client\Master\Jabatan\JabatanService;

class GetJabatanByIdQuery
{
    public function __construct(private JabatanService $service) {}
    public function execute(GetJabatanByIdRequest $req): GetJabatanByIdResponse
    {
        $data = $this->service->getById($req->id);
        return new GetJabatanByIdResponse($data, $data !== null);
    }
}
