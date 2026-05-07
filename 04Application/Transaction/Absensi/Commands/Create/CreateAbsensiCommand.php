<?php

declare(strict_types=1);

namespace Application\Transaction\Absensi\Commands\Create;

use Shared\Transaction\Absensi\Commands\Create\CreateAbsensiRequest;
use Shared\Transaction\Absensi\Commands\Create\CreateAbsensiResponse;
use Client\Transaction\Absensi\AbsensiService;
use Domain\Entities\Transaction\Absensi\AbsensiEntity;

class CreateAbsensiCommand
{
    public function __construct(private AbsensiService $service) {}
    public function execute(CreateAbsensiRequest $req): CreateAbsensiResponse
    {
        if ($this->service->existsByUserTanggal($req->userId, $req->tanggal)) {
            return new CreateAbsensiResponse(false, 'Absensi untuk tanggal ini sudah ada.');
        }
        $entity = AbsensiEntity::create(
            $req->userId,
            $req->tanggal,
            $req->status,
            $req->jamMasuk ?? '',
            $req->jamKeluar ?? '',
            $req->keterangan ?? '',
            $req->potonganGaji,
            $req->actorId
        );
        $id = $this->service->save($entity);
        return new CreateAbsensiResponse(true, 'Absensi berhasil dicatat.', $id);
    }
}
