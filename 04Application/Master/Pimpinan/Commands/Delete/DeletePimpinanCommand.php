<?php

declare(strict_types=1);

namespace Application\Master\Pimpinan\Commands\Delete;

use Client\Master\Pimpinan\PimpinanService;

class DeletePimpinanCommand
{
    public function __construct(private PimpinanService $service) {}

    public function execute(int $id, ?int $actorId = null): array
    {
        $entity = $this->service->getById($id);
        if (!$entity) return ['success' => false, 'message' => 'Pimpinan tidak ditemukan.'];
        $this->service->delete($id, $actorId);
        return ['success' => true, 'message' => 'Pimpinan berhasil dihapus.'];
    }
}
