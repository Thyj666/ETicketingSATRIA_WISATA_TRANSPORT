<?php
declare(strict_types=1);
namespace Shared\Master\Golongan\Queries\GetById;
class GetGolonganByIdRequest {
    public function __construct(public readonly int $id) {}
}
