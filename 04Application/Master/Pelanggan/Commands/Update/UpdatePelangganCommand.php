<?php

declare(strict_types=1);

namespace Application\Master\Pelanggan\Commands\Update;

use Client\Master\Pelanggan\PelangganService;
use Client\Master\User\UserService;

class UpdatePelangganCommand
{
    public function __construct(
        private PelangganService $service,
        private UserService $userService
    ) {}

    public function execute(int $id, array $data, ?int $actorId = null): array
    {
        $entity = $this->service->getById($id);
        if (!$entity) return ['success' => false, 'message' => 'Pelanggan tidak ditemukan.'];

        $nama   = trim($data['nama'] ?? $entity->getNama());
        $email  = trim($data['email'] ?? '') ?: null;
        $noTelp = trim($data['no_telp'] ?? '') ?: null;
        $alamat = trim($data['alamat'] ?? '') ?: null;
        $isActive = isset($data['is_active']) ? (bool)$data['is_active'] : $entity->getIsActive();

        $entity->update($nama, $email, $noTelp, $alamat, $isActive, $actorId);
        $this->service->save($entity);

        // Update password jika diisi
        if (!empty($data['password'])) {
            $userEntity = $this->userService->getById($entity->getUserId());
            if ($userEntity) {
                $userEntity->hashPassword($data['password']);
                $this->userService->save($userEntity);
            }
        }

        return ['success' => true, 'message' => 'Pelanggan berhasil diperbarui.'];
    }
}
