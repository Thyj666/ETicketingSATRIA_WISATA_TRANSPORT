<?php

declare(strict_types=1);

namespace Base\Schema\Attributes;

/**
 * Menandai sebuah property Entity agar DIABAIKAN oleh MigrationGenerator.
 * Gunakan ini untuk join-fields / virtual fields yang tidak punya kolom di DB.
 *
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Ignore {}
