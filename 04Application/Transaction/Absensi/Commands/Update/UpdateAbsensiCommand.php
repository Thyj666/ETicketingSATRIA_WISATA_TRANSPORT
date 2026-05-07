<?php

declare(strict_types=1);

namespace Application\Transaction\Absensi\Commands\Update;

use Shared\Transaction\Absensi\Commands\Update\UpdateAbsensiRequest;
use Shared\Transaction\Absensi\Commands\Update\UpdateAbsensiResponse;
use Client\Transaction\Absensi\AbsensiService;

class UpdateAbsensiCommand
{
    public function __construct(private AbsensiService $service) {}

    public function execute(UpdateAbsensiRequest $req): UpdateAbsensiResponse
    {
        $entity = $this->service->getById($req->id);
        if (!$entity) {
            return new UpdateAbsensiResponse(false, 'Absensi tidak ditemukan.');
        }

        $entity->update(
            $req->jamMasuk ?? '',
            $req->jamKeluar ?? '',
            $req->status,
            $req->keterangan ?? '',
            $req->potonganGaji,
            $req->actorId
        );

        $this->service->save($entity);
        return new UpdateAbsensiResponse(true, 'Absensi berhasil diperbarui.');
    }
}
