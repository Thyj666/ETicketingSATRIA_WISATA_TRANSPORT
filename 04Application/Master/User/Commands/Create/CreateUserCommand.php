<?php

declare(strict_types=1);

namespace Application\Master\User\Commands\Create;

use Shared\Master\User\Commands\Create\CreateUserRequest;
use Shared\Master\User\Commands\Create\CreateUserResponse;
use Client\Master\User\UserService;
use Domain\Entities\Master\User\UserEntity;

class CreateUserCommand
{
    public function __construct(private UserService $service) {}
    public function execute(CreateUserRequest $req): CreateUserResponse
    {
        if ($this->service->usernameExists($req->username)) {
            return new CreateUserResponse(false, 'Username sudah digunakan.');
        }
        $entity = UserEntity::create(
            $req->nama,
            $req->username,
            $req->password,
            $req->email,
            $req->nip,
            $req->role,
            $req->jabatanId,
            $req->gajiPokok,
            $req->jenisKelamin,
            $req->noTelp,
            $req->alamat,
            $req->userId
        );
        $id = $this->service->save($entity);
        return new CreateUserResponse(true, 'User berhasil ditambahkan.', $id);
    }
}
