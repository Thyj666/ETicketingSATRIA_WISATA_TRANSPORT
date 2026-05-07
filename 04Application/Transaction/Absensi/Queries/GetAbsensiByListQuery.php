<?php

declare(strict_types=1);

namespace Application\Transaction\Absensi\Queries;

use Shared\Transaction\Absensi\Queries\GetByList\GetAbsensiByListRequest;
use Shared\Transaction\Absensi\Queries\GetByList\GetAbsensiByListResponse;
use Client\Transaction\Absensi\AbsensiService;

class GetAbsensiByListQuery
{
    public function __construct(private AbsensiService $service) {}
    public function execute(GetAbsensiByListRequest $req): GetAbsensiByListResponse
    {
        $filter = [];
        if ($req->userId)        $filter['user_id']        = $req->userId;
        if ($req->bulan)         $filter['bulan']           = $req->bulan;
        if ($req->status)        $filter['status']          = $req->status;
        if ($req->tanggalDari)   $filter['tanggal_dari']    = $req->tanggalDari;
        if ($req->tanggalSampai) $filter['tanggal_sampai']  = $req->tanggalSampai;
        return new GetAbsensiByListResponse($this->service->getAll($filter));
    }
}
