<?php

declare(strict_types=1);

namespace Application\Master\Pelanggan\Commands\Delete;

use Client\Master\Pelanggan\PelangganService;

class DeletePelangganCommand
{
    public function __construct(private PelangganService $service) {}

    public function execute(int $id, ?int $actorId = null): array
    {
        $entity = $this->service->getById($id);
        if (!$entity) return ['success' => false, 'message' => 'Pelanggan tidak ditemukan.'];
        $this->service->delete($id, $actorId);
        return ['success' => true, 'message' => 'Pelanggan berhasil dihapus.'];
    }
}
