<?php
// transaction/pemesanan/index.php
$pageTitle  = 'Pemesanan';
$activeMenu = 'transaksi/pemesanan';
require BASE_PATH . '/08Bsui/layouts/app.php';

$isAdmin = $role === 'admin';
$statusLabels = [
    'pending'   => ['label' => 'Menunggu Bayar', 'class' => 'badge-warning'],
    'confirmed' => ['label' => 'Terkonfirmasi',  'class' => 'badge-success'],
    'cancelled' => ['label' => 'Dibatalkan',     'class' => 'badge-danger'],
];
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">📋 Pemesanan</h1>
            <p class="page-desc"><?= $isAdmin ? 'Daftar seluruh pemesanan tiket' : 'Riwayat pemesanan Anda' ?></p>
        </div>
        <?php if (!$isAdmin): ?>
            <a href="<?= url('/transaksi/tiket') ?>" class="btn btn-primary">🎫 Pesan Tiket Baru</a>
        <?php endif; ?>
    </div>

    <?php if ($flash ?? null): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- Filter -->
    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <select name="status" class="form-input filter-input">
                <option value="">Semua Status</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending'   ? 'selected' : '' ?>>Menunggu Bayar</option>
                <option value="confirmed" <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Terkonfirmasi</option>
                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="<?= url('/transaksi/pemesanan') ?>" class="btn btn-ghost">Reset</a>
        </form>
    </div>

    <?php if (empty($list)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">📋</div>
            <h3>Belum ada pemesanan</h3>
            <p><?= $isAdmin ? 'Belum ada data pemesanan masuk.' : 'Anda belum pernah memesan tiket.' ?></p>
            <?php if (!$isAdmin): ?>
                <a href="<?= url('/transaksi/tiket') ?>" class="btn btn-primary" style="margin-top:12px">Lihat Tiket Tersedia</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Pemesanan</th>
                        <?php if ($isAdmin): ?><th>Penumpang</th><?php endif; ?>
                        <th>Tujuan</th>
                        <th>Tgl Berangkat</th>
                        <th>Kursi</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="th-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $i => $p):
                        $st = $p->getStatusPemesanan() ?? 'pending';
                        $badge = $statusLabels[$st] ?? ['label' => ucfirst($st), 'class' => 'badge-secondary'];
                        $tiket = $p->getTiket();
                        $tgl   = $tiket?->getTanggalBerangkat() ? date('d M Y', strtotime($tiket->getTanggalBerangkat())) : '—';
                        $total = 'Rp ' . number_format($p->getTotalHarga(), 0, ',', '.');
                    ?>
                        <tr>
                            <td class="td-num"><?= $i + 1 ?></td>
                            <td><span class="badge badge-outline"><?= htmlspecialchars($p->getNoPemesanan()) ?></span></td>
                            <?php if ($isAdmin): ?>
                                <td>
                                    <div class="td-user">
                                        <div class="td-avatar"><?= strtoupper(substr($p->getUser()?->getNama() ?? 'U', 0, 1)) ?></div>
                                        <?= htmlspecialchars($p->getUser()?->getNama() ?? '—') ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                            <td><strong><?= htmlspecialchars($tiket?->getTujuan() ?? '—') ?></strong></td>
                            <td><?= $tgl ?></td>
                            <td><span class="badge badge-outline">Kursi <?= htmlspecialchars($p->getNoSeat()) ?></span></td>
                            <td><?= $total ?></td>
                            <td><span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span></td>
                            <td class="td-action">
                                <?php if ($st === 'pending' && !$isAdmin): ?>
                                    <a href="<?= url('/transaksi/pemesanan/bayar?id=' . $p->getId()) ?>" class="btn btn-primary btn-sm">💳 Bayar</a>
                                <?php endif; ?>
                                <?php if ($isAdmin && $st === 'pending'): ?>
                                    <form method="POST" action="<?= url('/transaksi/pemesanan/konfirmasi') ?>" data-confirm="Konfirmasi pembayaran ini?" style="display:inline">
                                        <input type="hidden" name="id" value="<?= $p->getId() ?>">
                                        <button class="btn btn-success btn-sm">✓ Konfirmasi</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
</div><!-- .layout-wrapper -->

<div id="toast-container"></div>
<script src="<?= url('08Bsui/wwwroot/js/app.js') ?>"></script>
</body>

</html>