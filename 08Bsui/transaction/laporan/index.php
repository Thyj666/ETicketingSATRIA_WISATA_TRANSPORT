<?php
$title     = 'Laporan';
$pageTitle = 'Laporan Penggajian & Absensi';
$breadcrumbs = ['Transaksi', 'Laporan'];

function rpL(float $v): string
{
    return 'Rp ' . number_format($v, 0, ',', '.');
}

$jenis   = $_GET['jenis']   ?? 'gaji';
$periode = $_GET['periode'] ?? date('Y-m');
$periodeLabel = date('F Y', strtotime($periode . '-01'));

ob_start();
?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="bi bi-bar-chart-line me-2 text-primary"></i>Laporan</h4>
        <p>Laporan rekapitulasi penggajian dan kehadiran pegawai</p>
    </div>
    <a href="<?= base('/transaksi/laporan/export?periode=' . urlencode($periode) . '&jenis=' . urlencode($jenis)) ?>"
        class="btn btn-success">
        <i class="bi bi-download me-1"></i>Export CSV
    </a>
</div>

<!-- FILTER -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="month" name="periode" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($periode) ?>">
            </div>
            <div class="col-md-3">
                <select name="jenis" class="form-select form-select-sm">
                    <option value="gaji" <?= $jenis === 'gaji'    ? 'selected' : '' ?>>Laporan Penggajian</option>
                    <option value="absensi" <?= $jenis === 'absensi' ? 'selected' : '' ?>>Laporan Absensi</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search me-1"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- TAB -->
<ul class="nav nav-tabs mb-3">
    <li class="nav-item">
        <a class="nav-link <?= $jenis === 'gaji' ? 'active' : '' ?>"
            href="?periode=<?= urlencode($periode) ?>&jenis=gaji">
            <i class="bi bi-cash-coin me-1"></i>Penggajian
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= $jenis === 'absensi' ? 'active' : '' ?>"
            href="?periode=<?= urlencode($periode) ?>&jenis=absensi">
            <i class="bi bi-calendar-check me-1"></i>Absensi
        </a>
    </li>
</ul>

<?php if ($jenis === 'gaji'): ?>
    <!-- =============== LAPORAN GAJI =============== -->
    <?php
    $totalGajiPokok   = array_sum(array_column($laporanGaji, 'gaji_pokok'));
    $totalTunjangan   = array_sum(array_column($laporanGaji, 'tunjangan'));
    $totalPotonganA   = array_sum(array_column($laporanGaji, 'potongan_absensi'));
    $totalPotonganL   = array_sum(array_column($laporanGaji, 'potongan_lain'));
    $totalGaji        = array_sum(array_column($laporanGaji, 'total_gaji'));
    $totalDibayar     = count(array_filter($laporanGaji, fn($r) => $r['status'] === 'dibayar'));
    $totalPending     = count($laporanGaji) - $totalDibayar;
    ?>

    <!-- SUMMARY GAJI -->
    <div class="row g-3 mb-3">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-primary fw-bold fs-5"><?= count($laporanGaji) ?></div>
                <div class="small text-muted">Pegawai</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-success fw-bold"><?= rpL($totalGaji) ?></div>
                <div class="small text-muted">Total Gaji</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-success fw-bold"><?= $totalDibayar ?></div>
                <div class="small text-muted">Sudah Dibayar</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-warning fw-bold"><?= $totalPending ?></div>
                <div class="small text-muted">Pending</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Laporan Penggajian &bull;
                <span class="text-muted fw-normal fs-6"><?= $periodeLabel ?></span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableGaji">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>Golongan</th>
                            <th class="text-end">Gaji Pokok</th>
                            <th class="text-end">Tunjangan</th>
                            <th class="text-end">Pot. Absensi</th>
                            <th class="text-end">Pot. Lain</th>
                            <th class="text-end">Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporanGaji)): ?>
                            <tr>
                                <td colspan="11" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>Tidak ada data
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporanGaji as $i => $row): ?>
                                <tr>
                                    <td class="text-muted small"><?= $i + 1 ?></td>
                                    <td class="fw-semibold small"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($row['nip'] ?? '-') ?></td>
                                    <td class="small"><?= htmlspecialchars($row['nama_jabatan'] ?? '-') ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($row['kode_golongan'] ?? '-') ?></td>
                                    <td class="text-end small"><?= rpL((float)$row['gaji_pokok']) ?></td>
                                    <td class="text-end small text-success"><?= rpL((float)$row['tunjangan']) ?></td>
                                    <td class="text-end small text-danger"><?= rpL((float)$row['potongan_absensi']) ?></td>
                                    <td class="text-end small text-danger"><?= rpL((float)$row['potongan_lain']) ?></td>
                                    <td class="text-end fw-bold small"><?= rpL((float)$row['total_gaji']) ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'dibayar'): ?>
                                            <span class="badge bg-success">Dibayar</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($laporanGaji)): ?>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="5" class="text-end">TOTAL</td>
                                <td class="text-end small"><?= rpL($totalGajiPokok) ?></td>
                                <td class="text-end small text-success"><?= rpL($totalTunjangan) ?></td>
                                <td class="text-end small text-danger"><?= rpL($totalPotonganA) ?></td>
                                <td class="text-end small text-danger"><?= rpL($totalPotonganL) ?></td>
                                <td class="text-end"><?= rpL($totalGaji) ?></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

