<?php

declare(strict_types=1);

namespace Application\Transaction\Penggajian\Commands\Create;

use Shared\Transaction\Penggajian\Commands\Create\CreatePenggajianRequest;
use Shared\Transaction\Penggajian\Commands\Create\CreatePenggajianResponse;
use Client\Transaction\Penggajian\PenggajianService;
use Client\Transaction\Absensi\AbsensiService;
use Domain\Entities\Transaction\Penggajian\PenggajianEntity;

class CreatePenggajianCommand
{
    public function __construct(
        private PenggajianService $service,
        private AbsensiService    $absensiService,
    ) {}
    public function execute(CreatePenggajianRequest $req): CreatePenggajianResponse
    {
        if ($this->service->existsByUserPeriode($req->userId, $req->periode)) {
            return new CreatePenggajianResponse(false, 'Data gaji periode ini sudah ada.');
        }
        $summary       = $this->absensiService->getSummaryByUserPeriode($req->userId, $req->periode);
        $potonganAbsensi = $req->potonganAbsensi > 0 ? $req->potonganAbsensi : (float)($summary['total_potongan'] ?? 0);
        $total         = $req->gajiPokok + $req->tunjangan - $potonganAbsensi - $req->potonganLain;
        $entity = PenggajianEntity::create(
            $req->userId,
            $req->periode,
            $req->gajiPokok,
            $req->tunjangan,
            $potonganAbsensi,
            $req->potonganLain,
            $req->keterangan,
            $req->actorId
        );
        $id = $this->service->createFromIntegration($entity);
        return new CreatePenggajianResponse(true, 'Penggajian berhasil dibuat.', $id);
    }
}
