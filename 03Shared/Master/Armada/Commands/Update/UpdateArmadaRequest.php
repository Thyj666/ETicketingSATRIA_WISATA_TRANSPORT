<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Commands\Update;

class UpdateArmadaRequest
{
    public function __construct(
        public readonly int $id,
        public readonly string $platNomor = '',
        public readonly string $namaArmada = '',
        public readonly string $tipeSeat = '',
        public readonly int $jumlahSeat = 0,
        public readonly string $status = 'tersedia',
    ) {}
}
