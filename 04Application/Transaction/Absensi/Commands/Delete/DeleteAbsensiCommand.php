<?php

declare(strict_types=1);

namespace Application\Transaction\Absensi\Commands\Delete;

use Shared\Transaction\Absensi\Commands\Delete\DeleteAbsensiRequest;
use Shared\Transaction\Absensi\Commands\Delete\DeleteAbsensiResponse;
use Client\Transaction\Absensi\AbsensiService;

class DeleteAbsensiCommand
{
    public function __construct(private AbsensiService $service) {}
    public function execute(DeleteAbsensiRequest $req): DeleteAbsensiResponse
    {
        $ok = $this->service->delete($req->id, $req->actorId);
        return new DeleteAbsensiResponse($ok, $ok ? 'Absensi berhasil dihapus.' : 'Gagal menghapus.');
    }
}
