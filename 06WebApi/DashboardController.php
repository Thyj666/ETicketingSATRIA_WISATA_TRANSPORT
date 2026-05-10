<?php

declare(strict_types=1);

namespace WebApi;

use Base\Auth\Auth;
use Infrastructure\AppDbContext;

class DashboardController
{
    public function __construct(
        private AppDbContext $db,
    ) {}

    public function index(): void
    {
        Auth::requireAuth();

        $today = date('Y-m-d');

        $stats = [
            'tiket'     => (int) ($this->db->fetchOne(
                "SELECT COUNT(*) as c FROM tikets WHERE is_deleted=0 AND is_full=0"
            )['c'] ?? 0),

            'armada'    => (int) ($this->db->fetchOne(
                "SELECT COUNT(*) as c FROM armada WHERE is_deleted=0 AND status='digunakan'"
            )['c'] ?? 0),

            'pemesanan' => (int) ($this->db->fetchOne(
                "SELECT COUNT(*) as c FROM pemesanans WHERE is_deleted=0 AND DATE(created_at)=?",
                [$today]
            )['c'] ?? 0),

            'pelanggan' => (int) ($this->db->fetchOne(
                "SELECT COUNT(*) as c FROM pelanggan WHERE is_deleted=0"
            )['c'] ?? 0),
        ];

        require BASE_PATH . '/08Bsui/dashboard/index.php';
    }
}
