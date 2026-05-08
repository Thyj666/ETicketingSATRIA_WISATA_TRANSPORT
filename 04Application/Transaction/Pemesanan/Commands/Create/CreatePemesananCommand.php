<?php

declare(strict_types=1);

namespace Application\Transaction\Pemesanan\Commands\Create;

use Shared\Transaction\Pemesanan\Commands\Create\CreatePemesananRequest;
use Shared\Transaction\Pemesanan\Commands\Create\CreatePemesananResponse;
use Client\Transaction\Pemesanan\PemesananService;
use Client\Transaction\Tiket\TiketService;
use Domain\Entities\Transaction\Pemesanan\PemesananEntity;

class CreatePemesananCommand
{
    public function __construct(
        private PemesananService $pemesananService,
        private TiketService $tiketService,
    ) {}

    public function execute(CreatePemesananRequest $req, int $actorId): CreatePemesananResponse
    {
        // Check if seat is taken
        if ($this->pemesananService->isSeatTaken($req->tiketId, $req->noSeat)) {
            return new CreatePemesananResponse(false, 'Kursi ' . $req->noSeat . ' sudah dipesan.');
        }

        $tiket = $this->tiketService->getById($req->tiketId);
        if (!$tiket) return new CreatePemesananResponse(false, 'Tiket tidak ditemukan.');
        if ($tiket->getIsFull()) return new CreatePemesananResponse(false, 'Tiket sudah penuh.');

        $orderId = 'ORD-' . strtoupper(date('Ymd')) . '-' . strtoupper(substr(uniqid(), -6));
        $noPemesanan = $this->pemesananService->generateNoPemesanan();

        $entity = PemesananEntity::create(
            $tiket->getArmadaId(),
            $req->userId,
            $req->tiketId,
            $noPemesanan,
            $req->noSeat,
            (float)($tiket->getHarga() ?? 0),
            date('Y-m-d'),
            date('H:i:s'),
            'pending',
            $orderId,
            $actorId
        );

        $id = $this->pemesananService->save($entity);

        // Check if armada is now full
        $armada = $tiket->getArmada();
        if ($armada) {
            $booked = $this->pemesananService->countByTiket($req->tiketId, 'confirmed');
            if ($booked >= $armada->getJumlahSeat()) {
                $this->tiketService->updateFullStatus($req->tiketId, true);
            }
        }

        return new CreatePemesananResponse(true, 'Pemesanan berhasil dibuat.', $id, $orderId, (float)($tiket->getHarga() ?? 0));
    }
}
