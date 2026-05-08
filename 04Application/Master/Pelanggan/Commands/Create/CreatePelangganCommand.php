<?php

declare(strict_types=1);

namespace Application\Master\Pelanggan\Commands\Create;

use Client\Master\Pelanggan\PelangganService;
use Client\Master\User\UserService;
use Domain\Entities\Master\Pelanggan\PelangganEntity;
use Domain\Entities\Master\User\UserEntity;

class CreatePelangganCommand
{
    public function __construct(
        private PelangganService $service,
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

        $userEntity = UserEntity::create($username, $password, 'pelanggan', $actorId);
        $userId = $this->userService->save($userEntity);

        $entity = PelangganEntity::create($userId, $nama, $email, $noTelp, $alamat, $actorId);
        $this->service->save($entity);

        return ['success' => true, 'message' => 'Pelanggan berhasil ditambahkan.'];
    }
}
