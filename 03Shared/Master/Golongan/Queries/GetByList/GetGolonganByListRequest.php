<?php
declare(strict_types=1);
namespace Shared\Master\Golongan\Queries\GetByList;
class GetGolonganByListRequest {
    public function __construct(public readonly string $search = '') {}
}
