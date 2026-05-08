<?php

declare(strict_types=1);

namespace Base\Schema;

use Base\Schema\Attributes\Column;
use Base\Schema\Attributes\ForeignKey;
use Base\Schema\Attributes\Ignore;
use Base\Schema\Attributes\Table;

/**
 * MigrationGenerator
 *
 * Men-generate DDL SQL (CREATE TABLE) secara otomatis dari Entity class
 * dengan membaca PHP Attributes (#[Table], #[Column], #[ForeignKey], #[Ignore])
 * menggunakan ReflectionClass — tanpa framework tambahan.
 *
 * Cara kerja:
 *  1. Terima FQCN (Fully Qualified Class Name) entity
 *  2. Baca #[Table] di class → nama tabel, index, unique key
 *  3. Baca #[Column] di setiap property → kolom, tipe, panjang, nullable, default
 *  4. Baca #[ForeignKey] di property → FOREIGN KEY constraint
 *  5. Property bertanda #[Ignore] dilewati
 *  6. Property dari abstract parent (id, created_at, dst) di-handle otomatis
 *
 * Kolom auto dari Entity/AuditableEntity:
 *   - id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
 *   - created_at    DATETIME
 *   - updated_at    DATETIME
 *   - created_by    INT UNSIGNED
 *   - updated_by    INT UNSIGNED
 *   - is_deleted    TINYINT(1) NOT NULL DEFAULT 0
 */
