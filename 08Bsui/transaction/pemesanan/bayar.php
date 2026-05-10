<?php
// transaction/pemesanan/bayar.php — Halaman pembayaran Midtrans
$pageTitle  = 'Pembayaran';
$activeMenu = 'transaksi/pemesanan';
require BASE_PATH . '/08Bsui/layouts/app.php';

$tiket  = $data->getTiket();
$tgl    = $tiket?->getTanggalBerangkat() ? date('d M Y', strtotime($tiket->getTanggalBerangkat())) : '—';
$jam    = $tiket?->getJamBerangkat() ? substr($tiket->getJamBerangkat(), 0, 5) : '—';
$total  = 'Rp ' . number_format($data->getTotalHarga(), 0, ',', '.');
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">💳 Pembayaran</h1>
            <p class="page-desc">Selesaikan pembayaran untuk mengkonfirmasi pemesanan Anda</p>
        </div>
        <a href="<?= url('/transaksi/pemesanan') ?>" class="btn btn-ghost">← Kembali</a>
    </div>

    <?php if ($flash ?? null): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="bayar-layout">
        <!-- Detail Pemesanan -->
        <div class="bayar-detail card-block">
            <h3 class="section-title-sm">📋 Detail Pemesanan</h3>
            <div class="detail-rows">
                <div class="detail-row">
                    <span class="detail-label">No. Pemesanan</span>
                    <span class="detail-value badge badge-outline"><?= htmlspecialchars($data->getNoPemesanan()) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tujuan</span>
                    <span class="detail-value"><strong><?= htmlspecialchars($tiket?->getTujuan() ?? '—') ?></strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Tanggal Berangkat</span>
                    <span class="detail-value"><?= $tgl ?> · <?= $jam ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">No. Kursi</span>
                    <span class="detail-value badge badge-outline">Kursi <?= htmlspecialchars($data->getNoSeat()) ?></span>
                </div>
                <?php if ($tiket?->getArmada()): ?>
                    <div class="detail-row">
                        <span class="detail-label">Armada</span>
                        <span class="detail-value">🚌 <?= htmlspecialchars($tiket->getArmada()->getNamaArmada()) ?> · <?= htmlspecialchars($tiket->getArmada()->getPlatNomor()) ?></span>
                    </div>
                <?php endif; ?>
                <div class="detail-row detail-row-total">
                    <span class="detail-label">Total Pembayaran</span>
                    <span class="detail-value total-price"><?= $total ?></span>
                </div>
            </div>
        </div>

        <!-- Panel Bayar -->
        <div class="bayar-panel card-block">
            <h3 class="section-title-sm">💳 Metode Pembayaran</h3>
            <?php if ($data->getMidtransToken()): ?>
                <p class="bayar-info">Klik tombol di bawah untuk melanjutkan ke halaman pembayaran Midtrans. Anda dapat membayar melalui transfer bank, QRIS, atau dompet digital.</p>
                <button id="pay-btn" class="btn btn-primary btn-full btn-lg">Bayar Sekarang</button>
                <p class="bayar-note">🔒 Pembayaran diproses secara aman oleh Midtrans</p>
            <?php else: ?>
                <div class="alert alert-warning">
                    <strong>Token pembayaran tidak tersedia.</strong><br>
                    Silakan hubungi admin atau coba lagi nanti.
                </div>
                <a href="<?= url('/transaksi/pemesanan') ?>" class="btn btn-ghost btn-full">Kembali ke Pemesanan</a>
            <?php endif; ?>

            <div class="bayar-status-check" id="bayar-status-check" style="display:none">
                <div class="spinner"></div>
                <span>Memverifikasi pembayaran…</span>
            </div>
        </div>
    </div>
</main>
</div><!-- .layout-wrapper -->

<div id="toast-container"></div>
<script src="<?= url('08Bsui/wwwroot/js/app.js') ?>"></script>
<?php if ($data->getMidtransToken()): ?>
    <script src="https://app<?= $isProduction ? '' : '.sandbox' ?>.midtrans.com/snap/snap.js"
        data-client-key="<?= htmlspecialchars($midtransClientKey) ?>"></script>
    <script>
        document.getElementById('pay-btn').addEventListener('click', function() {
            snap.pay('<?= htmlspecialchars($data->getMidtransToken()) ?>', {
                onSuccess: function(result) {
                    showToast('Pembayaran berhasil! ✓', 'success');
                    document.getElementById('bayar-status-check').style.display = 'flex';
                    setTimeout(() => {
                        window.location.href = '<?= url('/transaksi/pemesanan') ?>';
                    }, 2000);
                },
                onPending: function(result) {
                    showToast('Menunggu konfirmasi pembayaran…', 'info');
                    setTimeout(() => {
                        window.location.href = '<?= url('/transaksi/pemesanan') ?>';
                    }, 2000);
                },
                onError: function(result) {
                    showToast('Pembayaran gagal. Silakan coba lagi.', 'error');
                },
                onClose: function() {
                    showToast('Pembayaran dibatalkan.', 'warning');
                }
            });
        });
    </script>
<?php endif; ?>
</body>

</html>