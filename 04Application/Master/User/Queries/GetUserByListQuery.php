<?php

declare(strict_types=1);

namespace Application\Master\User\Queries;

use Shared\Master\User\Queries\GetByList\GetUserByListRequest;
use Shared\Master\User\Queries\GetByList\GetUserByListResponse;
use Client\Master\User\UserService;

class GetUserByListQuery
{
    public function __construct(private UserService $service) {}
    public function execute(GetUserByListRequest $req): GetUserByListResponse
    {
        return new GetUserByListResponse($this->service->getAll($req->search, $req->role));
    }
}
