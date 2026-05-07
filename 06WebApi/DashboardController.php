<?php

declare(strict_types=1);

namespace WebApi;

use Base\Auth\Auth;
use Client\Master\User\UserService;
use Client\Transaction\Absensi\AbsensiService;
use Client\Transaction\Penggajian\PenggajianService;

class DashboardController
{
    public function __construct(
        private UserService       $userService,
        private AbsensiService    $absensiService,
        private PenggajianService $penggajianService,
    ) {}

    public function index(): void
    {
        Auth::requireAuth();
        $periode = date('Y-m');
        $stats = [
            'total_guru'   => count($this->userService->getAll('', 'guru')),
            'total_staff'  => count($this->userService->getAll('', 'staff')),
            'absensi_hari' => count($this->absensiService->getAll(['bulan' => $periode])),
            'gaji_pending' => count($this->penggajianService->getAll(['periode' => $periode, 'status' => 'pending'])),
            'total_gaji'   => array_sum(array_map(fn($p) => $p->getTotalGaji(), $this->penggajianService->getAll(['periode' => $periode]))),
        ];
        $recentAbsensi = $this->absensiService->getAll(['bulan' => $periode]);
        require BASE_PATH . '/08Bsui/dashboard/index.php';
    }
}
