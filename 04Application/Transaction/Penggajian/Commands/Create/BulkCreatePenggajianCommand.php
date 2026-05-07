<?php

declare(strict_types=1);

namespace Application\Transaction\Penggajian\Commands\BulkCreate;

use Client\Transaction\Penggajian\PenggajianService;
use Client\Transaction\Absensi\AbsensiService;
use Domain\Entities\Transaction\Penggajian\PenggajianEntity;

class BulkCreatePenggajianCommand
{
    public function __construct(
        private PenggajianService $service,
        private AbsensiService    $absensiService,
    ) {}

    /**
     * Simpan penggajian untuk banyak pegawai sekaligus dalam satu periode.
     *
     * @param string $periode   Format Y-m (contoh: 2025-04)
     * @param array  $rows      [ ['user_id'=>int, 'potongan_lain'=>float, 'keterangan'=>string] ]
     * @param int    $actorId
     * @return array            ['success'=>int, 'skipped'=>int, 'errors'=>string[]]
     */
    public function execute(string $periode, array $rows, int $actorId): array
    {
        $success = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($rows as $row) {
            $userId = (int)($row['user_id'] ?? 0);
            if ($userId <= 0) continue;

            // Sudah ada penggajian di periode ini → lewati
            if ($this->service->existsByUserPeriode($userId, $periode)) {
                $skipped++;
                continue;
            }

            try {
                // Ambil data gaji (gaji_pokok + tunjangan) dari jabatan & golongan
                $salaryData = $this->service->getUserSalaryData($userId);

                // Ambil rekap potongan absensi periode ini
                $attendanceSummary = $this->service->getAttendanceSummary($userId, $periode);

                $gajiPokok       = (float)($salaryData['gaji_pokok']  ?? 0);
                $tunjangan       = (float)($salaryData['tunjangan']    ?? 0);
                $potonganAbsensi = (float)($attendanceSummary['total_potongan_absensi'] ?? 0);
                $potonganLain    = (float)($row['potongan_lain'] ?? 0);
                $keterangan      = $row['keterangan'] ?? '';

                $entity = PenggajianEntity::create(
                    userId: $userId,
                    periode: $periode,
                    gajiPokok: $gajiPokok,
                    tunjangan: $tunjangan,
                    potonganAbsensi: $potonganAbsensi,
                    potonganLain: $potonganLain,
                    keterangan: $keterangan,
                    createdBy: $actorId,
                );

                $this->service->createFromIntegration($entity);
                $success++;
            } catch (\Throwable $e) {
                $errors[] = "User ID {$userId}: " . $e->getMessage();
            }
        }

        return compact('success', 'skipped', 'errors');
    }
}
