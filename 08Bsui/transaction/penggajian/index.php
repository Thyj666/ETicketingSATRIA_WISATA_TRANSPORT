<?php
$title     = 'Penggajian';
$pageTitle = 'Transaksi Penggajian';
$breadcrumbs = ['Transaksi', 'Penggajian'];

function rpG(float $v): string
{
    return 'Rp ' . number_format($v, 0, ',', '.');
}

// Helper untuk format nominal compact (M, B, T)
function rpCompact(float $v): string
{
    if ($v >= 1000000000) {
        return 'Rp ' . number_format($v / 1000000000, 1) . 'M';
    }
    if ($v >= 1000000) {
        return 'Rp ' . number_format($v / 1000000, 1) . 'Jt';
    }
    return rpG($v);
}

$user = \Base\Auth\Auth::user() ?? [];
$role      = $user['role'] ?? '';
$isPegawai = !in_array($role, ['admin_tu', 'kepala_sekolah']);

ob_start();
?>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
<style>
    .select2-container {
        width: 100% !important;
    }

    .select2-container--bootstrap-5 .select2-selection {
        min-height: 38px;
    }
</style>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex align-items-center gap-2">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <?= htmlspecialchars($flash['msg']) ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
    <div>
        <h4 class="mb-1"><i class="bi bi-cash-coin me-2 text-primary"></i>
            <?= $isPegawai ? 'Slip Gaji Saya' : 'Penggajian' ?>
        </h4>
        <p class="text-muted mb-0">
            <?= $isPegawai ? 'Riwayat penggajian Anda' : 'Kelola data penggajian guru dan staff tata usaha' ?>
        </p>
    </div>

    <?php if ($role === 'admin_tu'): ?>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalBulk">
            <i class="bi bi-plus-lg me-1"></i>Tambah Penggajian
        </button>
    <?php endif; ?>
</div>

