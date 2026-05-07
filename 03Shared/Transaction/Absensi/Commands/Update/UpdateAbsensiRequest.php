<?php

declare(strict_types=1);

namespace Shared\Transaction\Absensi\Commands\Update;

class UpdateAbsensiRequest
{
    public function __construct(
        public readonly int $id,
        public readonly string $jamMasuk = '',
        public readonly string $jamKeluar = '',
        public readonly string $status = 'hadir',
        public readonly string $keterangan = '',
        public readonly float $potonganGaji = 0,
        public readonly ?int $actorId = null,
    ) {}
}
