<?php

declare(strict_types=1);

namespace Infrastructure;

use Base\Schema\MigrationGenerator;

class DatabaseMigration
{
    private AppDbContext     $db;
    private MigrationGenerator $generator;

    public function __construct(AppDbContext $db)
    {
        $this->db        = $db;
        $this->generator = new MigrationGenerator();
    }

    public function run(): void
    {
        $this->createMigrationTable();

        foreach ($this->getEntityMigrations() as $name => $entityClass) {
            $this->runOne($name, $entityClass);
        }
    }

    private function getEntityMigrations(): array
    {
        return [
            '001_create_users'      => \Domain\Entities\Master\User\UserEntity::class,
            '002_create_admin'      => \Domain\Entities\Master\Admin\AdminEntity::class,
            '003_create_pelanggan'  => \Domain\Entities\Master\Pelanggan\PelangganEntity::class,
            '004_create_pimpinan'   => \Domain\Entities\Master\Pimpinan\PimpinanEntity::class,
            '010_create_armada'     => \Domain\Entities\Master\Armada\ArmadaEntity::class,
            '011_create_tikets'     => \Domain\Entities\Transaction\Tiket\TiketEntity::class,
            '012_create_pemesanans' => \Domain\Entities\Transaction\Pemesanan\PemesananEntity::class,
        ];
    }

    private function runOne(string $name, string $entityClass): void
    {
        $done = $this->db->fetchOne(
            'SELECT id FROM _migrations WHERE name = ? LIMIT 1',
            [$name]
        );

        if ($done) {
            $tableName = $this->resolveTableName($entityClass);
            if ($tableName !== null && !$this->tableExists($tableName)) {
                $this->db->execute('DELETE FROM _migrations WHERE name = ?', [$name]);
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
        } catch (\Throwable $e) {
            $this->db->rollback();
            error_log("[Migration] FAILED {$name}: " . $e->getMessage());
        }
    }

    private function tableExists(string $tableName): bool
    {
        $row = $this->db->fetchOne(
            "SELECT 1 FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1",
            [$tableName]
        );
        return $row !== null;
    }

    private function resolveTableName(string $entityClass): ?string
    {
        try {
            $ref   = new \ReflectionClass($entityClass);
            $attrs = $ref->getAttributes(\Base\Schema\Attributes\Table::class);
            if (empty($attrs)) return null;
            $table = $attrs[0]->newInstance();
            return $table->name;
        } catch (\Throwable) {
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
