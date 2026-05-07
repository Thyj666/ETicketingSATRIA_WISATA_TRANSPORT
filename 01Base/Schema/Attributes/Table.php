<?php

declare(strict_types=1);

namespace Base\Schema\Attributes;

/**
 * Mendefinisikan tabel database dari suatu Entity class.
 *
 * Contoh:
 *   #[Table(name: 'users', indexes: ['username'], uniques: ['username', 'nip'])]
 *   class UserEntity extends AuditableEntity { ... }
 */
#[\Attribute(\Attribute::TARGET_CLASS)]
class Table
{
    public function __construct(
        /** Nama tabel di database */
        public readonly string $name,

        /**
         * Daftar kolom yang diberi INDEX biasa (non-unique).
         * Bisa berupa nama kolom tunggal atau composite (array of arrays).
         *
         * Contoh:
         *   indexes: ['jabatan_id']                        → INDEX idx_jabatan_id (jabatan_id)
         *   indexes: [['user_id', 'tanggal']]              → INDEX idx_user_id_tanggal (user_id, tanggal)
         */
        public readonly array $indexes = [],

        /**
         * Daftar kolom yang diberi UNIQUE KEY.
         * Format sama dengan $indexes.
         *
         * Contoh:
         *   uniques: ['username']                          → UNIQUE KEY uk_username (username)
         *   uniques: [['user_id', 'periode']]              → UNIQUE KEY uk_user_id_periode (user_id, periode)
         */
        public readonly array $uniques = [],

        /** Storage engine MySQL (default InnoDB) */
        public readonly string $engine = 'InnoDB',

        /** Charset tabel */
        public readonly string $charset = 'utf8mb4',
    ) {}
}
