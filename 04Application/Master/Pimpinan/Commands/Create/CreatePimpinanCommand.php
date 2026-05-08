<?php

declare(strict_types=1);

namespace Application\Master\Pimpinan\Commands\Create;

use Client\Master\Pimpinan\PimpinanService;
use Client\Master\User\UserService;
use Domain\Entities\Master\Pimpinan\PimpinanEntity;
use Domain\Entities\Master\User\UserEntity;

class CreatePimpinanCommand
{
    public function __construct(
        private PimpinanService $service,
        private UserService $userService
    ) {}

    public function execute(array $data, ?int $actorId = null): array
    {
        $username = trim($data['username'] ?? '');
        $password = trim($data['password'] ?? '');
        $nama     = trim($data['nama'] ?? '');
        $email    = trim($data['email'] ?? '') ?: null;
        $noTelp   = trim($data['no_telp'] ?? '') ?: null;
        $alamat   = trim($data['alamat'] ?? '') ?: null;

        if (!$username || !$password || !$nama) {
            return ['success' => false, 'message' => 'Username, password, dan nama wajib diisi.'];
        }

        if ($this->userService->usernameExists($username)) {
            return ['success' => false, 'message' => 'Username sudah digunakan.'];
        }

        $userEntity = UserEntity::create($username, $password, 'pimpinan', $actorId);
        $userId = $this->userService->save($userEntity);

        $entity = PimpinanEntity::create($userId, $nama, $email, $noTelp, $alamat, $actorId);
        $this->service->save($entity);

        return ['success' => true, 'message' => 'Pimpinan berhasil ditambahkan.'];
    }
}
