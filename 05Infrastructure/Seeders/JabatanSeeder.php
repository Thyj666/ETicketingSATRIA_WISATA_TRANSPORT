<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

class JabatanSeeder extends Seeder
{
    public function run(): void
    {
        // Lookup golongan_id dari kode — Golongan harus sudah di-seed lebih dulu
        $golIVa  = $this->findId('golongan', 'kode_golongan', 'IV/a');
        $golIIIb = $this->findId('golongan', 'kode_golongan', 'III/b');
        $golIIa  = $this->findId('golongan', 'kode_golongan', 'II/a');

        $rows = [
            // [nama_jabatan, jenis, golongan_id]
            ['Kepala Sekolah',          'staff', $golIVa],
            ['Guru Matematika',         'guru',  $golIIIb],
            ['Guru Bahasa Indonesia',   'guru',  $golIIIb],
            ['Guru Bahasa Inggris',     'guru',  $golIIIb],
            ['Guru IPA',                'guru',  $golIIIb],
            ['Guru IPS',                'guru',  $golIIIb],
            ['Guru PKN',                'guru',  $golIIIb],
            ['Guru Agama',              'guru',  $golIIIb],
            ['Guru Olahraga',           'guru',  $golIIIb],
            ['Guru BK',                 'guru',  $golIIIb],
            ['Staff TU',                'staff', $golIIa],
            ['Bendahara',               'staff', $golIIa],
            ['Petugas Perpustakaan',    'staff', $golIIa],
            ['Admin TU',                'staff', $golIIIb],
        ];

        foreach ($rows as [$nama, $jenis, $golId]) {
            $this->insertIgnore('jabatan', [
                'nama_jabatan' => $nama,
                'jenis'        => $jenis,
                'golongan_id'  => $golId,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        }

        $this->log('JabatanSeeder ✓ (' . count($rows) . ' rows)');
    }
}
