<?php

declare(strict_types=1);

namespace Shared\Master\Golongan\Commands\Create;

class CreateGolonganRequest
{
    public function __construct(
        public readonly string $kodeGolongan,
        public readonly string $namaGolongan,
        public readonly float  $gajiPokok,
        public readonly float  $tunjangan,
        public readonly ?int   $userId = null,
    ) {}
}
