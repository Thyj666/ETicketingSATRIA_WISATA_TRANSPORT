<?php

declare(strict_types=1);

namespace Application\Master\User\Queries;

use Shared\Master\User\Queries\GetById\GetUserByIdRequest;
use Shared\Master\User\Queries\GetById\GetUserByIdResponse;
use Client\Master\User\UserService;

class GetUserByIdQuery
{
    public function __construct(private UserService $service) {}
    public function execute(GetUserByIdRequest $req): GetUserByIdResponse
    {
        $data = $this->service->getById($req->id);
        return new GetUserByIdResponse($data, $data !== null);
    }
}
