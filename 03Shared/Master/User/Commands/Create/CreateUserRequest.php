<?php
declare(strict_types=1);
namespace Shared\Master\User\Commands\Create;

class CreateUserRequest
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
        public readonly string $role = 'pelanggan',
        public readonly bool $isActive = true,
        public readonly ?int $userId = null,
    ) {}
}
