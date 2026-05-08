<?php

declare(strict_types=1);

namespace Application\Master\Admin\Commands\Delete;

use Client\Master\Admin\AdminService;

class DeleteAdminCommand
{
    public function __construct(private AdminService $service) {}

    public function execute(int $id, ?int $actorId = null): array
    {
        $entity = $this->service->getById($id);
        if (!$entity) return ['success' => false, 'message' => 'Admin tidak ditemukan.'];
        $this->service->delete($id, $actorId);
        return ['success' => true, 'message' => 'Admin berhasil dihapus.'];
    }
}
