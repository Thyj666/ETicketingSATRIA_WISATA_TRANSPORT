<?php
$title     = 'Absensi Pegawai';
$pageTitle = 'Transaksi Absensi';
$breadcrumbs = ['Transaksi', 'Absensi'];

function rpA(float $v): string
{
    return 'Rp ' . number_format($v, 0, ',', '.');
}

$statusBadge = [
    'hadir' => ['bg-success',           'Hadir'],
    'izin'  => ['bg-warning text-dark', 'Izin'],
    'sakit' => ['bg-info text-dark',    'Sakit'],
    'alpha' => ['bg-danger',            'Alpha'],
];

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

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="bi bi-calendar-check me-2 text-primary"></i>
            <?= $isPegawai ? 'Absensi Saya' : 'Absensi Pegawai' ?>
        </h4>
        <p><?= $isPegawai ? 'Riwayat kehadiran Anda' : 'Kelola data kehadiran seluruh pegawai' ?></p>
    </div>

    <?php if ($role === 'admin_tu'): ?>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalBulk">
                <i class="bi bi-plus-lg me-1"></i>Tambah Absensi
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- FILTER -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="month" name="bulan" class="form-control form-control-sm"
                    value="<?= htmlspecialchars($_GET['bulan'] ?? date('Y-m')) ?>">
            </div>

            <?php if (!$isPegawai): ?>
                <div class="col-md-3">
                    <select name="user_id" class="form-select form-select-sm" id="filterUserId">
                        <option value="">Semua Pegawai</option>
                        <?php foreach ($users as $u): ?>
                            <option value="<?= $u->getId() ?>"
                                <?= (isset($_GET['user_id']) && $_GET['user_id'] == $u->getId()) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($u->getNama()) ?>
                                (<?= htmlspecialchars($u->getNamaJabatan() ?? $u->getRole()) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    <option value="hadir" <?= ($_GET['status'] ?? '') === 'hadir' ? 'selected' : '' ?>>Hadir</option>
                    <option value="izin" <?= ($_GET['status'] ?? '') === 'izin'  ? 'selected' : '' ?>>Izin</option>
                    <option value="sakit" <?= ($_GET['status'] ?? '') === 'sakit' ? 'selected' : '' ?>>Sakit</option>
                    <option value="alpha" <?= ($_GET['status'] ?? '') === 'alpha' ? 'selected' : '' ?>>Alpha</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="<?= base('/transaksi/absensi') ?>" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- SUMMARY CARDS -->
<?php
$totalHadir = count(array_filter($list, fn($a) => $a->getStatus() === 'hadir'));
$totalIzin  = count(array_filter($list, fn($a) => $a->getStatus() === 'izin'));
$totalSakit = count(array_filter($list, fn($a) => $a->getStatus() === 'sakit'));
$totalAlpha = count(array_filter($list, fn($a) => $a->getStatus() === 'alpha'));
?>
<div class="row g-3 mb-3">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-success fs-4 fw-bold"><?= $totalHadir ?></div>
            <div class="text-muted small"><i class="bi bi-check-circle me-1"></i>Hadir</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-warning fs-4 fw-bold"><?= $totalIzin ?></div>
            <div class="text-muted small"><i class="bi bi-calendar-x me-1"></i>Izin</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-info fs-4 fw-bold"><?= $totalSakit ?></div>
            <div class="text-muted small"><i class="bi bi-heart-pulse me-1"></i>Sakit</div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm text-center py-3">
            <div class="text-danger fs-4 fw-bold"><?= $totalAlpha ?></div>
            <div class="text-muted small"><i class="bi bi-x-circle me-1"></i>Alpha</div>
        </div>
    </div>
</div>

<!-- TABLE -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Daftar Absensi</h5>
        <span class="badge bg-primary-subtle text-primary"><?= count($list) ?> data</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableAbsensi">
                <thead class="table-light">
                    <tr>
                        <th style="width:45px">#</th>
                        <?php if (!$isPegawai): ?>
                            <th>Pegawai</th>
                            <th>Jabatan</th>
                        <?php endif; ?>
                        <th>Tanggal</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Status</th>
                        <th>Potongan</th>
                        <th>Keterangan</th>
                        <?php if ($role === 'admin_tu'): ?><th style="width:100px">Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                                Belum ada data absensi
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list as $i => $absensi): ?>
                            <?php [$badgeClass, $badgeText] = $statusBadge[$absensi->getStatus()] ?? ['bg-secondary', '?']; ?>
                            <tr>
                                <td class="text-muted small"><?= $i + 1 ?></td>
                                <?php if (!$isPegawai): ?>
                                    <td>
                                        <div class="fw-semibold small"><?= htmlspecialchars($absensi->getNamaUser()) ?></div>
                                        <div class="text-muted" style="font-size:.75rem"><?= htmlspecialchars($absensi->getNip() ?? '-') ?></div>
                                    </td>
                                    <td class="small text-muted"><?= htmlspecialchars($absensi->getNamaJabatan() ?? '-') ?></td>
                                <?php endif; ?>
                                <td class="small"><?= date('d/m/Y', strtotime($absensi->getTanggal())) ?></td>
                                <td class="small"><?= $absensi->getJamMasuk() ?: '-' ?></td>
                                <td class="small"><?= $absensi->getJamKeluar() ?: '-' ?></td>
                                <td><span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span></td>
                                <td class="small <?= $absensi->getPotonganGaji() > 0 ? 'text-danger' : '' ?>">
                                    <?= $absensi->getPotonganGaji() > 0 ? rpA($absensi->getPotonganGaji()) : '-' ?>
                                </td>
                                <td class="small text-muted"><?= htmlspecialchars($absensi->getKeterangan() ?: '-') ?></td>
                                <?php if ($role === 'admin_tu'): ?>
                                    <td>
                                        <button class="btn btn-sm btn-outline-warning me-1"
                                            onclick="editAbsensi(<?= $absensi->getId() ?>)" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger"
                                            onclick="deleteAbsensi(<?= $absensi->getId() ?>, '<?= htmlspecialchars($absensi->getNamaUser(), ENT_QUOTES) ?>')"
                                            title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if ($role === 'admin_tu'): ?>
    <!-- MODAL EDIT -->
    <div class="modal fade" id="modalEdit" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="<?= base('/transaksi/absensi/update') ?>">
                    <input type="hidden" name="id" id="editId">
                    <input type="hidden" name="bulan" value="<?= htmlspecialchars($_GET['bulan'] ?? date('Y-m')) ?>">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Absensi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pegawai</label>
                            <input type="text" id="editNama" class="form-control" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tanggal</label>
                            <input type="text" id="editTanggal" class="form-control" readonly>
                        </div>
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Jam Masuk</label>
                                <input type="time" name="jam_masuk" id="editJamMasuk" class="form-control">
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Jam Keluar</label>
                                <input type="time" name="jam_keluar" id="editJamKeluar" class="form-control">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" id="editStatus" class="form-select"
                                onchange="togglePotongan(this,'potonganEdit')">
                                <option value="hadir">Hadir</option>
                                <option value="izin">Izin</option>
                                <option value="sakit">Sakit</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </div>
                        <div class="mb-3" id="potonganEdit">
                            <label class="form-label fw-semibold">Potongan Gaji</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="potongan_gaji" id="editPotongan" class="form-control" min="0" step="1000" value="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" id="editKeterangan" class="form-control" rows="2"></textarea>
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
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <form method="POST" action="<?= base('/transaksi/absensi/delete') ?>">
                    <input type="hidden" name="id" id="deleteId">
                    <input type="hidden" name="bulan" value="<?= htmlspecialchars($_GET['bulan'] ?? date('Y-m')) ?>">
                    <div class="modal-body text-center py-4">
                        <i class="bi bi-trash3 fs-1 text-danger mb-2 d-block"></i>
                        <p>Hapus data absensi <strong id="deleteNama"></strong>?</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- MODAL ABSENSI MASSAL -->
    <?php require BASE_PATH . '/08Bsui/transaction/absensi/_modal_bulk.php'; ?>
