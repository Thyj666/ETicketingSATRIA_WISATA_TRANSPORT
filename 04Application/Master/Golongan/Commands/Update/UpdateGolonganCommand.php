<?php

declare(strict_types=1);

namespace Application\Master\Golongan\Commands\Update;

use Shared\Master\Golongan\Commands\Update\UpdateGolonganRequest;
use Shared\Master\Golongan\Commands\Update\UpdateGolonganResponse;
use Client\Master\Golongan\GolonganService;

class UpdateGolonganCommand
{
    public function __construct(private GolonganService $service) {}
    public function execute(UpdateGolonganRequest $req): UpdateGolonganResponse
    {
        $entity = $this->service->getById($req->id);
        if (!$entity) return new UpdateGolonganResponse(false, 'Golongan tidak ditemukan.');
        if ($this->service->existsByKode($req->kodeGolongan, $req->id)) {
            return new UpdateGolonganResponse(false, 'Kode golongan sudah digunakan.');
        }
        $entity->update($req->kodeGolongan, $req->namaGolongan, $req->gajiPokok, $req->tunjangan, $req->userId);
        $this->service->save($entity);
        return new UpdateGolonganResponse(true, 'Golongan berhasil diperbarui.');
    }
}
