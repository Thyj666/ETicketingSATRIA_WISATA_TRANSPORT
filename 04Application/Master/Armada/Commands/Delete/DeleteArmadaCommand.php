<?php

declare(strict_types=1);

namespace Application\Master\Armada\Commands\Delete;

use Shared\Master\Armada\Commands\Delete\DeleteArmadaRequest;
use Shared\Master\Armada\Commands\Delete\DeleteArmadaResponse;
use Client\Master\Armada\ArmadaService;

class DeleteArmadaCommand
{
    public function __construct(private ArmadaService $service) {}
    public function execute(DeleteArmadaRequest $req, int $actorId): DeleteArmadaResponse
    {
        $this->service->delete($req->id, $actorId);
        return new DeleteArmadaResponse(true, 'Armada berhasil dihapus.');
    }
}
