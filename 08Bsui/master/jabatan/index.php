<?php
$title = 'Jabatan';
$pageTitle = 'Master Jabatan';
$breadcrumbs = ['Master Data', 'Jabatan'];

function rp(float $v): string
{
    return 'Rp ' . number_format($v, 0, ',', '.');
}

ob_start();
?>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible alert-auto-dismiss d-flex align-items-center gap-2">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <?= htmlspecialchars($flash['msg']) ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <div>
        <h4><i class="bi bi-briefcase me-2 text-primary"></i>Jabatan</h4>
        <p>Kelola jabatan guru dan staff tata usaha beserta golongan gajinya</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
        <i class="bi bi-plus-lg me-1"></i>Tambah Jabatan
    </button>
</div>

<!-- FILTER -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari nama jabatan..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select name="jenis" class="form-select form-select-sm">
                    <option value="">Semua Jenis</option>
                    <option value="guru" <?= ($_GET['jenis'] ?? '') === 'guru' ? 'selected' : '' ?>>Guru</option>
                    <option value="staff" <?= ($_GET['jenis'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff TU</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
                <a href="<?= base('/master/jabatan') ?>" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Daftar Jabatan</h5>
        <span class="badge bg-primary-subtle text-primary"><?= count($list) ?> jabatan</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableJabatan">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Nama Jabatan</th>
                        <th>Jenis Pegawai</th>
                        <th>Golongan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data jabatan
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list as $i => $j): ?>
                            <tr>
                                <td class="text-muted"><?= $i + 1 ?></td>
                                <td class="fw-600"><?= htmlspecialchars($j->getNamaJabatan()) ?></td>
                                <td>
                                    <?php if ($j->getJenis() === 'guru'): ?>
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            <i class="bi bi-mortarboard me-1"></i>Guru
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-info-subtle text-info px-3 py-2">
                                            <i class="bi bi-person-badge me-1"></i>Staff TU
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($j->getNamaGolongan()): ?>
                                        <span class="badge bg-primary"><?= htmlspecialchars($j->getKodeGolongan() ?? '') ?></span>
                                        <span class="ms-1 small text-muted"><?= htmlspecialchars($j->getNamaGolongan()) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">— Tidak ada</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $j->getGajiPokok() ? rp($j->getGajiPokok()) : '<span class="text-muted">—</span>' ?></td>
                                <td><?= $j->getTunjangan() ? rp($j->getTunjangan()) : '<span class="text-muted">—</span>' ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                        onclick="editJabatan(<?= $j->getId() ?>)" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteJabatan(<?= $j->getId() ?>, '<?= htmlspecialchars($j->getNamaJabatan()) ?>')"
                                        title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL CREATE -->
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= base('/master/jabatan/create') ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jabatan" class="form-control"
                                placeholder="cth: Guru Matematika" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pegawai <span class="text-danger">*</span></label>
                            <select name="jenis" class="form-select" required>
                                <option value="guru">Guru</option>
                                <option value="staff">Staff TU</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Golongan</label>
                            <select name="golongan_id" class="form-select">
                                <option value="">— Pilih Golongan —</option>
                                <?php foreach ($golonganList as $g): ?>
                                    <option value="<?= $g->getId() ?>">
                                        <?= htmlspecialchars($g->getKodeGolongan()) ?> - <?= htmlspecialchars($g->getNamaGolongan()) ?>
                                        (Rp <?= number_format($g->getGajiPokok(), 0, ',', '.') ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Memilih golongan akan menentukan gaji pokok dan tunjangan.</div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2"
                                placeholder="Deskripsi jabatan (opsional)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= base('/master/jabatan/update') ?>">
                <input type="hidden" name="id" id="editId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Jabatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_jabatan" id="editNama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Pegawai</label>
                            <select name="jenis" id="editJenis" class="form-select" required>
                                <option value="guru">Guru</option>
                                <option value="staff">Staff TU</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Golongan</label>
                            <select name="golongan_id" id="editGolongan" class="form-select">
                                <option value="">— Pilih Golongan —</option>
                                <?php foreach ($golonganList as $g): ?>
                                    <option value="<?= $g->getId() ?>">
                                        <?= htmlspecialchars($g->getKodeGolongan()) ?> - <?= htmlspecialchars($g->getNamaGolongan()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Keterangan</label>
                            <textarea name="keterangan" id="editKeterangan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL DELETE -->
<div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <form method="POST" action="<?= base('/master/jabatan/delete') ?>">
                <input type="hidden" name="id" id="deleteId">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Hapus Jabatan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-triangle text-danger fs-2 d-block mb-3"></i>
                    <p>Hapus jabatan <strong id="deleteNama"></strong>?</p>
                    <p class="text-muted small">Pegawai dengan jabatan ini tidak akan memiliki jabatan.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i>Hapus</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editJabatan(id) {
        fetch('<?= base('/master/jabatan/get') ?>?id=' + id)
            .then(r => r.json())
            .then(d => {
                if (!d) return;
                document.getElementById('editId').value = d.id;
                document.getElementById('editNama').value = d.nama_jabatan;
                document.getElementById('editJenis').value = d.jenis;
                document.getElementById('editGolongan').value = d.golongan_id ?? '';
                document.getElementById('editKeterangan').value = d.keterangan ?? '';
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    }

    function deleteJabatan(id, nama) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteNama').textContent = nama;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }

    $(document).ready(function() {
        $('#tableJabatan').DataTable({
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