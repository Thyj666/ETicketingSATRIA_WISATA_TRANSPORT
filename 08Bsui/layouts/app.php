<?php
// layouts/app.php - Main layout wrapper
// Usage: set $title, $pageTitle, $breadcrumbs before including
$user = \Base\Auth\Auth::user() ?? [];
$role = $user['role'] ?? '';
$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');

$roleLabels = [
    'admin_tu'       => 'Admin TU',
    'kepala_sekolah' => 'Kepala Sekolah',
    'guru'           => 'Guru',
    'staff'          => 'Staff TU',
];

function isActive(string $path): string
{
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $basePath   = dirname($scriptName);
    $currentPath = str_replace($basePath, '', strtok($requestUri, '?'));
    return str_starts_with($currentPath, $path) ? 'active' : '';
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'SIMPEG') ?> - SMAN 7 Bungo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- DataTables -->
    <link href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/3.0.2/css/buttons.bootstrap5.min.css" rel="stylesheet">
    <!-- jQuery (harus di head agar tersedia saat konten dirender) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/3.0.2/js/buttons.print.min.js"></script>
    <link href="<?= base('/08Bsui/wwwroot/css/app.css') ?>" rel="stylesheet">
</head>

<body>

    <!-- SIDEBAR -->
    <nav id="sidebar">
        <div class="sidebar-brand d-flex align-items-center gap-3">
            <div class="brand-logo">S7</div>
            <div class="brand-text">
                <h6>SIMPEG</h6>
                <small>SMAN 7 Bungo</small>
            </div>
        </div>

        <div class="mt-2">
            <div class="nav-label">Menu Utama</div>
            <a href="<?= base('/dashboard') ?>" class="nav-link <?= isActive('/dashboard') ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <?php if (in_array($role, ['admin_tu', 'kepala_sekolah'])): ?>
                <?php if ($role === 'admin_tu'): ?>
                    <div class="nav-label">Master Data</div>

                    <a href="<?= base('/master/user') ?>" class="nav-link <?= isActive('/master/user') ?>">
                        <i class="bi bi-people"></i> Kelola Pegawai
                    </a>
                    <a href="<?= base('/master/jabatan') ?>" class="nav-link <?= isActive('/master/jabatan') ?>">
                        <i class="bi bi-briefcase"></i> Jabatan
                    </a>
                    <a href="<?= base('/master/golongan') ?>" class="nav-link <?= isActive('/master/golongan') ?>">
                        <i class="bi bi-layers"></i> Golongan &amp; Gaji
                    </a>
                <?php endif; ?>

                <div class="nav-label">Transaksi</div>
                <a href="<?= base('/transaksi/absensi') ?>" class="nav-link <?= isActive('/transaksi/absensi') ?>">
                    <i class="bi bi-calendar-check"></i> Absensi
                </a>
                <a href="<?= base('/transaksi/penggajian') ?>" class="nav-link <?= isActive('/transaksi/penggajian') ?>">
                    <i class="bi bi-cash-stack"></i> Penggajian
                </a>
                <a href="<?= base('/transaksi/laporan') ?>" class="nav-link <?= isActive('/transaksi/laporan') ?>">
                    <i class="bi bi-file-earmark-bar-graph"></i> Laporan
                </a>
            <?php else: ?>
                <!-- Guru & Staff: lihat data diri sendiri -->
                <div class="nav-label">Transaksi Saya</div>
                <a href="<?= base('/transaksi/absensi') ?>" class="nav-link <?= isActive('/transaksi/absensi') ?>">
                    <i class="bi bi-calendar-check"></i> Absensi Saya
                </a>
                <a href="<?= base('/transaksi/penggajian') ?>" class="nav-link <?= isActive('/transaksi/penggajian') ?>">
                    <i class="bi bi-cash-stack"></i> Slip Gaji Saya
                </a>
            <?php endif; ?>

            <div class="nav-label">Akun</div>
            <a href="<?= base('/profile') ?>" class="nav-link <?= isActive('/profile') ?>">
                <i class="bi bi-person-circle"></i> Profil Saya
            </a>
            <a href="<?= base('/logout') ?>" class="nav-link text-danger">
                <i class="bi bi-box-arrow-right"></i> Keluar
            </a>
        </div>

        <div class="sidebar-footer">
            <div class="user-info d-flex align-items-center gap-2">
                <div class="rounded-circle bg-accent d-flex align-items-center justify-content-center"
                    style="width:34px;height:34px;background:var(--accent);color:var(--primary);font-weight:700;font-size:.85rem;flex-shrink:0">
                    <?= strtoupper(substr(\Base\Auth\Auth::getName() ?? 'U', 0, 1)) ?>
                </div>
                <div>
                    <strong><?= htmlspecialchars(\Base\Auth\Auth::getName() ?? '') ?></strong>
                    <span><?= $roleLabels[htmlspecialchars(\Base\Auth\Auth::getRole() ?? '')] ?></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- TOPBAR -->
    <header id="topbar">
        <button class="btn btn-sm btn-light me-3 d-md-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
            <i class="bi bi-list"></i>
        </button>
        <div>
            <div class="page-title"><?= htmlspecialchars($pageTitle ?? '') ?></div>
            <?php if (!empty($breadcrumbs)): ?>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <?php foreach ($breadcrumbs as $i => $bc): ?>
                            <?php if ($i < count($breadcrumbs) - 1): ?>
                                <li class="breadcrumb-item"><a href="#"><?= htmlspecialchars($bc) ?></a></li>
                            <?php else: ?>
                                <li class="breadcrumb-item active"><?= htmlspecialchars($bc) ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ol>
                </nav>
            <?php endif; ?>
        </div>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="badge bg-primary-subtle text-primary fs-7" style="font-size:.72rem">
                <?= date('l, d F Y') ?>
            </span>
        </div>
    </header>

    <!-- CONTENT -->
    <main id="content">
        <?= $content ?? '' ?>
    </main>

    <script src="<?= base('/08Bsui/wwwroot/js/app.js') ?>"></script>
</body>

</html>