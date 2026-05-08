<?php

declare(strict_types=1);

namespace Application\Master\Admin\Commands\Create;

use Client\Master\Admin\AdminService;
use Client\Master\User\UserService;
use Domain\Entities\Master\Admin\AdminEntity;
use Domain\Entities\Master\User\UserEntity;

class CreateAdminCommand
{
    public function __construct(
        private AdminService $service,
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

        $userEntity = UserEntity::create($username, $password, 'admin', $actorId);
        $userId = $this->userService->save($userEntity);

        $entity = AdminEntity::create($userId, $nama, $email, $noTelp, $alamat, $actorId);
        $this->service->save($entity);

        return ['success' => true, 'message' => 'Admin berhasil ditambahkan.'];
    }
}
