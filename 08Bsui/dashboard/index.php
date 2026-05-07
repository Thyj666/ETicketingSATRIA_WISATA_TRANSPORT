<?php
$title = 'Dashboard';
$pageTitle = 'Dashboard';
$breadcrumbs = ['Dashboard'];
$user = \Base\Auth\Auth::user() ?? [];
$role = $user['role'] ?? '';

// Format rupiah
function rp(float $v): string
{
    return 'Rp ' . number_format($v, 0, ',', '.');
}

ob_start();
?>

<div class="page-header">
    <h4>Selamat Datang, <?= htmlspecialchars($user['nama'] ?? '') ?>! 👋</h4>
    <p>
        <?= htmlspecialchars($user['jabatan'] ?? '') ?> &bull;
        <?= date('l, d F Y') ?>
    </p>
</div>

<?php if (in_array($role, ['admin_tu', 'kepala_sekolah'])): ?>
    <!-- STAT CARDS -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#2a5298,#1e3a5f)">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-value"><?= $stats['total_guru'] ?></div>
                        <div class="stat-label">Total Guru</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-mortarboard"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#0f766e,#0d5954)">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-value"><?= $stats['total_staff'] ?></div>
                        <div class="stat-label">Total Staff TU</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-person-badge"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#d97706,#b45309)">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-value"><?= $stats['gaji_pending'] ?></div>
                        <div class="stat-label">Gaji Pending</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="stat-card" style="background:linear-gradient(135deg,#7c3aed,#5b21b6)">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-value" style="font-size:1.1rem"><?= rp($stats['total_gaji']) ?></div>
                        <div class="stat-label">Total Gaji Bulan Ini</div>
                    </div>
                    <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- QUICK ACCESS -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center gap-2">
                    <i class="bi bi-lightning-charge text-warning"></i>
                    <h5>Akses Cepat</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <?php if ($role === 'admin_tu'): ?>
                            <div class="col-6">
                                <a href="<?= base('/master/user') ?>" class="btn btn-outline-primary w-100 text-start py-3">
                                    <i class="bi bi-person-plus me-2"></i>Tambah Pegawai
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= base('/transaksi/absensi') ?>" class="btn btn-outline-success w-100 text-start py-3">
                                    <i class="bi bi-calendar-plus me-2"></i>Input Absensi
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= base('/transaksi/penggajian') ?>" class="btn btn-outline-warning w-100 text-start py-3">
                                    <i class="bi bi-cash me-2"></i>Proses Gaji
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="<?= base('/transaksi/laporan') ?>" class="btn btn-outline-info w-100 text-start py-3">
                                    <i class="bi bi-file-earmark me-2"></i>Laporan
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="col-12">
                                <a href="<?= base('/transaksi/laporan') ?>" class="btn btn-outline-primary w-100 text-start py-3">
                                    <i class="bi bi-file-earmark-bar-graph me-2"></i>Lihat Laporan Gaji
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-calendar-check text-success"></i>
                        <h5>Absensi Terkini</h5>
                    </div>
                    <a href="<?= base('/transaksi/absensi') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($recentAbsensi)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-calendar-x fs-3 d-block mb-2"></i>
                            Belum ada data absensi bulan ini
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-sm mb-0" id="tableDashAbsensi">
                                <thead>
                                    <tr>
                                        <th>Pegawai</th>
                                        <th>Tanggal</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach (array_slice($recentAbsensi, 0, 6) as $ab): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($ab->getNamaUser() ?? '-') ?></td>
                                            <td><?= date('d/m', strtotime($ab->getTanggal())) ?></td>
                                            <td>
                                                <?php $s = $ab->getStatus(); ?>
                                                <span class="badge <?= ['hadir' => 'bg-success', 'izin' => 'bg-info', 'sakit' => 'bg-warning text-dark', 'alpha' => 'bg-danger'][$s] ?? 'bg-secondary' ?>">
                                                    <?= ucfirst($s) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- PEGAWAI DASHBOARD -->
    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-person-circle me-2 text-primary"></i>Informasi Saya</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm mb-0" id="tableDashGaji">
                        <tr>
                            <td class="text-muted" style="width:40%">Nama</td>
                            <td><?= htmlspecialchars($user['nama']) ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Jabatan</td>
                            <td><?= htmlspecialchars($user['jabatan'] ?? '-') ?></td>
                        </tr>
                        <tr>
                            <td class="text-muted">Role</td>
                            <td><span class="badge bg-primary"><?= $user['role'] ?></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="bi bi-link-45deg me-2 text-warning"></i>Menu Saya</h5>
                </div>
                <div class="card-body">
                    <a href="<?= base('/transaksi/absensi') ?>" class="btn btn-outline-success w-100 mb-2 text-start">
                        <i class="bi bi-calendar-check me-2"></i>Lihat Absensi Saya
                    </a>
                    <a href="<?= base('/transaksi/penggajian') ?>" class="btn btn-outline-primary w-100 mb-2 text-start">
                        <i class="bi bi-cash me-2"></i>Slip Gaji Saya
                    </a>
                    <a href="<?= base('/profile') ?>" class="btn btn-outline-secondary w-100 text-start">
                        <i class="bi bi-person-gear me-2"></i>Profil Saya
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    $(document).ready(function() {
        if ($('#tableDashAbsensi tbody tr').length > 0 && !$('#tableDashAbsensi tbody td[colspan]').length) {
            $('#tableDashAbsensi').DataTable({
                info: false,
                pagingType: 'simple_numbers',
                language: {
                    paginate: {
                        first: '«',
                        previous: '‹',
                        next: '›',
                        last: '»'
                    }
                },
                pageLength: 10,
                lengthMenu: [5, 10, 25],
                order: [
                    [0, 'asc']
                ],
                dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });
        }

        if ($('#tableDashGaji tbody tr').length > 0 && !$('#tableDashGaji tbody td[colspan]').length) {
            $('#tableDashGaji').DataTable({
                info: false,
                pagingType: 'simple_numbers',
                language: {
                    paginate: {
                        first: '«',
                        previous: '‹',
                        next: '›',
                        last: '»'
                    }
                },
                pageLength: 10,
                lengthMenu: [5, 10, 25],
                order: [
                    [0, 'asc']
                ],
                dom: "<'row'<'col-sm-12 col-md-6'><'col-sm-12 col-md-6'f>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
            });
        }
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/08Bsui/layouts/app.php';
?>