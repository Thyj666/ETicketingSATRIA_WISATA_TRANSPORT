<?php

declare(strict_types=1);

namespace Application\Master\Jabatan\Commands\Update;

use Shared\Master\Jabatan\Commands\Update\UpdateJabatanRequest;
use Shared\Master\Jabatan\Commands\Update\UpdateJabatanResponse;
use Client\Master\Jabatan\JabatanService;

class UpdateJabatanCommand
{
    public function __construct(private JabatanService $service) {}
    public function execute(UpdateJabatanRequest $req): UpdateJabatanResponse
    {
        try {
            $entity = $this->service->getById($req->id);
            if (!$entity) return new UpdateJabatanResponse(false, 'Jabatan tidak ditemukan.');
            $entity->update($req->namaJabatan, $req->jenis, $req->golonganId, $req->keterangan, $req->userId);
            $this->service->save($entity);
            return new UpdateJabatanResponse(true, 'Jabatan berhasil diperbarui.');
        } catch (\DomainException $e) {
            return new UpdateJabatanResponse(false, $e->getMessage(), 0);
        }
    }
}
