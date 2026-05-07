<?php

declare(strict_types=1);

namespace Shared\Transaction\Penggajian\Commands\Update;

class UpdatePenggajianRequest
{
    public function __construct(
        public readonly int $id,
        public readonly float $tunjangan = 0,
        public readonly float $potonganAbsensi = 0,
        public readonly float $potonganLain = 0,
        public readonly string $status = 'pending',
        public readonly string $keterangan = '',
        public readonly ?string $tanggalBayar = null,
        public readonly ?int $actorId = null,
    ) {}
}
