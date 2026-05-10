<?php
// transaction/laporan/index.php
$pageTitle  = 'Laporan';
$activeMenu = 'transaksi/laporan';
require BASE_PATH . '/08Bsui/layouts/app.php';

$jenis = $_GET['jenis'] ?? 'pemesanan';
$statusLabels = [
    'pending'   => ['label' => 'Menunggu Bayar', 'class' => 'badge-warning'],
    'confirmed' => ['label' => 'Terkonfirmasi',  'class' => 'badge-success'],
    'cancelled' => ['label' => 'Dibatalkan',     'class' => 'badge-danger'],
];
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">📊 Laporan</h1>
            <p class="page-desc">Ringkasan dan data laporan operasional</p>
        </div>
        <a href="<?= url('/transaksi/laporan/export?' . http_build_query($_GET)) ?>" class="btn btn-secondary">⬇ Export CSV</a>
    </div>

    <?php if ($flash ?? null): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <!-- Summary cards -->
    <div class="stat-grid">
        <div class="stat-card stat-card--green">
            <div class="stat-card-icon">✅</div>
            <div class="stat-card-body">
                <div class="stat-card-value"><?= $summary['confirmed'] ?? 0 ?></div>
                <div class="stat-card-label">Pemesanan Terkonfirmasi</div>
            </div>
        </div>
        <div class="stat-card stat-card--amber">
            <div class="stat-card-icon">⏳</div>
            <div class="stat-card-body">
                <div class="stat-card-value"><?= $summary['pending'] ?? 0 ?></div>
                <div class="stat-card-label">Menunggu Pembayaran</div>
            </div>
        </div>
        <div class="stat-card stat-card--blue">
            <div class="stat-card-icon">💰</div>
            <div class="stat-card-body">
                <div class="stat-card-value">Rp <?= number_format($summary['total_pendapatan'] ?? 0, 0, ',', '.') ?></div>
                <div class="stat-card-label">Total Pendapatan</div>
            </div>
        </div>
        <div class="stat-card stat-card--navy">
            <div class="stat-card-icon">🚌</div>
            <div class="stat-card-body">
                <div class="stat-card-value"><?= $summary['total_tiket'] ?? 0 ?></div>
                <div class="stat-card-label">Tiket Aktif</div>
            </div>
        </div>
    </div>

    <!-- Filter + Tab -->
    <div class="filter-bar" style="margin-bottom:0">
        <form method="GET" class="filter-form" style="flex-wrap:wrap;gap:8px">
            <div class="tab-switcher">
                <a href="?<?= http_build_query(array_merge($_GET, ['jenis' => 'pemesanan'])) ?>" class="tab-btn <?= $jenis === 'pemesanan' ? 'active' : '' ?>">📋 Pemesanan</a>
                <a href="?<?= http_build_query(array_merge($_GET, ['jenis' => 'tiket'])) ?>" class="tab-btn <?= $jenis === 'tiket' ? 'active' : '' ?>">🎫 Tiket</a>
            </div>
            <input type="hidden" name="jenis" value="<?= htmlspecialchars($jenis) ?>">
            <input type="date" name="tanggal" class="form-input filter-input" value="<?= htmlspecialchars($_GET['tanggal'] ?? '') ?>" placeholder="Filter tanggal">
            <?php if ($jenis === 'pemesanan'): ?>
                <select name="status" class="form-input filter-input">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending'   ? 'selected' : '' ?>>Menunggu Bayar</option>
                    <option value="confirmed" <?= ($_GET['status'] ?? '') === 'confirmed' ? 'selected' : '' ?>>Terkonfirmasi</option>
                    <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Dibatalkan</option>
                </select>
            <?php endif; ?>
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="search-input" placeholder="Cari..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="?jenis=<?= $jenis ?>" class="btn btn-ghost">Reset</a>
        </form>
    </div>

    <!-- Table Pemesanan -->
    <?php if ($jenis === 'pemesanan'): ?>
        <?php if (empty($laporanPemesanan)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">📋</div>
                <h3>Tidak ada data pemesanan</h3>
                <p>Coba ubah filter pencarian Anda</p>
            </div>
        <?php else: ?>
            <div class="table-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>No. Pemesanan</th>
                            <th>Penumpang</th>
                            <th>Tujuan</th>
                            <th>Armada</th>
                            <th>Tgl Berangkat</th>
                            <th>Kursi</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($laporanPemesanan as $i => $row): ?>
                            <tr>
                                <td class="td-num"><?= $i + 1 ?></td>
                                <td><span class="badge badge-outline"><?= htmlspecialchars($row['no_pemesanan'] ?? '—') ?></span></td>
                                <td>
                                    <div class="td-user">
                                        <div class="td-avatar"><?= strtoupper(substr($row['nama_penumpang'] ?? 'U', 0, 1)) ?></div>
                                        <?= htmlspecialchars($row['nama_penumpang'] ?? '—') ?>
                                    </div>
                                </td>
                                <td><strong><?= htmlspecialchars($row['tujuan'] ?? '—') ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_armada'] ?? '—') ?><br><small class="td-muted"><?= htmlspecialchars($row['plat_nomor'] ?? '') ?></small></td>
                                <td><?= $row['tanggal_berangkat'] ? date('d M Y', strtotime($row['tanggal_berangkat'])) : '—' ?></td>
                                <td><span class="badge badge-outline">Kursi <?= htmlspecialchars($row['no_seat'] ?? '—') ?></span></td>
                                <td>Rp <?= number_format($row['total_harga'] ?? 0, 0, ',', '.') ?></td>
                                <td>
                                    <?php $st = $row['status_pemesanan'] ?? 'pending';
                                    $bd = $statusLabels[$st] ?? ['label' => ucfirst($st), 'class' => 'badge-secondary']; ?>
                                    <span class="badge <?= $bd['class'] ?>"><?= $bd['label'] ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Table Tiket -->
    <?php else: ?>
        <?php if (empty($laporanTiket)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">🎫</div>
                <h3>Tidak ada data tiket</h3>
                <p>Coba ubah filter pencarian Anda</p>
            </div>
        <?php else: ?>
            <div class="table-card">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tujuan</th>
                            <th>Armada</th>
                            <th>Tgl Berangkat</th>
                            <th>Jam</th>
                            <th>Harga</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($laporanTiket as $i => $row): ?>
                            <tr>
                                <td class="td-num"><?= $i + 1 ?></td>
                                <td><strong><?= htmlspecialchars($row['tujuan'] ?? '—') ?></strong></td>
                                <td><?= htmlspecialchars($row['nama_armada'] ?? '—') ?><br><small class="td-muted"><?= htmlspecialchars($row['plat_nomor'] ?? '') ?></small></td>
                                <td><?= $row['tanggal_berangkat'] ? date('d M Y', strtotime($row['tanggal_berangkat'])) : '—' ?></td>
                                <td><?= $row['jam_berangkat'] ? substr($row['jam_berangkat'], 0, 5) : '—' ?></td>
                                <td>Rp <?= number_format($row['harga'] ?? 0, 0, ',', '.') ?></td>
                                <td>
                                    <span class="badge <?= $row['is_full'] ? 'badge-danger' : 'badge-success' ?>">
                                        <?= $row['is_full'] ? 'Penuh' : 'Tersedia' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</main>
</div><!-- .layout-wrapper -->

<div id="toast-container"></div>
<script src="<?= url('08Bsui/wwwroot/js/app.js') ?>"></script>
</body>

</html>