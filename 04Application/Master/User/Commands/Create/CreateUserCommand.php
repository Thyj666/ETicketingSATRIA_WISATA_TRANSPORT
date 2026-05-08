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
        if (!$req->username || !$req->password) {
            return new CreateUserResponse(false, 'Username dan password wajib diisi.');
        }
        if ($this->service->usernameExists($req->username)) {
            return new CreateUserResponse(false, 'Username sudah digunakan.');
        }
        $entity = UserEntity::create(
            $req->username,
            $req->password,
            $req->role,
            $req->userId,
        );
        $id = $this->service->save($entity);
        return new CreateUserResponse(true, 'User berhasil ditambahkan.', $id);
    }
}
