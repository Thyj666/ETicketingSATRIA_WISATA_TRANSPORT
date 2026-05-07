<?php

declare(strict_types=1);

namespace Shared\Transaction\Absensi\Commands\Create;

class CreateAbsensiRequest
{
    public function __construct(
        public readonly int $userId,
        public readonly string $tanggal,
        public readonly string $jamMasuk = '',
        public readonly string $jamKeluar = '',
        public readonly string $status = 'hadir',
        public readonly string $keterangan = '',
        public readonly float $potonganGaji = 0,
        public readonly ?int $actorId = null,
    ) {}
}
