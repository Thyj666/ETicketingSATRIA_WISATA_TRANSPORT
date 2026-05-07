<?php

declare(strict_types=1);

namespace Shared\Transaction\Penggajian\Commands\Create;

class CreatePenggajianRequest
{
    public function __construct(
        public readonly int $userId,
        public readonly string $periode,
        public readonly float $gajiPokok = 0,
        public readonly float $tunjangan = 0,
        public readonly float $potonganAbsensi = 0,
        public readonly float $potonganLain = 0,
        public readonly string $keterangan = '',
        public readonly ?int $actorId = null,
    ) {}
}
