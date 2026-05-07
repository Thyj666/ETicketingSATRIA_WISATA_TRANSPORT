<?php
declare(strict_types=1);
namespace Shared\Master\Golongan\Commands\Delete;
class DeleteGolonganResponse {
    public function __construct(
        public readonly bool   $success,
        public readonly string $message = '',
    ) {}
}
