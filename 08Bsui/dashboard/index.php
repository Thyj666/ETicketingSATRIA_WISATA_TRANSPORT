<?php
// dashboard/index.php
// Role: admin | pelanggan | pimpinan
$pageTitle  = 'Dashboard';
$activeMenu = 'dashboard';
require BASE_PATH . '/08Bsui/layouts/app.php';
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">Dashboard</h1>
            <p class="page-desc">Selamat datang, <?= htmlspecialchars($nama) ?>!</p>
        </div>
        <div class="page-header-meta"><?= date('l, d F Y') ?></div>
    </div>

    <!-- Stat cards -->
    <div class="stat-grid">
        <div class="stat-card stat-card--blue">
            <div class="stat-card-icon">🎫</div>
            <div class="stat-card-body">
                <div class="stat-card-value"><?= number_format($stats['tiket'] ?? 0) ?></div>
                <div class="stat-card-label">Total Tiket Tersedia</div>
            </div>
        </div>
        <div class="stat-card stat-card--amber">
            <div class="stat-card-icon">🚌</div>
            <div class="stat-card-body">
                <div class="stat-card-value"><?= number_format($stats['armada'] ?? 0) ?></div>
                <div class="stat-card-label">Armada Beroperasi</div>
            </div>
        </div>
        <div class="stat-card stat-card--green">
            <div class="stat-card-icon">📋</div>
            <div class="stat-card-body">
                <div class="stat-card-value"><?= number_format($stats['pemesanan'] ?? 0) ?></div>
                <div class="stat-card-label">Pemesanan Hari Ini</div>
            </div>
        </div>
        <div class="stat-card stat-card--navy">
            <div class="stat-card-icon">👥</div>
            <div class="stat-card-body">
                <div class="stat-card-value"><?= number_format($stats['pelanggan'] ?? 0) ?></div>
                <div class="stat-card-label">Total Pelanggan</div>
            </div>
        </div>
    </div>

    <!-- Quick links -->
    <div class="section-title">Akses Cepat</div>
    <div class="quick-links">
        <a href="<?= url('/transaksi/tiket') ?>" class="quick-link-card">
            <div class="quick-link-icon">🎫</div>
            <div class="quick-link-label">Lihat Tiket</div>
        </a>

        <?php if (in_array($role, ['admin', 'pelanggan'])): ?>
            <a href="<?= url('/transaksi/pemesanan') ?>" class="quick-link-card">
                <div class="quick-link-icon">📋</div>
                <div class="quick-link-label">
                    <?= $role === 'pelanggan' ? 'Pemesanan Saya' : 'Kelola Pemesanan' ?>
                </div>
            </a>
        <?php endif; ?>

        <?php if (in_array($role, ['admin', 'pimpinan'])): ?>
            <a href="<?= url('/transaksi/laporan') ?>" class="quick-link-card">
                <div class="quick-link-icon">📊</div>
                <div class="quick-link-label">Laporan</div>
            </a>
        <?php endif; ?>

        <?php if ($role === 'admin'): ?>
            <a href="<?= url('/master/armada') ?>" class="quick-link-card">
                <div class="quick-link-icon">🚌</div>
                <div class="quick-link-label">Kelola Armada</div>
            </a>
        <?php endif; ?>

        <a href="<?= url('/profile') ?>" class="quick-link-card">
            <div class="quick-link-icon">⚙️</div>
            <div class="quick-link-label">Profil Saya</div>
        </a>
    </div>
</main>
</div><!-- .layout-wrapper -->

<div id="toast-container"></div>
<script src="<?= url('08Bsui/wwwroot/js/app.js') ?>"></script>
</body>

</html>