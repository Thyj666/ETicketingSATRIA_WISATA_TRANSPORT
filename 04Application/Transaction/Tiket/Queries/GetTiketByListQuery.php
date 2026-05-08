<?php

declare(strict_types=1);

namespace Application\Transaction\Tiket\Queries;

use Shared\Transaction\Tiket\Queries\GetByList\GetTiketByListRequest;
use Shared\Transaction\Tiket\Queries\GetByList\GetTiketByListResponse;
use Client\Transaction\Tiket\TiketService;

class GetTiketByListQuery
{
    public function __construct(private TiketService $service) {}
    public function execute(GetTiketByListRequest $req): GetTiketByListResponse
    {
        $data = $this->service->getAll($req->armadaId, $req->search, $req->tujuan, $req->tanggalBerangkat);
        return new GetTiketByListResponse(true, '', $data);
    }
}
