<?php
declare(strict_types=1);
namespace Shared\Master\Golongan\Queries\GetById;
use Domain\Entities\Master\Golongan\GolonganEntity;
class GetGolonganByIdResponse {
    public function __construct(
        public readonly ?GolonganEntity $data,
        public readonly bool $found,
    ) {}
}
