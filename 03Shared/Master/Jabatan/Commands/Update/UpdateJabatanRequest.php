<?php

declare(strict_types=1);

namespace Shared\Master\Jabatan\Commands\Update;

class UpdateJabatanRequest
{
    public function __construct(
        public readonly int $id,
        public readonly string $namaJabatan,
        public readonly string $jenis,
        public readonly ?int $golonganId,
        public readonly string $keterangan = '',
        public readonly ?int $userId = null,
    ) {}
}
