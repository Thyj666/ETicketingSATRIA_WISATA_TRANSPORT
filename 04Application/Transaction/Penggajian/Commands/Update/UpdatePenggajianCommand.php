<?php

declare(strict_types=1);

namespace Application\Transaction\Penggajian\Commands\Update;

use Shared\Transaction\Penggajian\Commands\Update\UpdatePenggajianRequest;
use Shared\Transaction\Penggajian\Commands\Update\UpdatePenggajianResponse;
use Client\Transaction\Penggajian\PenggajianService;

class UpdatePenggajianCommand
{
    public function __construct(private PenggajianService $service) {}
    public function execute(UpdatePenggajianRequest $req): UpdatePenggajianResponse
    {
        $entity = $this->service->getById($req->id);
        if (!$entity) return new UpdatePenggajianResponse(false, 'Data penggajian tidak ditemukan.');
        $total = $entity->getGajiPokok() + $req->tunjangan - $req->potonganAbsensi - $req->potonganLain;
        $entity->update($req->tunjangan, $req->potonganAbsensi, $req->potonganLain, $total, $req->status, $req->keterangan, $req->tanggalBayar, $req->actorId);
        $this->service->update($entity);
        return new UpdatePenggajianResponse(true, 'Penggajian berhasil diperbarui.');
    }
}
