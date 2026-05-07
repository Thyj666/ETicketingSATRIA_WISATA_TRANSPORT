<?php

declare(strict_types=1);

namespace Application\Master\Golongan\Commands\Create;

use Shared\Master\Golongan\Commands\Create\CreateGolonganRequest;
use Shared\Master\Golongan\Commands\Create\CreateGolonganResponse;
use Client\Master\Golongan\GolonganService;
use Domain\Entities\Master\Golongan\GolonganEntity;

class CreateGolonganCommand
{
    public function __construct(private GolonganService $service) {}
    public function execute(CreateGolonganRequest $req): CreateGolonganResponse
    {
        if ($this->service->existsByKode($req->kodeGolongan)) {
            return new CreateGolonganResponse(false, 'Kode golongan sudah digunakan.');
        }
        $entity = GolonganEntity::create($req->kodeGolongan, $req->namaGolongan, $req->gajiPokok, $req->tunjangan, $req->userId);
        $id = $this->service->save($entity);
        return new CreateGolonganResponse(true, 'Golongan berhasil ditambahkan.', $id);
    }
}