class MigrationGenerator
{
    /**
     * Generate SQL CREATE TABLE dari sebuah entity class.
     *
     * @param  string $entityClass FQCN, contoh: Domain\Entities\Master\User\UserEntity
     * @return string              SQL siap pakai
     * @throws \ReflectionException
     * @throws \RuntimeException   jika entity tidak punya #[Table] attribute
     */
    public function generate(string $entityClass): string
    {
        $ref = new \ReflectionClass($entityClass);

        // --- Baca #[Table] attribute dari class ---
        $tableAttr = $this->getClassAttribute($ref, Table::class);
        if ($tableAttr === null) {
            throw new \RuntimeException(
                "Entity [{$entityClass}] tidak memiliki #[Table] attribute. " .
                    "Tambahkan #[Table(name: 'nama_tabel')] di atas class."
            );
        }
        /** @var Table $tableMeta */
        $tableMeta = $tableAttr->newInstance();

        $lines = [];

        // --- Kolom otomatis dari Entity base: id ---
        $lines[] = '    `id`          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY';

        // --- Kolom dari property entity (semua hierarki class) ---
        $foreignKeys = [];
        foreach ($this->getAllProperties($ref) as $prop) {
            // Lewati jika ada #[Ignore]
            if ($this->hasAttribute($prop, Ignore::class)) {
                continue;
            }

            // Lewati property bawaan abstract parent yang sudah kita tangani manual
            // (id, createdAt, updatedAt, createdBy, updatedBy, isDeleted)
            if (in_array($prop->getName(), ['id', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy', 'isDeleted'])) {
                continue;
            }

            $colAttr = $this->getPropAttribute($prop, Column::class);
            if ($colAttr === null) {
                // Property tanpa #[Column] — lewati (mungkin join-field tanpa #[Ignore])
                continue;
            }

            /** @var Column $col */
            $col     = $colAttr->newInstance();
            $colName = $col->name ?? $this->toSnakeCase($prop->getName());

            $lines[] = '    ' . $this->buildColumnDDL($colName, $col);

            // Kumpulkan FK jika ada
            $fkAttr = $this->getPropAttribute($prop, ForeignKey::class);
            if ($fkAttr !== null) {
                /** @var ForeignKey $fk */
                $fk = $fkAttr->newInstance();
                $foreignKeys[] = [
                    'column'     => $colName,
                    'references' => $fk->references,
                    'on'         => $fk->on,
                    'onDelete'   => $fk->onDelete,
                    'onUpdate'   => $fk->onUpdate,
                ];
            }
        }

        // --- Kolom audit otomatis dari AuditableEntity ---
        if ($this->extendsAuditable($ref)) {
            $lines[] = '    `is_deleted`  TINYINT(1)    NOT NULL DEFAULT 0';
            $lines[] = '    `created_at`  DATETIME';
            $lines[] = '    `updated_at`  DATETIME';
            $lines[] = '    `created_by`  INT UNSIGNED';
            $lines[] = '    `updated_by`  INT UNSIGNED';
        }

        // --- UNIQUE KEY ---
        foreach ($tableMeta->uniques as $unique) {
            $cols   = (array) $unique;
            $keyName = 'uk_' . implode('_', $cols);
            $colList = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
            $lines[] = "    UNIQUE KEY `{$keyName}` ({$colList})";
        }

        // --- INDEX ---
        foreach ($tableMeta->indexes as $index) {
            $cols    = (array) $index;
            $keyName = 'idx_' . implode('_', $cols);
            $colList = implode(', ', array_map(fn($c) => "`{$c}`", $cols));
            $lines[] = "    INDEX `{$keyName}` ({$colList})";
        }

        // --- FOREIGN KEY ---
        foreach ($foreignKeys as $fk) {
            $constraintName = "fk_{$tableMeta->name}_{$fk['column']}";
            $lines[] = sprintf(
                '    CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `%s`(`%s`) ON DELETE %s ON UPDATE %s',
                $constraintName,
                $fk['column'],
                $fk['references'],
                $fk['on'],
                $fk['onDelete'],
                $fk['onUpdate'],
            );
        }

        $body   = implode(",\n", $lines);
        $engine  = $tableMeta->engine;
        $charset = $tableMeta->charset;

        return <<<SQL
        CREATE TABLE IF NOT EXISTS `{$tableMeta->name}` (
        {$body}
        ) ENGINE={$engine} DEFAULT CHARSET={$charset}
        SQL;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Build satu baris DDL kolom dari Column attribute.
     */
    private function buildColumnDDL(string $colName, Column $col): string
    {
        $type = strtolower($col->type);

        $typeDef = match (true) {
            $type === 'varchar'   => "VARCHAR({$this->len($col, 100)})",
            $type === 'char'      => "CHAR({$this->len($col, 1)})",
            $type === 'text'      => 'TEXT',
            $type === 'longtext'  => 'LONGTEXT',
            $type === 'int'       => 'INT' . ($col->unsigned ? ' UNSIGNED' : ''),
            $type === 'bigint'    => 'BIGINT' . ($col->unsigned ? ' UNSIGNED' : ''),
            $type === 'tinyint'   => "TINYINT({$this->len($col, 1)})",
            $type === 'smallint'  => 'SMALLINT' . ($col->unsigned ? ' UNSIGNED' : ''),
            $type === 'decimal'   => 'DECIMAL(' . ($col->precision ?? 15) . ',' . ($col->scale ?? 2) . ')',
            $type === 'float'     => 'FLOAT',
            $type === 'double'    => 'DOUBLE',
            $type === 'date'      => 'DATE',
            $type === 'datetime'  => 'DATETIME',
            $type === 'time'      => 'TIME',
            $type === 'timestamp' => 'TIMESTAMP',
            $type === 'boolean'   => 'TINYINT(1)',
            $type === 'enum'      => $this->buildEnum($col),
            $type === 'json'      => 'JSON',
            default               => strtoupper($type),
        };

        $nullable = $col->nullable ? 'NULL' : 'NOT NULL';
        $default  = '';

        if ($col->default !== 'NONE') {
            if ($col->default === null) {
                $default = ' DEFAULT NULL';
            } elseif (is_bool($col->default)) {
                $default = ' DEFAULT ' . ($col->default ? '1' : '0');
            } elseif (is_string($col->default)) {
                $default = " DEFAULT '{$col->default}'";
            } else {
                $default = ' DEFAULT ' . $col->default;
            }
        }

        return "`{$colName}` {$typeDef} {$nullable}{$default}";
    }

    private function buildEnum(Column $col): string
    {
        // Prioritas 1: ambil nilai dari PHP backed enum class (enumClass)
        if (!empty($col->enumClass)) {
            $enumClass = $col->enumClass;
            if (!enum_exists($enumClass)) {
                throw new \RuntimeException("enumClass '{$enumClass}' tidak ditemukan atau bukan PHP enum.");
            }
            if (!method_exists($enumClass, 'values')) {
                // Fallback: ambil langsung dari cases() jika tidak ada ::values()
                $values = array_column($enumClass::cases(), 'value');
            } else {
                $values = $enumClass::values();
            }
            if (empty($values)) {
                throw new \RuntimeException("enumClass '{$enumClass}' tidak memiliki nilai.");
            }
            $vals = implode("','", $values);
            return "ENUM('{$vals}')";
        }

        // Prioritas 2: nilai enum manual (enumValues)
        if (!empty($col->enumValues)) {
            $vals = implode("','", $col->enumValues);
            return "ENUM('{$vals}')";
        }

        throw new \RuntimeException("Column dengan type='enum' harus mengisi enumClass atau enumValues.");
    }

    private function len(Column $col, int $default): int
    {
        return $col->length ?? $default;
    }

    /** Cek apakah class merupakan turunan dari AuditableEntity */
    private function extendsAuditable(\ReflectionClass $ref): bool
    {
        $parent = $ref->getParentClass();
        while ($parent) {
            if ($parent->getShortName() === 'AuditableEntity') {
                return true;
            }
            $parent = $parent->getParentClass();
        }
        return false;
    }

    /**
     * Ambil semua property dari class + semua parent-nya,
     * tapi EXCLUDE property dari abstract base (Entity, AuditableEntity).
     */
    private function getAllProperties(\ReflectionClass $ref): array
    {
        $props     = [];
        $baseNames = ['Entity', 'AuditableEntity'];
        $current   = $ref;

        while ($current) {
            if (in_array($current->getShortName(), $baseNames)) {
                break;
            }
            foreach ($current->getProperties() as $prop) {
                $props[$prop->getName()] = $prop;
            }
            $current = $current->getParentClass() ?: null;
        }

        return $props;
    }

    private function getClassAttribute(\ReflectionClass $ref, string $attrClass): ?\ReflectionAttribute
    {
        $attrs = $ref->getAttributes($attrClass);
        return $attrs[0] ?? null;
    }

    private function getPropAttribute(\ReflectionProperty $prop, string $attrClass): ?\ReflectionAttribute
    {
        $attrs = $prop->getAttributes($attrClass);
        return $attrs[0] ?? null;
    }

    private function hasAttribute(\ReflectionProperty $prop, string $attrClass): bool
    {
        return !empty($prop->getAttributes($attrClass));
    }

    /**
     * Convert camelCase → snake_case
     */
    private function toSnakeCase(string $name): string
    {
        return strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($name)));
    }
}
