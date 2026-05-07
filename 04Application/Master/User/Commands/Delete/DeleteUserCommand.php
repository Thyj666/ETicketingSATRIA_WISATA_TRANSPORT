<?php

declare(strict_types=1);

namespace Application\Master\User\Commands\Delete;

use Shared\Master\User\Commands\Delete\DeleteUserRequest;
use Shared\Master\User\Commands\Delete\DeleteUserResponse;
use Client\Master\User\UserService;

class DeleteUserCommand
{
    public function __construct(private UserService $service) {}
    public function execute(DeleteUserRequest $req): DeleteUserResponse
    {
        $ok = $this->service->delete($req->id, $req->userId);
        return new DeleteUserResponse($ok, $ok ? 'User berhasil dihapus.' : 'Gagal menghapus.');
    }
}