<!-- FILTER -->
<div class="card mb-4 border-0 shadow-sm">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Periode</label>
                <input type="month" name="periode" class="form-control"
                    value="<?= htmlspecialchars($_GET['periode'] ?? date('Y-m')) ?>">
            </div>
            <?php if (!$isPegawai): ?>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Pegawai</label>
                    <select name="user_id" class="form-select" id="filterUserId">
                        <option value="">Semua Pegawai</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u->getId() ?>"
                                <?= (isset($_GET['user_id']) && $_GET['user_id'] == $u->getId()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u->getNama()) ?> (<?= htmlspecialchars($u->getNamaJabatan() ?? '-') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <div class="col-md-3">
                <label class="form-label fw-semibold small">Status</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="dibayar" <?= ($_GET['status'] ?? '') === 'dibayar' ? 'selected' : '' ?>>Dibayar</option>
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Filter
                    </button>
                    <a href="<?= base('/transaksi/penggajian') ?>" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- SUMMARY CARDS -->
<?php
$totalGaji    = array_sum(array_map(fn($p) => $p->getTotalGaji(), $list));
$totalPending = count(array_filter($list, fn($p) => $p->getStatus() === 'pending'));
$totalDibayar = count(array_filter($list, fn($p) => $p->getStatus() === 'dibayar'));
$totalPotongan = array_sum(array_map(fn($p) => $p->getPotonganAbsensi() + $p->getPotonganLain(), $list));
?>
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase">Total Data</span>
                        <h3 class="mb-0 mt-1"><?= count($list) ?></h3>
                    </div>
                    <div class="rounded-circle bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-people text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase">Total Gaji</span>
                        <h3 class="mb-0 mt-1 fs-5"><?= rpG($totalGaji) ?></h3>
                    </div>
                    <div class="rounded-circle bg-success bg-opacity-10 p-3">
                        <i class="bi bi-cash-stack text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase">Total Potongan</span>
                        <h3 class="mb-0 mt-1 fs-5"><?= rpG($totalPotongan) ?></h3>
                    </div>
                    <div class="rounded-circle bg-danger bg-opacity-10 p-3">
                        <i class="bi bi-dash-circle text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <span class="text-muted small text-uppercase">Status</span>
                        <div class="mt-1">
                            <span class="badge bg-warning text-dark me-1">Pending <?= $totalPending ?></span>
                            <span class="badge bg-success">Dibayar <?= $totalDibayar ?></span>
                        </div>
                    </div>
                    <div class="rounded-circle bg-info bg-opacity-10 p-3">
                        <i class="bi bi-hourglass-split text-info fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TABLE -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0">
            <i class="bi bi-table me-2 text-primary"></i>Daftar Penggajian
            <span class="text-muted fw-normal fs-6">
                • <?= date('F Y', strtotime(($_GET['periode'] ?? date('Y-m')) . '-01')) ?>
            </span>
        </h5>
        <span class="badge bg-primary"><?= count($list) ?> data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablePenggajian">
                <thead class="table-light">
                    <tr class="small">
                        <th style="width:45px" class="ps-3">#</th>
                        <?php if (!$isPegawai): ?>
                            <th>Pegawai</th>
                            <th>Jabatan</th>
                        <?php endif; ?>
                        <th class="text-end" style="min-width:110px">Gaji Pokok</th>
                        <th class="text-end" style="min-width:100px">Tunjangan</th>
                        <th class="text-end" style="min-width:100px">Pot. Absensi</th>
                        <th class="text-end" style="min-width:100px">Pot. Lain</th>
                        <th class="text-end" style="min-width:110px">Total Gaji</th>
                        <th style="min-width:90px">Status</th>
                        <th style="min-width:90px">Tgl Bayar</th>
                        <th style="width:110px" class="pe-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="11" class="text-center py-5 text-muted">
                                <i class="bi bi-cash-stack fs-2 d-block mb-2"></i>
                                Belum ada data penggajian untuk periode ini
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list as $i => $p): ?>
                            <tr>
                                <td class="text-muted small ps-3"><?= $i + 1 ?></td>
                                <?php if (!$isPegawai): ?>
                                    <td>
                                        <div class="fw-semibold"><?= htmlspecialchars($p->getNamaUser()) ?></div>
                                        <div class="text-muted small"><?= htmlspecialchars($p->getNip() ?? '-') ?></div>
                                    </td>
                                    <td class="small">
                                        <?= htmlspecialchars($p->getNamaJabatan() ?? '-') ?>
                                        <?php if ($p->getNamaGolongan()): ?>
                                            <span class="badge bg-light text-dark ms-1"><?= htmlspecialchars($p->getNamaGolongan()) ?></span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="text-end font-monospace small"><?= rpG($p->getGajiPokok()) ?></td>
                                <td class="text-end text-success font-monospace small"><?= rpG($p->getTunjangan()) ?></td>
                                <td class="text-end text-danger font-monospace small"><?= rpG($p->getPotonganAbsensi()) ?></td>
                                <td class="text-end text-danger font-monospace small"><?= rpG($p->getPotonganLain()) ?></td>
                                <td class="text-end fw-bold font-monospace"><?= rpG($p->getTotalGaji()) ?></td>
                                <td>
                                    <?php if ($p->getStatus() === 'dibayar'): ?>
                                        <span class="badge bg-success">Dibayar</span>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    <?php endif; ?>
                                </td>
                                <td class="small">
                                    <?= $p->getTanggalBayar() ? date('d/m/Y', strtotime($p->getTanggalBayar())) : '-' ?>
                                </td>
                                <td class="pe-3">
                                    <div class="btn-group btn-group-sm">
                                        <!-- Slip gaji bisa dilihat semua role -->
                                        <a href="<?= base('/transaksi/penggajian/slip?id=' . $p->getId()) ?>"
                                            class="btn btn-outline-info" title="Lihat Slip Gaji" target="_blank">
                                            <i class="bi bi-file-earmark-text"></i>
                                        </a>
                                        <?php if ($role === 'admin_tu' && $p->getStatus() !== 'dibayar'): ?>
                                            <button class="btn btn-outline-warning"
                                                onclick="editPenggajian(<?= $p->getId() ?>)" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-outline-danger"
                                                onclick="deletePenggajian(<?= $p->getId() ?>, '<?= htmlspecialchars($p->getNamaUser(), ENT_QUOTES) ?>')"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        <?php elseif ($role === 'admin_tu' && $p->getStatus() === 'dibayar'): ?>
                                            <button class="btn btn-outline-warning"
                                                onclick="editPenggajian(<?= $p->getId() ?>)" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <?php if (!empty($list)): ?>
                    <tfoot class="table-light fw-semibold">
                        <tr>
                            <td colspan="3" class="ps-3">TOTAL</td>
                            <td class="text-end font-monospace small"><?= rpG(array_sum(array_map(fn($p) => $p->getGajiPokok(), $list))) ?></td>
                            <td class="text-end text-success font-monospace small"><?= rpG(array_sum(array_map(fn($p) => $p->getTunjangan(), $list))) ?></td>
                            <td class="text-end text-danger font-monospace small"><?= rpG(array_sum(array_map(fn($p) => $p->getPotonganAbsensi(), $list))) ?></td>
                            <td class="text-end text-danger font-monospace small"><?= rpG(array_sum(array_map(fn($p) => $p->getPotonganLain(), $list))) ?></td>
                            <td class="text-end fw-bold font-monospace"><?= rpG($totalGaji) ?></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>

