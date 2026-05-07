<?php
declare(strict_types=1);
namespace Shared\Master\Golongan\Commands\Update;
class UpdateGolonganRequest {
    public function __construct(
        public readonly int    $id,
        public readonly string $kodeGolongan,
        public readonly string $namaGolongan,
        public readonly float  $gajiPokok,
        public readonly float  $tunjangan,
        public readonly ?int   $userId = null,
    ) {}
}
