<?php

declare(strict_types=1);

namespace Application\Transaction\Tiket\Commands\Create;

use Shared\Transaction\Tiket\Commands\Create\CreateTiketRequest;
use Shared\Transaction\Tiket\Commands\Create\CreateTiketResponse;
use Client\Transaction\Tiket\TiketService;
use Domain\Entities\Transaction\Tiket\TiketEntity;

class CreateTiketCommand
{
    public function __construct(private TiketService $service) {}
    public function execute(CreateTiketRequest $req, int $actorId): CreateTiketResponse
    {
        $entity = TiketEntity::create($req->armadaId, $req->tujuan, $req->tanggalBerangkat, $req->jamBerangkat, $req->harga, $actorId);
        $id = $this->service->save($entity);
        return new CreateTiketResponse(true, 'Tiket berhasil dibuat.', $id);
    }
}
