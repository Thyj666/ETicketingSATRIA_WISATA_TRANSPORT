<?php

declare(strict_types=1);

namespace Application\Master\Armada\Commands\Create;

use Shared\Master\Armada\Commands\Create\createArmadaRequest;
use Shared\Master\Armada\Commands\Create\CreateArmadaResponse;
use Client\Master\Armada\ArmadaService;
use Domain\Entities\Master\Armada\ArmadaEntity;

class CreateArmadaCommand
{
    public function __construct(private ArmadaService $service) {}
    public function execute(createArmadaRequest $req, int $actorId): CreateArmadaResponse
    {
        if ($this->service->existsByPlatNomor($req->platNomor)) {
            return new CreateArmadaResponse(false, 'Plat nomor sudah terdaftar.');
        }
        $entity = ArmadaEntity::create($req->platNomor, $req->namaArmada, $req->tipeSeat, $req->jumlahSeat, $req->status, $actorId);
        $id = $this->service->save($entity);
        return new CreateArmadaResponse(true, 'Armada berhasil ditambahkan.', $id);
    }
}
