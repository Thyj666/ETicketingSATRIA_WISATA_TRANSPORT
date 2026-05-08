<?php

declare(strict_types=1);

namespace Application\Transaction\Tiket\Commands\Update;

use Shared\Transaction\Tiket\Commands\Update\UpdateTiketRequest;
use Shared\Transaction\Tiket\Commands\Update\UpdateTiketResponse;
use Client\Transaction\Tiket\TiketService;

class UpdateTiketCommand
{
    public function __construct(private TiketService $service) {}
    public function execute(UpdateTiketRequest $req, int $actorId): UpdateTiketResponse
    {
        $entity = $this->service->getById($req->id);
        if (!$entity) return new UpdateTiketResponse(false, 'Tiket tidak ditemukan.');
        $entity->update($req->tujuan, $req->tanggalBerangkat, $req->jamBerangkat, $req->harga, $req->isFull, $actorId);
        $entity->setArmadaId($req->armadaId);
        $this->service->update($entity);
        return new UpdateTiketResponse(true, 'Tiket berhasil diperbarui.');
    }
}
