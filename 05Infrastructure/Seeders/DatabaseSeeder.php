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
     * Golongan → Jabatan → User → (tambahkan lainnya di sini)
     */
    private function seeders(): array
    {
        return [
            GolonganSeeder::class,
            JabatanSeeder::class,
            UserSeeder::class,
        ];
    }
}