<?php endif; ?>

<!-- Select2 JS (setelah Bootstrap JS yang sudah di-load di layout) -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Filter bar – select pegawai dengan search
        <?php if (!$isPegawai): ?>
            $('#filterUserId').select2({
                theme: 'bootstrap-5',
                placeholder: 'Semua Pegawai',
                allowClear: true,
                width: '100%'
            });
        <?php endif; ?>

        // Select di dalam modal Create – dropdownParent agar tampil di atas modal
        <?php if ($role === 'admin_tu'): ?>
            $('#createUserId').select2({
                theme: 'bootstrap-5',
                placeholder: '-- Cari & Pilih Pegawai --',
                allowClear: true,
                dropdownParent: $('#modalCreate'),
                width: '100%'
            });
        <?php endif; ?>

        // DataTables
        $('#tableAbsensi').DataTable({
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

    function togglePotongan(sel, targetId) {
        const div = document.getElementById(targetId);
        div.style.display = ['izin', 'sakit', 'alpha'].includes(sel.value) ? '' : 'none';
    }

    <?php if ($role === 'admin_tu'): ?>

        function editAbsensi(id) {
            fetch('<?= base('/transaksi/absensi/get') ?>?id=' + id)
                .then(r => r.json())
                .then(d => {
                    if (!d) return alert('Data tidak ditemukan');
                    document.getElementById('editId').value = d.id;
                    document.getElementById('editNama').value = d.nama;
                    document.getElementById('editTanggal').value = d.tanggal;
                    document.getElementById('editJamMasuk').value = d.jam_masuk || '';
                    document.getElementById('editJamKeluar').value = d.jam_keluar || '';
                    document.getElementById('editStatus').value = d.status;
                    document.getElementById('editPotongan').value = d.potongan_gaji || 0;
                    document.getElementById('editKeterangan').value = d.keterangan || '';
                    const editSel = document.getElementById('editStatus');
                    togglePotongan(editSel, 'potonganEdit');
                    new bootstrap.Modal(document.getElementById('modalEdit')).show();
                });
        }

        function deleteAbsensi(id, nama) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteNama').textContent = nama;
            new bootstrap.Modal(document.getElementById('modalDelete')).show();
        }
    <?php endif; ?>
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/08Bsui/layouts/app.php';
?>