<?php

declare(strict_types=1);

namespace Base\Schema\Attributes;

/**
 * Mendefinisikan kolom database dari property Entity.
 *
 * Contoh — enum dari PHP enum class (direkomendasikan):
 *   #[Column(type: 'enum', enumClass: Role::class)]
 *   private string $role = '';
 *
 * Contoh — enum manual (fallback jika tidak punya enum class):
 *   #[Column(type: 'enum', enumValues: ['guru', 'staff'])]
 *   private string $jenis = '';
 *
 * Contoh — tipe lain:
 *   #[Column(type: 'varchar', length: 150, nullable: false)]
 *   private string $nama = '';
 *
 *   #[Column(type: 'decimal', precision: 15, scale: 2, default: 0)]
 *   private float $gajiPokok = 0;
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column
{
    public function __construct(
        /**
         * Tipe kolom MySQL: varchar, text, int, bigint, decimal,
         * tinyint, date, datetime, time, enum, float
         */
        public readonly string $type = 'varchar',

        /** Panjang untuk varchar / char */
        public readonly ?int $length = null,

        /** Presisi untuk decimal */
        public readonly ?int $precision = null,

        /** Skala untuk decimal */
        public readonly ?int $scale = null,

        /** Apakah kolom boleh NULL */
        public readonly bool $nullable = false,

        /** Nilai default kolom (null = tidak ada DEFAULT) */
        public readonly mixed $default = 'NONE',

        /**
         * Nama kolom di database (snake_case).
         * Jika null, akan di-convert otomatis dari nama property.
         */
        public readonly ?string $name = null,

        /**
         * FQCN dari PHP backed enum yang menjadi sumber nilai ENUM kolom ini.
         * MigrationGenerator akan memanggil ::values() pada enum tersebut.
         *
         * Contoh: enumClass: Role::class
         *         → memanggil Role::values() → ['admin_tu','kepala_sekolah','guru','staff']
         *
         * Prioritas: enumClass > enumValues.
         * Gunakan ini jika enum sudah didefinisikan sebagai PHP enum (direkomendasikan).
         */
        public readonly ?string $enumClass = null,

        /**
         * Nilai-nilai ENUM secara manual (fallback jika tidak ada enum class).
         * Contoh: ['guru', 'staff']
         * Diabaikan jika enumClass diisi.
         */
        public readonly array $enumValues = [],

        /** Tandai kolom ini sebagai UNSIGNED */
        public readonly bool $unsigned = false,
    ) {}
}
