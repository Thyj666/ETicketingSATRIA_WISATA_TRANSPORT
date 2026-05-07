<?php

declare(strict_types=1);

namespace Application\Master\Golongan\Commands\Delete;

use Shared\Master\Golongan\Commands\Delete\DeleteGolonganRequest;
use Shared\Master\Golongan\Commands\Delete\DeleteGolonganResponse;
use Client\Master\Golongan\GolonganService;

class DeleteGolonganCommand
{
    public function __construct(private GolonganService $service) {}
    public function execute(DeleteGolonganRequest $req): DeleteGolonganResponse
    {
        if ($this->service->isUsedByJabatan($req->id)) {
            return new DeleteGolonganResponse(false, 'Golongan masih digunakan oleh jabatan aktif.');
        }
        $ok = $this->service->delete($req->id, $req->userId);
        return new DeleteGolonganResponse($ok, $ok ? 'Golongan berhasil dihapus.' : 'Gagal menghapus.');
    }
}
