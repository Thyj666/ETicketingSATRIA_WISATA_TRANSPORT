<?php

declare(strict_types=1);

namespace Shared\Master\Armada\Commands\Create;

class CreateArmadaRequest
{
    public function __construct(
        public readonly string $platNomor,
        public readonly string $namaArmada,
        public readonly string $tipeSeat,
        public readonly int $jumlahSeat,
        public readonly string $status = 'tersedia',
    ) {}
}
