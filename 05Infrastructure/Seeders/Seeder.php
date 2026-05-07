<?php

declare(strict_types=1);

namespace Infrastructure\Seeders;

use Infrastructure\AppDbContext;

/**
 * Base class untuk semua Seeder.
 * Setiap seeder wajib mengimplementasikan method run().
 */
abstract class Seeder
{
    public function __construct(
        protected readonly AppDbContext $db
    ) {}

    /**
     * Jalankan seeder ini.
     * Implementasi di child class harus idempoten:
     * jalankan berkali-kali tidak boleh duplikat data.
     */
    abstract public function run(): void;

    /**
     * Helper: insert satu baris dengan INSERT IGNORE (skip jika sudah ada).
     */
    protected function insertIgnore(string $table, array $data): void
    {
        $cols   = implode(', ', array_map(fn($k) => "`{$k}`", array_keys($data)));
        $marks  = implode(', ', array_fill(0, count($data), '?'));
        $this->db->execute(
            "INSERT IGNORE INTO `{$table}` ({$cols}) VALUES ({$marks})",
            array_values($data)
        );
    }

    /**
     * Helper: ambil ID dari tabel berdasarkan kondisi kolom tertentu.
     */
    protected function findId(string $table, string $column, mixed $value): ?int
    {
        $row = $this->db->fetchOne(
            "SELECT id FROM `{$table}` WHERE `{$column}` = ? LIMIT 1",
            [$value]
        );
        return $row ? (int) $row['id'] : null;
    }

    protected function log(string $msg): void
    {
        error_log("[Seeder] {$msg}");
    }
}
