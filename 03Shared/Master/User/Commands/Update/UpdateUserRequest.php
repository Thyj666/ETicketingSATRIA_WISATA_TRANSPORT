<?php

declare(strict_types=1);

namespace Shared\Master\User\Commands\Update;

class UpdateUserRequest
{
    public function __construct(
        public readonly int $id,
        public readonly string $nama,
        public readonly string $email = '',
        public readonly string $nip = '',
        public readonly string $noTelp = '',
        public readonly string $alamat = '',
        public readonly string $role = 'guru',
        public readonly ?int $jabatanId = null,
        public readonly float $gajiPokok = 0,
        public readonly string $jenisKelamin = 'L',
        public readonly bool $isActive = true,
        public readonly string $password = '',
        public readonly ?int $userId = null,
    ) {}
}
