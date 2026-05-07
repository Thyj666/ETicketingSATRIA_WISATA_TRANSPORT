<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

class GolonganSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // [kode_golongan, nama_golongan, gaji_pokok, tunjangan]
            ['IV/a',  'Pembina',            4_500_000, 1_500_000],
            ['III/d', 'Penata Tk. I',       3_800_000, 1_200_000],
            ['III/c', 'Penata',             3_500_000, 1_100_000],
            ['III/b', 'Penata Muda Tk. I',  3_200_000, 1_000_000],
            ['III/a', 'Penata Muda',        2_900_000,   900_000],
            ['II/d',  'Pengatur Tk. I',     2_600_000,   800_000],
            ['II/c',  'Pengatur',           2_400_000,   700_000],
            ['II/a',  'Pengatur Muda',      2_200_000,   600_000],
        ];

        foreach ($rows as [$kode, $nama, $gaji, $tunjangan]) {
            $this->insertIgnore('golongan', [
                'kode_golongan' => $kode,
                'nama_golongan' => $nama,
                'gaji_pokok'    => $gaji,
                'tunjangan'     => $tunjangan,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        $this->log('GolonganSeeder ✓ (' . count($rows) . ' rows)');
    }
}
