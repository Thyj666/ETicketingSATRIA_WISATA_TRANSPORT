<?php

declare(strict_types=1);

namespace Base\Schema\Attributes;

/**
 * Mendefinisikan Foreign Key pada sebuah property Entity.
 *
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class ForeignKey
{
    public function __construct(
        /** Nama tabel yang direferensikan */
        public readonly string $references,

        /** Nama kolom di tabel yang direferensikan (default: id) */
        public readonly string $on = 'id',

        /** Aksi ON DELETE: CASCADE | SET NULL | RESTRICT | NO ACTION */
        public readonly string $onDelete = 'SET NULL',

        /** Aksi ON UPDATE: CASCADE | SET NULL | RESTRICT | NO ACTION */
        public readonly string $onUpdate = 'RESTRICT',
    ) {}
}
