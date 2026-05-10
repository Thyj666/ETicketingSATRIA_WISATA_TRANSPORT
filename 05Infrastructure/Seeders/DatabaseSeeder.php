<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

use Infrastructure\AppDbContext;

/**
 * DatabaseSeeder
 *
 * Orchestrator utama untuk semua seeder.
 * Urutan seeder PENTING: data yang menjadi referensi FK harus di-seed lebih dulu.
 *
 * Urutan saat ini:
 *   1. UserSeeder   → mengisi `users` + profil (admin / pimpinan / pelanggan)
 *   2. ArmadaSeeder → mengisi `armada` (tidak bergantung pada user)
 *
 * Cara menambah seeder baru:
 *   1. Buat file SesuatuSeeder.php di folder ini, extends Seeder.
 *   2. Implementasikan method run() — pastikan idempoten (INSERT IGNORE).
 *   3. Daftarkan di $this->seeders() pada posisi urutan yang tepat.
 */
class DatabaseSeeder
{
    private AppDbContext $db;

    public function __construct(AppDbContext $db)
    {
        $this->db = $db;
    }

    public function run(): void
    {
        foreach ($this->seeders() as $seederClass) {
            try {
                /** @var Seeder $seeder */
                $seeder = new $seederClass($this->db);
                $seeder->run();
            } catch (\Throwable $e) {
                error_log("[Seeder] ERROR {$seederClass}: " . $e->getMessage());
            }
        }
    }

    /**
     * Daftar seeder yang akan dijalankan, berurutan.
     *
     * PENTING:
     *   - UserSeeder HARUS lebih dulu dari seeder lain yang butuh user_id sebagai FK.
     *   - ArmadaSeeder tidak bergantung pada tabel user, boleh di posisi manapun.
     */
    private function seeders(): array
    {
        return [
            UserSeeder::class,    // 1. users + admin + pimpinan + pelanggan
            ArmadaSeeder::class,  // 2. armada
        ];
    }
}
