<?php

declare(strict_types=1);

namespace Infrastructure;

/**
 * CronJob - Auto-expire tiket yang sudah berangkat atau habis
 * 
 * Jalankan via CLI: php /path/to/app/05Infrastructure/CronJob.php
 * 
 * Tambahkan ke crontab (setiap menit):
 * * * * * * php /var/www/html/05Infrastructure/CronJob.php >> /var/log/tiket-cron.log 2>&1
 *
 * Atau setiap 5 menit:
 * *\/5 * * * * php /var/www/html/05Infrastructure/CronJob.php >> /var/log/tiket-cron.log 2>&1
 */

// Bootstrap minimal
if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    echo json_encode(['error' => 'CLI only']);
    exit(1);
}

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/05Infrastructure/AppDbContext.php';

class TicketCronJob
{
    private AppDbContext $db;

    public function __construct()
    {
        $this->db = AppDbContext::getInstance();
    }

    public function run(): void
    {
        $this->log('=== CronJob Tiket dimulai: ' . date('Y-m-d H:i:s') . ' ===');

        $expired  = $this->expireBerangkat();
        $fullSync = $this->syncFullStatus();
        $cancelled = $this->cancelPendingExpiredBookings();

        $this->log("Tiket diexpire (sudah berangkat): {$expired}");
        $this->log("Tiket sync is_full: {$fullSync}");
        $this->log("Pemesanan pending di-cancel karena tiket expired: {$cancelled}");
        $this->log('=== CronJob selesai ===');
    }

    /**
     * Tandai tiket sebagai is_full=1 jika tanggal+jam berangkat sudah lewat
     */
    private function expireBerangkat(): int
    {
        try {
            $result = $this->db->query(
                "UPDATE tikets 
                 SET is_full = 1, updated_at = NOW()
                 WHERE is_deleted = 0
                   AND is_full = 0
                   AND tanggal_berangkat IS NOT NULL
                   AND jam_berangkat IS NOT NULL
                   AND CONCAT(tanggal_berangkat, ' ', jam_berangkat) <= NOW()"
            );
            return $result->rowCount();
        } catch (\Throwable $e) {
            $this->log('ERROR expireBerangkat: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Sync is_full berdasarkan jumlah seat yang sudah terisi
     * (jika semua seat sudah dipesan dan confirmed/pending)
     */
    private function syncFullStatus(): int
    {
        try {
            // Tandai is_full=1 jika jumlah kursi terisi >= kapasitas armada
            $result = $this->db->query(
                "UPDATE tikets t
                 INNER JOIN armada a ON t.armada_id = a.id
                 SET t.is_full = 1, t.updated_at = NOW()
                 WHERE t.is_deleted = 0
                   AND t.is_full = 0
                   AND a.jumlah_seat > 0
                   AND (
                       SELECT COUNT(*) FROM pemesanans p
                       WHERE p.tiket_id = t.id
                         AND p.is_deleted = 0
                         AND p.status_pemesanan NOT IN ('cancelled', 'expired')
                   ) >= a.jumlah_seat"
            );

            // Tandai is_full=0 jika masih ada kursi kosong dan belum berangkat
            $this->db->query(
                "UPDATE tikets t
                 INNER JOIN armada a ON t.armada_id = a.id
                 SET t.is_full = 0, t.updated_at = NOW()
                 WHERE t.is_deleted = 0
                   AND t.is_full = 1
                   AND (tanggal_berangkat IS NULL OR CONCAT(tanggal_berangkat, ' ', IFNULL(jam_berangkat, '23:59:59')) > NOW())
                   AND a.jumlah_seat > 0
                   AND (
                       SELECT COUNT(*) FROM pemesanans p
                       WHERE p.tiket_id = t.id
                         AND p.is_deleted = 0
                         AND p.status_pemesanan NOT IN ('cancelled', 'expired')
                   ) < a.jumlah_seat"
            );

            return $result->rowCount();
        } catch (\Throwable $e) {
            $this->log('ERROR syncFullStatus: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Cancel pemesanan pending jika tiketnya sudah expired/berangkat
     */
    private function cancelPendingExpiredBookings(): int
    {
        try {
            $result = $this->db->query(
                "UPDATE pemesanans p
                 INNER JOIN tikets t ON p.tiket_id = t.id
                 SET p.status_pemesanan = 'expired', p.updated_at = NOW()
                 WHERE p.is_deleted = 0
                   AND p.status_pemesanan = 'pending'
                   AND t.is_deleted = 0
                   AND t.tanggal_berangkat IS NOT NULL
                   AND t.jam_berangkat IS NOT NULL
                   AND CONCAT(t.tanggal_berangkat, ' ', t.jam_berangkat) <= NOW()"
            );
            return $result->rowCount();
        } catch (\Throwable $e) {
            $this->log('ERROR cancelPendingExpiredBookings: ' . $e->getMessage());
            return 0;
        }
    }

    private function log(string $message): void
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
        echo $line;
        error_log($message);
    }
}

// Run
try {
    $job = new TicketCronJob();
    $job->run();
    exit(0);
} catch (\Throwable $e) {
    echo '[' . date('Y-m-d H:i:s') . '] FATAL: ' . $e->getMessage() . PHP_EOL;
    exit(1);
}
