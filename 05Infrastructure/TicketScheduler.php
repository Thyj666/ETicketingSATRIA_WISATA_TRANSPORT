<?php

declare(strict_types=1);

namespace Infrastructure;

/**
 * TicketScheduler — Pseudo-cronjob yang berjalan otomatis saat aplikasi dibuka.
 *
 * Cara kerja:
 *  - Dipanggil sekali di index.php setiap request masuk.
 *  - Menggunakan file-lock (tmp) agar hanya berjalan maksimal 1x per interval
 *    (default: setiap 60 detik), sehingga tidak membebani database di setiap request.
 *  - Tidak memerlukan crontab atau task scheduler apapun — cocok untuk development lokal.
 *
 * Di production, Anda bisa tetap memakai ini ATAU menggantinya dengan:
 *   * * * * * php /path/to/app/05Infrastructure/CronJob.php >> /var/log/tiket-cron.log 2>&1
 */
class TicketScheduler
{
    /** Interval minimum antar eksekusi (detik). Default: 60 detik. */
    private int $intervalSeconds;

    /** Path file lock untuk throttling */
    private string $lockFile;

    public function __construct(private AppDbContext $db, int $intervalSeconds = 60)
    {
        $this->intervalSeconds = $intervalSeconds;
        $this->lockFile        = sys_get_temp_dir() . '/tiket_scheduler_last_run.txt';
    }

    /**
     * Dipanggil di setiap request. Hanya benar-benar berjalan jika sudah
     * melewati $intervalSeconds sejak eksekusi terakhir.
     */
    public function runIfDue(): void
    {
        if (!$this->isDue()) {
            return;
        }

        // Tandai waktu eksekusi sekarang sebelum proses, untuk mencegah race condition
        file_put_contents($this->lockFile, (string) time());

        try {
            $this->expirePassedTickets();
            $this->syncFullStatus();
            $this->cancelPendingOnExpiredTickets();
        } catch (\Throwable $e) {
            // Jangan crash aplikasi — log saja
            error_log('[TicketScheduler] ERROR: ' . $e->getMessage());
        }
    }

    // -----------------------------------------------------------------------
    // Private
    // -----------------------------------------------------------------------

    private function isDue(): bool
    {
        if (!file_exists($this->lockFile)) {
            return true;
        }
        $lastRun = (int) file_get_contents($this->lockFile);
        return (time() - $lastRun) >= $this->intervalSeconds;
    }

    /**
     * Tandai is_full=1 + status_perjalanan='selesai' untuk tiket yang tanggal
     * & jam keberangkatannya sudah lewat dari waktu sekarang.
     */
    private function expirePassedTickets(): void
    {
        $this->db->query(
            "UPDATE tikets
             SET    is_full = 1,
                    status_perjalanan = 'selesai',
                    updated_at = NOW()
             WHERE  is_deleted = 0
               AND  status_perjalanan = 'berlangsung'
               AND  tanggal_berangkat IS NOT NULL
               AND  jam_berangkat     IS NOT NULL
               AND  CONCAT(tanggal_berangkat, ' ', jam_berangkat) <= NOW()"
        );
    }

    /**
     * Tandai is_full=1 jika jumlah kursi terisi (confirmed/pending) >= kapasitas armada.
     * Tandai is_full=0 kembali jika ada kursi yang terbuka lagi (misal pemesanan dibatalkan)
     * dan tiket belum berangkat.
     */
    private function syncFullStatus(): void
    {
        // Penuh: kursi terisi >= kapasitas
        $this->db->query(
            "UPDATE tikets t
             INNER JOIN armada a ON t.armada_id = a.id
             SET    t.is_full = 1, t.updated_at = NOW()
             WHERE  t.is_deleted = 0
               AND  t.is_full   = 0
               AND  a.jumlah_seat > 0
               AND (
                   SELECT COUNT(*)
                   FROM   pemesanans p
                   WHERE  p.tiket_id = t.id
                     AND  p.is_deleted = 0
                     AND  p.status_pemesanan NOT IN ('cancelled','expired')
               ) >= a.jumlah_seat"
        );

        // Tidak penuh: kursi kembali tersedia & tiket belum berangkat
        $this->db->query(
            "UPDATE tikets t
             INNER JOIN armada a ON t.armada_id = a.id
             SET    t.is_full = 0, t.updated_at = NOW()
             WHERE  t.is_deleted = 0
               AND  t.is_full   = 1
               AND  t.status_perjalanan = 'berlangsung'
               AND  a.jumlah_seat > 0
               AND (
                   SELECT COUNT(*)
                   FROM   pemesanans p
                   WHERE  p.tiket_id = t.id
                     AND  p.is_deleted = 0
                     AND  p.status_pemesanan NOT IN ('cancelled','expired')
               ) < a.jumlah_seat"
        );
    }

    /**
     * Ubah status pemesanan pending menjadi 'expired' jika tiketnya sudah berangkat.
     */
    private function cancelPendingOnExpiredTickets(): void
    {
        $this->db->query(
            "UPDATE pemesanans p
             INNER JOIN tikets t ON p.tiket_id = t.id
             SET    p.status_pemesanan = 'expired', p.updated_at = NOW()
             WHERE  p.is_deleted = 0
               AND  p.status_pemesanan = 'pending'
               AND  t.is_deleted = 0
               AND  t.tanggal_berangkat IS NOT NULL
               AND  t.jam_berangkat     IS NOT NULL
               AND  CONCAT(t.tanggal_berangkat, ' ', t.jam_berangkat) <= NOW()"
        );
    }
}
