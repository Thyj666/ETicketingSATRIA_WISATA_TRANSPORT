<?php

declare(strict_types=1);

namespace Application\Transaction\Pemesanan\Queries;

use Shared\Transaction\Pemesanan\Queries\GetByList\GetPemesananByListRequest;
use Shared\Transaction\Pemesanan\Queries\GetByList\GetPemesananByListResponse;
use Client\Transaction\Pemesanan\PemesananService;

class GetPemesananByListQuery
{
    public function __construct(private PemesananService $service) {}
    public function execute(GetPemesananByListRequest $req): GetPemesananByListResponse
    {
        $data = $this->service->getAll($req->userId ?? 0, $req->tiketId ?? 0, $req->status ?? '');
        return new GetPemesananByListResponse(true, '', $data);
    }
}
