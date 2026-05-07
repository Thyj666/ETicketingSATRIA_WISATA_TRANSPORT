<?php

declare(strict_types=1);

namespace Application\Master\Jabatan\Commands\Delete;

use Shared\Master\Jabatan\Commands\Delete\DeleteJabatanRequest;
use Shared\Master\Jabatan\Commands\Delete\DeleteJabatanResponse;
use Client\Master\Jabatan\JabatanService;

class DeleteJabatanCommand
{
    public function __construct(private JabatanService $service) {}
    public function execute(DeleteJabatanRequest $req): DeleteJabatanResponse
    {
        try {
            if ($this->service->isUsedByUser($req->id)) {
                return new DeleteJabatanResponse(false, 'Jabatan masih digunakan oleh pengguna aktif.');
            }
            $ok = $this->service->delete($req->id, $req->userId);
            return new DeleteJabatanResponse($ok, $ok ? 'Jabatan berhasil dihapus.' : 'Gagal menghapus.');
        } catch (\DomainException $e) {
            return new DeleteJabatanResponse(false, $e->getMessage(), 0);
        }
    }
}