<?php else: ?>
    <!-- =============== LAPORAN ABSENSI =============== -->
    <?php
    $totalHadir = array_sum(array_column($laporanAbsensi, 'hadir'));
    $totalIzin  = array_sum(array_column($laporanAbsensi, 'izin'));
    $totalSakit = array_sum(array_column($laporanAbsensi, 'sakit'));
    $totalAlpha = array_sum(array_column($laporanAbsensi, 'alpha'));
    $totalPotAbsensi = array_sum(array_column($laporanAbsensi, 'total_potongan'));
    ?>
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-success fw-bold fs-5"><?= $totalHadir ?></div>
                <div class="small text-muted"><i class="bi bi-check-circle me-1"></i>Total Hadir</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-warning fw-bold fs-5"><?= $totalIzin ?></div>
                <div class="small text-muted"><i class="bi bi-calendar-x me-1"></i>Total Izin</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-info fw-bold fs-5"><?= $totalSakit ?></div>
                <div class="small text-muted"><i class="bi bi-heart-pulse me-1"></i>Total Sakit</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm text-center py-3">
                <div class="text-danger fw-bold fs-5"><?= $totalAlpha ?></div>
                <div class="small text-muted"><i class="bi bi-x-circle me-1"></i>Total Alpha</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Laporan Absensi &bull;
                <span class="text-muted fw-normal fs-6"><?= $periodeLabel ?></span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tableLaporanAbsensi">
                    <thead class="table-light">
                        <tr>
                            <th style="width:40px">#</th>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th class="text-center">Hadir</th>
                            <th class="text-center">Izin</th>
                            <th class="text-center">Sakit</th>
                            <th class="text-center">Alpha</th>
                            <th class="text-center">Total Hari</th>
                            <th class="text-end">Potongan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($laporanAbsensi)): ?>
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>Tidak ada data
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($laporanAbsensi as $i => $row): ?>
                                <tr>
                                    <td class="text-muted small"><?= $i + 1 ?></td>
                                    <td class="fw-semibold small"><?= htmlspecialchars($row['nama']) ?></td>
                                    <td class="small text-muted"><?= htmlspecialchars($row['nip'] ?? '-') ?></td>
                                    <td class="small"><?= htmlspecialchars($row['nama_jabatan'] ?? '-') ?></td>
                                    <td class="text-center"><span class="badge bg-success"><?= $row['hadir'] ?? 0 ?></span></td>
                                    <td class="text-center"><span class="badge bg-warning text-dark"><?= $row['izin'] ?? 0 ?></span></td>
                                    <td class="text-center"><span class="badge bg-info text-dark"><?= $row['sakit'] ?? 0 ?></span></td>
                                    <td class="text-center"><span class="badge bg-danger"><?= $row['alpha'] ?? 0 ?></span></td>
                                    <td class="text-center small"><?= ($row['hadir'] ?? 0) + ($row['izin'] ?? 0) + ($row['sakit'] ?? 0) + ($row['alpha'] ?? 0) ?></td>
                                    <td class="text-end small <?= $row['total_potongan'] > 0 ? 'text-danger fw-semibold' : 'text-muted' ?>">
                                        <?= $row['total_potongan'] > 0 ? rpL((float)$row['total_potongan']) : '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                    <?php if (!empty($laporanAbsensi)): ?>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="4" class="text-end">TOTAL</td>
                                <td class="text-center"><?= $totalHadir ?></td>
                                <td class="text-center"><?= $totalIzin ?></td>
                                <td class="text-center"><?= $totalSakit ?></td>
                                <td class="text-center"><?= $totalAlpha ?></td>
                                <td class="text-center"><?= $totalHadir + $totalIzin + $totalSakit + $totalAlpha ?></td>
                                <td class="text-end text-danger"><?= rpL($totalPotAbsensi) ?></td>
                            </tr>
                        </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<script>
    $(document).ready(function() {
        <?php if (!empty($laporanGaji)): ?>
            $('#tableGaji').DataTable({
                info: false,
                processing: true,
                pagingType: 'simple_numbers',
                language: {
                    paginate: {
                        first: '«',
                        previous: '‹',
                        next: '›',
                        last: '»'
                    }
                },
                lengthChange: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25],
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }],
                dom: "<'row align-items-center mb-3'<'col'f><'col-auto ps-3'l><'col-auto d-flex gap-2 me-2'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 d-flex justify-content-center'p>>",
                buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel"></i>',
                        titleAttr: 'Export Excel',
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i>',
                        titleAttr: 'Print',
                        className: 'btn btn-sm btn-secondary'
                    }
                ]
            });
        <?php endif; ?>

        <?php if (!empty($laporanAbsensi)): ?>
            $('#tableLaporanAbsensi').DataTable({
                info: false,
                processing: true,
                pagingType: 'simple_numbers',
                language: {
                    paginate: {
                        first: '«',
                        previous: '‹',
                        next: '›',
                        last: '»'
                    }
                },
                lengthChange: true,
                pageLength: 10,
                lengthMenu: [5, 10, 25],
                order: [
                    [1, 'asc']
                ],
                columnDefs: [{
                    orderable: false,
                    targets: -1
                }],
                dom: "<'row align-items-center mb-3'<'col'f><'col-auto ps-3'l><'col-auto d-flex gap-2 me-2'B>>" +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row mt-3'<'col-sm-12 col-md-5'i><'col-sm-12 d-flex justify-content-center'p>>",
                buttons: [{
                        extend: 'excelHtml5',
                        text: '<i class="bi bi-file-earmark-excel"></i>',
                        titleAttr: 'Export Excel',
                        className: 'btn btn-sm btn-success'
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i>',
                        titleAttr: 'Print',
                        className: 'btn btn-sm btn-secondary'
                    }
                ]
            });
        <?php endif; ?>
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/08Bsui/layouts/app.php';
?>