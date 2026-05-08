<?php

declare(strict_types=1);

namespace Infrastructure;

/**
 * CronJobHttp - Endpoint HTTP untuk menjalankan cron via request
 * 
 * Bisa dipanggil dari:
 * 1. Server cron: curl -s http://yourdomain.com/cron/tiket?secret=SECRET_KEY
 * 2. Route internal (tambahkan ke index.php): 'GET:/cron/tiket' => ['CronController', 'run']
 * 
 * PENTING: Lindungi dengan CRON_SECRET di .env
 */

class CronJobHttp
{
    public static function handle(): void
    {
        $secret = getenv('CRON_SECRET') ?: 'satria-wisata-cron-2025';
        $providedSecret = $_GET['secret'] ?? $_SERVER['HTTP_X_CRON_SECRET'] ?? '';

        if (!hash_equals($secret, $providedSecret)) {
            http_response_code(403);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }

        define('BASE_PATH', dirname(__DIR__));
        require_once BASE_PATH . '/05Infrastructure/AppDbContext.php';

        $db = AppDbContext::getInstance();
        $results = [];

        // 1. Expire tiket yang sudah berangkat
        try {
            $r1 = $db->query(
                "UPDATE tikets 
                 SET is_full = 1, updated_at = NOW()
                 WHERE is_deleted = 0 AND is_full = 0
                   AND tanggal_berangkat IS NOT NULL AND jam_berangkat IS NOT NULL
                   AND CONCAT(tanggal_berangkat, ' ', jam_berangkat) <= NOW()"
            );
            $results['expired_tickets'] = $r1->rowCount();
        } catch (\Throwable $e) {
            $results['expired_tickets_error'] = $e->getMessage();
        }

        // 2. Sync is_full status
        try {
            $r2 = $db->query(
                "UPDATE tikets t INNER JOIN armada a ON t.armada_id = a.id
                 SET t.is_full = 1, t.updated_at = NOW()
                 WHERE t.is_deleted = 0 AND t.is_full = 0 AND a.jumlah_seat > 0
                   AND (SELECT COUNT(*) FROM pemesanans p 
                        WHERE p.tiket_id = t.id AND p.is_deleted = 0
                          AND p.status_pemesanan NOT IN ('cancelled','expired')
                   ) >= a.jumlah_seat"
            );
            $results['synced_full'] = $r2->rowCount();
        } catch (\Throwable $e) {
            $results['synced_full_error'] = $e->getMessage();
        }

        // 3. Cancel pemesanan pending pada tiket expired
        try {
            $r3 = $db->query(
                "UPDATE pemesanans p INNER JOIN tikets t ON p.tiket_id = t.id
                 SET p.status_pemesanan = 'expired', p.updated_at = NOW()
                 WHERE p.is_deleted = 0 AND p.status_pemesanan = 'pending'
                   AND t.is_deleted = 0 AND t.tanggal_berangkat IS NOT NULL
                   AND CONCAT(t.tanggal_berangkat, ' ', t.jam_berangkat) <= NOW()"
            );
            $results['expired_bookings'] = $r3->rowCount();
        } catch (\Throwable $e) {
            $results['expired_bookings_error'] = $e->getMessage();
        }

        header('Content-Type: application/json');
        echo json_encode([
            'status'    => 'ok',
            'timestamp' => date('Y-m-d H:i:s'),
            'results'   => $results,
        ]);
        exit;
    }
}
