<?php

declare(strict_types=1);

namespace Infrastructure;

use Base\Schema\MigrationGenerator;

/**
 * DatabaseMigration
 *
 * Versi baru yang TIDAK perlu menulis SQL secara manual.
 * SQL CREATE TABLE di-generate otomatis dari Entity class via #[Table],
 * #[Column], #[ForeignKey], dan #[Ignore] attributes.
 *
 * Cara mendaftarkan entity baru:
 *   1. Tambahkan #[Table], #[Column], dst di Entity-nya.
 *   2. Daftarkan di $this->getEntityMigrations() dengan key unik dan FQCN-nya.
 *   3. Jalankan aplikasi — migration otomatis ter-eksekusi.
 *
 * Urutan array PENTING: tabel yang direferensikan FK harus didaftarkan lebih dulu.
 */
class DatabaseMigration
{
    private AppDbContext     $db;
    private MigrationGenerator $generator;

    public function __construct(AppDbContext $db)
    {
        $this->db        = $db;
        $this->generator = new MigrationGenerator();
    }

    // =========================================================================
    // Public entry point
    // =========================================================================

    public function run(): void
    {
        $this->createMigrationTable();

        foreach ($this->getEntityMigrations() as $name => $entityClass) {
            $this->runOne($name, $entityClass);
        }
    }

    // =========================================================================
    // Daftar Entity -> Migration
    // Urutan penting: parent FK harus lebih dulu dari child-nya.
    // =========================================================================

    private function getEntityMigrations(): array
    {
        return [
            // key = nama unik migration (tidak boleh diubah setelah dijalankan)
            // value = FQCN entity
            '001_create_golongan'   => \Domain\Entities\Master\Golongan\GolonganEntity::class,
            '002_create_jabatan'    => \Domain\Entities\Master\Jabatan\JabatanEntity::class,
            '003_create_users'      => \Domain\Entities\Master\User\UserEntity::class,
            '004_create_absensi'    => \Domain\Entities\Transaction\Absensi\AbsensiEntity::class,
            '005_create_penggajian' => \Domain\Entities\Transaction\Penggajian\PenggajianEntity::class,
        ];
    }

    // =========================================================================
    // Internals
    // =========================================================================

    private function runOne(string $name, string $entityClass): void
    {
        // Cek apakah migration sudah dijalankan sebelumnya
        $done = $this->db->fetchOne(
            'SELECT id FROM _migrations WHERE name = ? LIMIT 1',
            [$name]
        );

        // Self-healing: jika migration tercatat selesai tapi tabelnya tidak ada,
        // hapus record migration agar bisa dijalankan ulang.
        if ($done) {
            $tableName = $this->resolveTableName($entityClass);
            if ($tableName !== null && !$this->tableExists($tableName)) {
                $this->db->execute('DELETE FROM _migrations WHERE name = ?', [$name]);
                error_log("[Migration] WARNING {$name}: record ada tapi tabel `{$tableName}` tidak ditemukan -- menjalankan ulang.");
            } else {
                return;
            }
        }

        try {
            $sql = $this->generator->generate($entityClass);

            $this->db->beginTransaction();
            $this->db->getPdo()->exec($sql);
            $this->db->execute(
                'INSERT INTO _migrations (name, executed_at) VALUES (?, NOW())',
                [$name]
            );
            $this->db->commit();

            error_log("[Migration] OK {$name}");
        } catch (\Throwable $e) {
            $this->db->rollback();
            $msg = "[Migration] FAILED {$name}: " . $e->getMessage();
            error_log($msg);
            // Tampilkan error agar mudah di-debug di browser
            echo '<div style="font-family:monospace;background:#fff3cd;border:2px solid #856404;padding:12px;margin:8px">'
                . '<b>[Migration Error]</b> ' . htmlspecialchars($name) . ': '
                . htmlspecialchars($e->getMessage()) . '</div>';
        }
    }

    /**
     * Cek apakah sebuah tabel sudah ada di database aktif.
     */
    private function tableExists(string $tableName): bool
    {
        $row = $this->db->fetchOne(
            "SELECT 1 FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1",
            [$tableName]
        );
        return $row !== null;
    }

    /**
     * Ambil nama tabel dari #[Table] attribute di entity class.
     * Mengembalikan null jika entity tidak punya attribute tersebut.
     */
    private function resolveTableName(string $entityClass): ?string
    {
        try {
            $ref   = new \ReflectionClass($entityClass);
            $attrs = $ref->getAttributes(\Base\Schema\Attributes\Table::class);
            if (empty($attrs)) {
                return null;
            }
            /** @var \Base\Schema\Attributes\Table $table */
            $table = $attrs[0]->newInstance();
            return $table->name;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function createMigrationTable(): void
    {
        $this->db->getPdo()->exec("
            CREATE TABLE IF NOT EXISTS `_migrations` (
                `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name`        VARCHAR(255) NOT NULL UNIQUE,
                `executed_at` DATETIME     NOT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
        ");
    }
}
