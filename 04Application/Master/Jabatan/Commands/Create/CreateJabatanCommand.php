<?php

declare(strict_types=1);

namespace Application\Master\Jabatan\Commands\Create;

use Shared\Master\Jabatan\Commands\Create\CreateJabatanRequest;
use Shared\Master\Jabatan\Commands\Create\CreateJabatanResponse;
use Client\Master\Jabatan\JabatanService;
use Domain\Entities\Master\Jabatan\JabatanEntity;

class CreateJabatanCommand
{
    public function __construct(private JabatanService $service) {}
    public function execute(CreateJabatanRequest $req): CreateJabatanResponse
    {
        try {
            $entity = JabatanEntity::create($req->namaJabatan, $req->jenis, $req->golonganId, $req->keterangan, $req->userId);
            $id = $this->service->save($entity);
            return new CreateJabatanResponse(true, 'Jabatan berhasil ditambahkan.', $id);
        } catch (\DomainException $e) {
            return new CreateJabatanResponse(false, $e->getMessage(), 0);
        }
    }
}
