<?php
declare(strict_types=1);
namespace Shared\Master\Golongan\Commands\Delete;
class DeleteGolonganRequest {
    public function __construct(
        public readonly int  $id,
        public readonly ?int $userId = null,
    ) {}
}
