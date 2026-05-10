<?php
// layouts/app.php — master layout
// Usage: include this at the top of each page view, set $pageTitle before including
// Role: admin | pelanggan | pimpinan
$pageTitle  = $pageTitle  ?? 'Satria Wisata Transport';
$activeMenu = $activeMenu ?? '';
$user       = \Base\Auth\Auth::user();
$role       = $user['role'] ?? '';
$nama       = $user['nama'] ?? 'User';
$initials   = strtoupper(substr($nama, 0, 1));

function menuItem(string $href, string $icon, string $label, string $active): string
{
    $cls = (str_starts_with($active, ltrim($href, '/'))) ? ' active' : '';
    return '<a href="' . url($href) . '" class="nav-link' . $cls . '"><span class="nav-icon">' . $icon . '</span><span class="nav-label">' . $label . '</span></a>';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — Satria Wisata</title>
    <link rel="stylesheet" href="<?= url('08Bsui/wwwroot/css/app.css') ?>">
</head>

<body>
    <?php if (isset($flash) && $flash): ?>
        <script>
            window.__flash = <?= json_encode(['type' => $flash['type'], 'msg' => $flash['msg']]) ?>;
        </script>
    <?php endif; ?>

    <!-- Sidebar overlay (mobile) -->
    <div class="sidebar-overlay" id="sidebar-overlay"></div>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">🚌</div>
            <div class="sidebar-brand-text">
                <div class="sidebar-brand-name">Satria Wisata</div>
                <div class="sidebar-brand-sub">Transport</div>
            </div>
            <button class="sidebar-close-btn" id="btn-sidebar-close" aria-label="Tutup sidebar">✕</button>
        </div>

        <div class="sidebar-user">
            <div class="sidebar-avatar"><?= $initials ?></div>
            <div class="sidebar-user-info">
                <div class="sidebar-user-name"><?= htmlspecialchars($nama) ?></div>
                <div class="sidebar-user-role"><?= ucfirst($role) ?></div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?= menuItem('/dashboard', '🏠', 'Dashboard', $activeMenu) ?>

            <?php if ($role === 'admin'): ?>
                <div class="nav-section-title">Master Data</div>
                <?= menuItem('/master/admin',    '👤', 'Admin',    $activeMenu) ?>
                <?= menuItem('/master/pelanggan', '👥', 'Pelanggan', $activeMenu) ?>
                <?= menuItem('/master/pimpinan',  '🏆', 'Pimpinan',  $activeMenu) ?>
                <?= menuItem('/master/armada',    '🚌', 'Armada',    $activeMenu) ?>
                <?= menuItem('/master/user',      '🔑', 'User',      $activeMenu) ?>
            <?php endif; ?>

            <div class="nav-section-title">Transaksi</div>
            <?= menuItem('/transaksi/tiket', '🎫', 'Tiket', $activeMenu) ?>

            <?php if (in_array($role, ['admin', 'pelanggan'])): ?>
                <?= menuItem('/transaksi/pemesanan', '📋', 'Pemesanan', $activeMenu) ?>
            <?php endif; ?>

            <?php if (in_array($role, ['admin', 'pimpinan'])): ?>
                <?= menuItem('/transaksi/laporan', '📊', 'Laporan', $activeMenu) ?>
            <?php endif; ?>

            <div class="nav-section-title">Akun</div>
            <?= menuItem('/profile', '⚙️', 'Profil', $activeMenu) ?>
            <a href="<?= url('/logout') ?>" class="nav-link nav-link-danger"><span class="nav-icon">🚪</span><span class="nav-label">Keluar</span></a>
        </nav>
    </aside>

    <!-- Main layout -->
    <div class="layout-wrapper">
        <!-- Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button class="topbar-toggle" id="btn-sidebar-open" aria-label="Buka sidebar">
                    <span></span><span></span><span></span>
                </button>
                <div class="topbar-breadcrumb">
                    <span class="breadcrumb-home">🏠</span>
                    <?php if ($pageTitle !== 'Dashboard'): ?><span class="breadcrumb-sep">›</span><span class="breadcrumb-current"><?= htmlspecialchars($pageTitle) ?></span><?php endif; ?>
                </div>
            </div>
            <div class="topbar-right">
                <div class="topbar-user">
                    <div class="topbar-avatar"><?= $initials ?></div>
                    <div class="topbar-user-info">
                        <div class="topbar-user-name"><?= htmlspecialchars($nama) ?></div>
                        <div class="topbar-user-role"><?= ucfirst($role) ?></div>
                    </div>
                </div>
            </div>
        </header>
