<?php

declare(strict_types=1);

namespace Application\Master\Armada\Commands\Update;

use Shared\Master\Armada\Commands\Update\UpdateArmadaRequest;
use Shared\Master\Armada\Commands\Update\UpdateArmadaResponse;
use Client\Master\Armada\ArmadaService;

class UpdateArmadaCommand
{
    public function __construct(private ArmadaService $service) {}
    public function execute(UpdateArmadaRequest $req, int $actorId): UpdateArmadaResponse
    {
        if ($this->service->existsByPlatNomor($req->platNomor, $req->id)) {
            return new UpdateArmadaResponse(false, 'Plat nomor sudah digunakan armada lain.');
        }
        $entity = $this->service->getById($req->id);
        if (!$entity) return new UpdateArmadaResponse(false, 'Armada tidak ditemukan.');
        $entity->update($req->platNomor, $req->namaArmada, $req->tipeSeat, $req->jumlahSeat, $req->status, $actorId);
        $this->service->update($entity);
        return new UpdateArmadaResponse(true, 'Armada berhasil diperbarui.');
    }
}
