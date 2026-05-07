<?php
$title = 'Golongan & Gaji';
$pageTitle = 'Master Golongan & Gaji';
$breadcrumbs = ['Master Data', 'Golongan & Gaji'];

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
        <h4><i class="bi bi-layers me-2 text-primary"></i>Golongan & Gaji</h4>
        <p>Kelola golongan kepangkatan dan gaji pokok pegawai</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
        <i class="bi bi-plus-lg me-1"></i>Tambah Golongan
    </button>
</div>

<div class="card">
    <div class="card-header">
        <div class="row g-2 align-items-center">
            <div class="col">
                <h5>Daftar Golongan</h5>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary-subtle text-primary"><?= count($list) ?> golongan</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableGolongan">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Kode Golongan</th>
                        <th>Nama Golongan</th>
                        <th>Gaji Pokok</th>
                        <th>Tunjangan</th>
                        <th>Total Estimasi</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-3 d-block mb-2"></i>Belum ada data golongan
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list as $i => $g): ?>
                            <tr>
                                <td class="text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <span class="badge bg-primary fs-6 px-3 py-2"><?= htmlspecialchars($g->getKodeGolongan()) ?></span>
                                </td>
                                <td class="fw-500"><?= htmlspecialchars($g->getNamaGolongan()) ?></td>
                                <td class="text-success fw-600"><?= rp($g->getGajiPokok()) ?></td>
                                <td class="text-info fw-600"><?= rp($g->getTunjangan()) ?></td>
                                <td class="fw-700 text-primary"><?= rp($g->getGajiPokok() + $g->getTunjangan()) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                        onclick="editGolongan(<?= $g->getId() ?>)"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteGolongan(<?= $g->getId() ?>, '<?= htmlspecialchars($g->getNamaGolongan()) ?>')"
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

<!-- INFO FORMULA -->
<div class="card mt-3">
    <div class="card-body">
        <div class="gaji-formula">
            <div class="row align-items-center">
                <div class="col-auto"><i class="bi bi-info-circle text-info fs-5"></i></div>
                <div class="col">
                    <strong>Formula Perhitungan Gaji:</strong>
                    <span class="ms-2 text-primary">Total Gaji = Gaji Pokok + Tunjangan Golongan - Potongan Alpha - Potongan Lain</span>
                </div>
            </div>
            <div class="mt-2 text-muted small">
                Gaji Pokok dan Tunjangan diambil dari golongan yang ditetapkan pada jabatan pegawai.
                Potongan dihitung berdasarkan rekap absensi (alpha/tanpa keterangan) dan potongan lainnya.
            </div>
        </div>
    </div>
</div>

<!-- MODAL CREATE -->
<div class="modal fade" id="modalCreate" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="<?= base('/master/golongan/create') ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Tambah Golongan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Golongan <span class="text-danger">*</span></label>
                            <input type="text" name="kode_golongan" class="form-control" placeholder="cth: III/b" required maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Golongan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_golongan" class="form-control" placeholder="cth: Penata Muda Tk. I" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="gaji_pokok" class="form-control" placeholder="0" min="0" step="50000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tunjangan (Rp)</label>
                            <input type="number" name="tunjangan" class="form-control" placeholder="0" min="0" step="50000" value="0">
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 small mb-0">
                        <i class="bi bi-lightbulb me-1"></i>
                        Gaji pokok dan tunjangan akan digunakan sebagai dasar perhitungan gaji pegawai berdasarkan golongannya.
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
            <form method="POST" action="<?= base('/master/golongan/update') ?>">
                <input type="hidden" name="id" id="editId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Golongan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Kode Golongan <span class="text-danger">*</span></label>
                            <input type="text" name="kode_golongan" id="editKode" class="form-control" required maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Golongan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_golongan" id="editNama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="gaji_pokok" id="editGaji" class="form-control" min="0" step="50000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tunjangan (Rp)</label>
                            <input type="number" name="tunjangan" id="editTunjangan" class="form-control" min="0" step="50000">
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
            <form method="POST" action="<?= base('/master/golongan/delete') ?>">
                <input type="hidden" name="id" id="deleteId">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-trash me-2"></i>Hapus Golongan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-triangle text-danger fs-2 d-block mb-3"></i>
                    <p>Hapus golongan <strong id="deleteNama"></strong>?</p>
                    <p class="text-muted small">Jabatan yang menggunakan golongan ini tidak akan memiliki golongan.</p>
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
    function editGolongan(id) {
        fetch('<?= base('/master/golongan/get') ?>?id=' + id)
            .then(r => r.json())
            .then(d => {
                if (!d) return;
                document.getElementById('editId').value = d.id;
                document.getElementById('editKode').value = d.kode_golongan;
                document.getElementById('editNama').value = d.nama_golongan;
                document.getElementById('editGaji').value = d.gaji_pokok;
                document.getElementById('editTunjangan').value = d.tunjangan;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    }

    function deleteGolongan(id, nama) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteNama').textContent = nama;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }

    $(document).ready(function() {
        $('#tableGolongan').DataTable({
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