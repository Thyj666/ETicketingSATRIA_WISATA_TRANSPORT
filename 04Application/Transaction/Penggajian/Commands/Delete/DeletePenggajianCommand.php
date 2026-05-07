<?php

declare(strict_types=1);

namespace Application\Transaction\Penggajian\Commands\Delete;

use Shared\Transaction\Penggajian\Commands\Delete\DeletePenggajianRequest;
use Shared\Transaction\Penggajian\Commands\Delete\DeletePenggajianResponse;
use Client\Transaction\Penggajian\PenggajianService;

class DeletePenggajianCommand
{
    public function __construct(private PenggajianService $service) {}
    public function execute(DeletePenggajianRequest $req): DeletePenggajianResponse
    {
        $ok = $this->service->delete($req->id, $req->actorId);
        return new DeletePenggajianResponse($ok, $ok ? 'Penggajian berhasil dihapus.' : 'Gagal menghapus.');
    }
}
