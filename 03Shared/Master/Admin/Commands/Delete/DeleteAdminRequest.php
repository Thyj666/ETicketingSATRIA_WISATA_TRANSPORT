<?php

declare(strict_types=1);

namespace Shared\Master\Admin\Commands\Delete;

class DeleteAdminRequest
{
    public function __construct(
        public readonly int $id,
    ) {}
}
