<?php

declare(strict_types=1);

namespace Application\Master\User\Commands\Update;

use Shared\Master\User\Commands\Update\UpdateUserRequest;
use Shared\Master\User\Commands\Update\UpdateUserResponse;
use Client\Master\User\UserService;

class UpdateUserCommand
{
    public function __construct(private UserService $service) {}
    public function execute(UpdateUserRequest $req): UpdateUserResponse
    {
        $entity = $this->service->getById($req->id);
        if (!$entity) return new UpdateUserResponse(false, 'User tidak ditemukan.');
        $hashed = $req->password ? password_hash($req->password, PASSWORD_BCRYPT) : '';
        $entity->update(
            $req->nama,
            $req->email,
            $req->nip,
            $req->noTelp,
            $req->alamat,
            $req->role,
            $req->jabatanId,
            $req->gajiPokok,
            $req->jenisKelamin,
            $req->isActive,
            $hashed,
            $req->userId
        );
        $this->service->save($entity);
        return new UpdateUserResponse(true, 'User berhasil diperbarui.');
    }
}
