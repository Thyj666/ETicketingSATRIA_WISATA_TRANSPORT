<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

/**
 * ArmadaSeeder
 *
 * Seed data armada (bus/kendaraan) default ke tabel `armada`.
 * Idempoten: INSERT IGNORE berdasarkan UNIQUE key `plat_nomor`.
 *
 * Kolom yang di-seed:
 *   plat_nomor, nama_armada, tipe_seat, jumlah_seat, status,
 *   is_deleted, created_at, updated_at
 */
class ArmadaSeeder extends Seeder
{
    private array $armada = [
        [
            'plat_nomor'  => 'BA 1234 AB',
            'nama_armada' => 'Armada 01 - Ekonomi',
            'tipe_seat'   => '2-2',
            'jumlah_seat' => 40,
            'status'      => 'tersedia',
        ],
        [
            'plat_nomor'  => 'BA 5678 CD',
            'nama_armada' => 'Armada 02 - Eksekutif',
            'tipe_seat'   => '2-1',
            'jumlah_seat' => 30,
            'status'      => 'tersedia',
        ],
        [
            'plat_nomor'  => 'BA 9012 EF',
            'nama_armada' => 'Armada 03 - Ekonomi',
            'tipe_seat'   => '2-2',
            'jumlah_seat' => 40,
            'status'      => 'tidak_tersedia',
        ],
    ];

    public function run(): void
    {
        $now = date('Y-m-d H:i:s');

        foreach ($this->armada as $data) {
            $this->insertIgnore('armada', [
                'plat_nomor'  => $data['plat_nomor'],
                'nama_armada' => $data['nama_armada'],
                'tipe_seat'   => $data['tipe_seat'],
                'jumlah_seat' => $data['jumlah_seat'],
                'status'      => $data['status'],
                'is_deleted'  => 0,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }

        $this->log('ArmadaSeeder ✓ (' . count($this->armada) . ' armada)');
    }
}
