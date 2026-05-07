<?php

declare(strict_types=1);

namespace Application\Transaction\Penggajian\Queries;

use Shared\Transaction\Penggajian\Queries\GetByList\GetPenggajianByListRequest;
use Shared\Transaction\Penggajian\Queries\GetByList\GetPenggajianByListResponse;
use Client\Transaction\Penggajian\PenggajianService;

class GetPenggajianByListQuery
{
    public function __construct(private PenggajianService $service) {}
    public function execute(GetPenggajianByListRequest $req): GetPenggajianByListResponse
    {
        $filter = [];
        if ($req->userId)  $filter['user_id'] = $req->userId;
        if ($req->periode) $filter['periode']  = $req->periode;
        if ($req->status)  $filter['status']   = $req->status;
        return new GetPenggajianByListResponse($this->service->getAll($filter));
    }
}
