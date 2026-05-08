<?php

declare(strict_types=1);

namespace Application\Transaction\Tiket\Commands\Delete;

use Shared\Transaction\Tiket\Commands\Delete\DeleteTiketRequest;
use Shared\Transaction\Tiket\Commands\Delete\DeleteTiketResponse;
use Client\Transaction\Tiket\TiketService;

class DeleteTiketCommand
{
    public function __construct(private TiketService $service) {}
    public function execute(DeleteTiketRequest $req, int $actorId): DeleteTiketResponse
    {
        $this->service->delete($req->id, $actorId);
        return new DeleteTiketResponse(true, 'Tiket berhasil dihapus.');
    }
}
