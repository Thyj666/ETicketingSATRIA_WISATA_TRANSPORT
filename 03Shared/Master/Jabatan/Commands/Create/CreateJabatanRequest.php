<?php

declare(strict_types=1);

namespace Shared\Master\Jabatan\Commands\Create;

class CreateJabatanRequest
{
    public function __construct(
        public readonly string $namaJabatan,
        public readonly string $jenis,
        public readonly ?int $golonganId,
        public readonly string $keterangan = '',
        public readonly ?int $userId = null,
    ) {}
}
