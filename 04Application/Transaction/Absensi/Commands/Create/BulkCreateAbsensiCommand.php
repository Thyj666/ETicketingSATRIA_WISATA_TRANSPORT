<?php

declare(strict_types=1);

namespace Application\Transaction\Absensi\Commands\BulkCreate;

use Client\Transaction\Absensi\AbsensiService;
use Domain\Entities\Transaction\Absensi\AbsensiEntity;

class BulkCreateAbsensiCommand
{
    public function __construct(private AbsensiService $service) {}

    /**
     * Simpan absensi untuk banyak pegawai sekaligus dalam satu tanggal.
     *
     * @param string $tanggal   Format Y-m-d
     * @param array  $rows      [ ['user_id'=>int, 'status'=>string, 'jam_masuk'=>string,
     *                             'jam_keluar'=>string, 'keterangan'=>string, 'potongan_gaji'=>float] ]
     * @param int    $actorId
     * @return array            ['success'=>int, 'skipped'=>int, 'errors'=>string[]]
     */
    public function execute(string $tanggal, array $rows, int $actorId): array
    {
        $success = 0;
        $skipped = 0;
        $errors  = [];

        foreach ($rows as $row) {
            $userId = (int)($row['user_id'] ?? 0);
            if ($userId <= 0) continue;

            // Sudah absen hari ini → lewati (checkbox seharusnya tidak muncul, tapi guard tetap perlu)
            if ($this->service->existsByUserTanggal($userId, $tanggal)) {
                $skipped++;
                continue;
            }

            try {
                $entity = AbsensiEntity::create(
                    userId: $userId,
                    tanggal: $tanggal,
                    status: $row['status']       ?? 'hadir',
                    jamMasuk: $row['jam_masuk']    ?? '',
                    jamKeluar: $row['jam_keluar']   ?? '',
                    keterangan: $row['keterangan']   ?? '',
                    potonganGaji: (float)($row['potongan_gaji'] ?? 0),
                    createdBy: $actorId,
                );
                $this->service->save($entity);
                $success++;
            } catch (\Throwable $e) {
                $errors[] = "User ID {$userId}: " . $e->getMessage();
            }
        }

        return compact('success', 'skipped', 'errors');
    }
}
