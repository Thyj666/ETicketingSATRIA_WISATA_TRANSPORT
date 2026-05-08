<?php

declare(strict_types=1);

namespace Shared\Master\Admin\Commands\Delete;

class DeleteAdminResponse
{
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
        public readonly int    $id = 0,
    ) {}
}