<!-- MODAL BULK PENGGAJIAN MASSAL -->
<?php require BASE_PATH . '/08Bsui/transaction/penggajian/_modal_bulk.php'; ?>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= base('/transaksi/penggajian/update') ?>">
                <input type="hidden" name="id" id="editId">
                <input type="hidden" name="periode" value="<?= htmlspecialchars($_GET['periode'] ?? date('Y-m')) ?>">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Penggajian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Pegawai</label>
                        <input type="text" id="editNama" class="form-control bg-light" readonly>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Gaji Pokok</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="text" id="editGajiPokok" class="form-control bg-light" readonly disabled>
                            </div>
                            <small class="text-muted">Terintegrasi dari Golongan</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tunjangan</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="tunjangan" id="editTunjangan" class="form-control"
                                    min="0" step="1000" oninput="hitungTotalEdit()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Potongan Absensi</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">Rp</span>
                                <input type="text" id="editPotonganAbsensi" class="form-control bg-light" readonly disabled>
                            </div>
                            <small class="text-muted">Terintegrasi dari Rekap Absensi</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Potongan Lain</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="potongan_lain" id="editPotonganLain" class="form-control"
                                    min="0" step="1000" oninput="hitungTotalEdit()">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="editStatus" class="form-select" onchange="toggleTanggalBayar(this)">
                                <option value="pending">Pending</option>
                                <option value="dibayar">Dibayar</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="tanggalBayarWrap" style="display:none">
                            <label class="form-label fw-semibold">Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" id="editTanggalBayar" class="form-control">
                        </div>
                    </div>

                    <div class="alert alert-info py-2 d-flex justify-content-between align-items-center mt-3">
                        <span class="small"><i class="bi bi-calculator-fill me-1"></i> Total Gaji = Gaji Pokok + Tunjangan − Potongan (Absensi + Lain)</span>
                        <strong id="editTotal" class="fs-5"></strong>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Keterangan</label>
                        <textarea name="keterangan" id="editKeterangan" class="form-control" rows="2"></textarea>
                    </div>

                    <div id="editInfoAbsensi" class="alert alert-secondary py-2 small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Potongan absensi terintegrasi dari rekap absensi periode ini.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="bi bi-save me-1"></i>Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DELETE -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form method="POST" action="<?= base('/transaksi/penggajian/delete') ?>">
                <input type="hidden" name="id" id="deleteId">
                <input type="hidden" name="periode" value="<?= htmlspecialchars($_GET['periode'] ?? date('Y-m')) ?>">
                <div class="modal-body text-center py-4">
                    <i class="bi bi-trash3 fs-1 text-danger mb-2 d-block"></i>
                    <p class="mb-0">Hapus data penggajian <strong id="deleteNama"></strong>?</p>
                    <small class="text-muted">Data yang dihapus tidak dapat dikembalikan</small>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const fmt = n => 'Rp ' + Number(n).toLocaleString('id-ID');

    function hitungTotalEdit() {
        const gp = parseFloat(document.getElementById('editGajiPokok').value?.replace(/[^0-9]/g, '') || 0);
        const tj = parseFloat(document.getElementById('editTunjangan').value || 0);
        const pa = parseFloat(document.getElementById('editPotonganAbsensi').value?.replace(/[^0-9]/g, '') || 0);
        const pl = parseFloat(document.getElementById('editPotonganLain').value || 0);
        const tot = gp + tj - pa - pl;
        document.getElementById('editTotal').textContent = fmt(Math.max(0, tot));
    }

    function toggleTanggalBayar(sel) {
        const wrap = document.getElementById('tanggalBayarWrap');
        wrap.style.display = sel.value === 'dibayar' ? 'block' : 'none';
        if (sel.value === 'dibayar') {
            const d = document.getElementById('editTanggalBayar');
            if (!d.value) d.value = new Date().toISOString().split('T')[0];
        }
    }

    function editPenggajian(id) {
        fetch('<?= base('/transaksi/penggajian/get') ?>?id=' + id)
            .then(r => r.json())
            .then(d => {
                if (!d) return alert('Data tidak ditemukan');
                document.getElementById('editId').value = d.id;
                document.getElementById('editNama').value = d.nama;
                document.getElementById('editGajiPokok').value = fmt(d.gaji_pokok);
                document.getElementById('editTunjangan').value = d.tunjangan;
                document.getElementById('editPotonganAbsensi').value = fmt(d.potongan_absensi);
                document.getElementById('editPotonganLain').value = d.potongan_lain;
                document.getElementById('editStatus').value = d.status;
                document.getElementById('editKeterangan').value = d.keterangan || '';
                document.getElementById('editTanggalBayar').value = d.tanggal_bayar || '';
                document.getElementById('editInfoAbsensi').innerHTML = `
                    <i class="bi bi-info-circle me-1"></i>
                    Potongan absensi terintegrasi dari rekap absensi periode ini.
                    Total potongan: ${fmt(d.potongan_absensi)}
                `;
                toggleTanggalBayar(document.getElementById('editStatus'));
                hitungTotalEdit();
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    }

    function deletePenggajian(id, nama) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteNama').textContent = nama;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }
</script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        <?php if (!$isPegawai): ?>
            // Filter bar — select pegawai dengan pencarian
            $('#filterUserId').select2({
                theme: 'bootstrap-5',
                placeholder: 'Semua Pegawai',
                allowClear: true,
                width: '100%'
            });
        <?php endif; ?>

        <?php if ($role === 'admin_tu'): ?>
            // Select2 tidak diperlukan di modal bulk (sudah pakai input search sendiri)
        <?php endif; ?>

        // DataTables
        $('#tablePenggajian').DataTable({
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
    });
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/08Bsui/layouts/app.php';
?>