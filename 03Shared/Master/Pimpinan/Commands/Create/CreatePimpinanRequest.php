<?php

declare(strict_types=1);

namespace Shared\Master\Pimpinan\Commands\Create;

class CreatePimpinanRequest
{
    public function __construct(
        public readonly string $nama,
        public readonly ?string $email,
        public readonly ?string $noTelp,
        public readonly ?string $alamat,
        public readonly ?string $foto,
        public readonly bool $isActive = true,
        public readonly ?int $userId = null,
    ) {}
}
