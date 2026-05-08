<?php

declare(strict_types=1);

namespace WebApi;

use Base\Auth\Auth;
use Client\Master\User\UserService;

class DashboardController
{
    public function __construct(
        private UserService       $userService,
    ) {}

    public function index(): void
    {
        Auth::requireAuth();
        $periode = date('Y-m');
        $stats = [];
        require BASE_PATH . '/08Bsui/dashboard/index.php';
    }
}
