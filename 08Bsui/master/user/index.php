<?php
$title = 'Kelola Pegawai';
$pageTitle = 'Master Pegawai';
$breadcrumbs = ['Master Data', 'Pegawai'];

function rp(float $v): string
{
    return 'Rp ' . number_format($v, 0, ',', '.');
}

$roleBadge = [
    'admin_tu'        => ['bg-danger', 'Admin TU'],
    'kepala_sekolah'  => ['bg-dark', 'Kepala Sekolah'],
    'guru'            => ['bg-success', 'Guru'],
    'staff'           => ['bg-info text-dark', 'Staff TU'],
];

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
        <h4><i class="bi bi-people me-2 text-primary"></i>Kelola Pegawai</h4>
        <p>Manajemen data guru, staff tata usaha, dan akun pengguna sistem</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalCreate">
        <i class="bi bi-person-plus me-1"></i>Tambah Pegawai
    </button>
</div>

<!-- FILTER -->
<div class="card mb-3">
    <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm"
                    placeholder="Cari nama, NIP, username..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select form-select-sm">
                    <option value="">Semua Role</option>
                    <option value="guru" <?= ($_GET['role'] ?? '') === 'guru' ? 'selected' : '' ?>>Guru</option>
                    <option value="staff" <?= ($_GET['role'] ?? '') === 'staff' ? 'selected' : '' ?>>Staff TU</option>
                    <option value="admin_tu" <?= ($_GET['role'] ?? '') === 'admin_tu' ? 'selected' : '' ?>>Admin TU</option>
                    <option value="kepala_sekolah" <?= ($_GET['role'] ?? '') === 'kepala_sekolah' ? 'selected' : '' ?>>Kepala Sekolah</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-search me-1"></i>Cari
                </button>
                <a href="<?= base('/master/user') ?>" class="btn btn-sm btn-outline-secondary ms-1">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>Daftar Pegawai</h5>
        <span class="badge bg-primary-subtle text-primary"><?= count($list) ?> pegawai</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tableUser">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Nama / NIP</th>
                        <th>Username</th>
                        <th>Jabatan</th>
                        <th>Golongan</th>
                        <th>Gaji Pokok</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($list)): ?>
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-people fs-3 d-block mb-2"></i>Belum ada data pegawai
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($list as $i => $u): ?>
                            <tr>
                                <td class="text-muted"><?= $i + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white fw-700"
                                            style="width:34px;height:34px;background:<?= $u->getRole() === 'guru' ? '#2a5298' : ($u->getRole() === 'staff' ? '#0f766e' : '#7c3aed') ?>;font-size:.8rem;flex-shrink:0">
                                            <?= strtoupper(substr($u->getNama(), 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="fw-600 text-dark"><?= htmlspecialchars($u->getNama()) ?></div>
                                            <div class="small text-muted"><?= $u->getNip() ?: '—' ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td><code><?= htmlspecialchars($u->getUsername()) ?></code></td>
                                <td class="small"><?= $u->getNamaJabatan() ? htmlspecialchars($u->getNamaJabatan()) : '<span class="text-muted">—</span>' ?></td>
                                <td>
                                    <?php if ($u->getKodeGolongan()): ?>
                                        <span class="badge bg-secondary"><?= htmlspecialchars($u->getKodeGolongan()) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted small">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="fw-600 text-success small"><?= rp($u->getGajiPokok()) ?></td>
                                <td>
                                    <?php [$cls, $lbl] = $roleBadge[$u->getRole()] ?? ['bg-secondary', $u->getRole()]; ?>
                                    <span class="badge <?= $cls ?>"><?= $lbl ?></span>
                                </td>
                                <td>
                                    <?php if ($u->getIsActive()): ?>
                                        <span class="badge bg-success-subtle text-success"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Aktif</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger"><i class="bi bi-circle-fill me-1" style="font-size:.5rem"></i>Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1"
                                        onclick="editUser(<?= $u->getId() ?>)" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger"
                                        onclick="deleteUser(<?= $u->getId() ?>, '<?= htmlspecialchars($u->getNama()) ?>')"
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= base('/master/user/create') ?>">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>Tambah Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama lengkap" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" class="form-control" placeholder="Nomor Induk Pegawai">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="Username login" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" placeholder="Password (min. 6 karakter)" required minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@sman7bungo.sch.id">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control" placeholder="08xx-xxxx-xxxx">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Role / Akses <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="guru">Guru</option>
                                <option value="staff">Staff TU</option>
                                <option value="admin_tu">Admin TU</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan_id" class="form-select">
                                <option value="">— Pilih Jabatan —</option>
                                <?php foreach ($jabatan as $j): ?>
                                    <option value="<?= $j->getId() ?>">
                                        <?= htmlspecialchars($j->getNamaJabatan()) ?>
                                        (<?= $j->getJenis() === 'guru' ? 'Guru' : 'Staff' ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="gaji_pokok" class="form-control" placeholder="0" min="0" step="50000" value="0" required>
                            <div class="form-text">Gaji pokok individu (bisa berbeda dengan golongan)</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="is_active" id="isActiveCreate" class="form-check-input" checked value="1">
                                <label class="form-check-label" for="isActiveCreate">Pegawai Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="<?= base('/master/user/update') ?>">
                <input type="hidden" name="id" id="editId">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Pegawai</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" id="editNama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">NIP</label>
                            <input type="text" name="nip" id="editNip" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="editEmail" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" id="editNoTelp" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Role / Akses</label>
                            <select name="role" id="editRole" class="form-select">
                                <option value="guru">Guru</option>
                                <option value="staff">Staff TU</option>
                                <option value="admin_tu">Admin TU</option>
                                <option value="kepala_sekolah">Kepala Sekolah</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jabatan</label>
                            <select name="jabatan_id" id="editJabatan" class="form-select">
                                <option value="">— Pilih Jabatan —</option>
                                <?php foreach ($jabatan as $j): ?>
                                    <option value="<?= $j->getId() ?>">
                                        <?= htmlspecialchars($j->getNamaJabatan()) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="editJenisKelamin" class="form-select">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Gaji Pokok (Rp)</label>
                            <input type="number" name="gaji_pokok" id="editGajiPokok" class="form-control" min="0" step="50000">
                            <div class="form-text">Gaji pokok individu pegawai ini</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password_baru" class="form-control"
                                placeholder="Kosongkan jika tidak diubah" minlength="6">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status Pegawai</label>
                            <div class="form-check mt-2">
                                <input type="checkbox" name="is_active" id="editIsActive" class="form-check-input" value="1">
                                <label class="form-check-label" for="editIsActive">Aktif</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" id="editAlamat" class="form-control" rows="2"></textarea>
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
            <form method="POST" action="<?= base('/master/user/delete') ?>">
                <input type="hidden" name="id" id="deleteId">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-person-x me-2"></i>Hapus Pegawai</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="bi bi-exclamation-triangle text-danger fs-2 d-block mb-3"></i>
                    <p>Hapus pegawai <strong id="deleteNama"></strong>?</p>
                    <p class="text-muted small">Data absensi dan penggajian terkait akan ikut terhapus.</p>
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
    function editUser(id) {
        fetch('<?= base('/master/user/get') ?>?id=' + id)
            .then(r => r.json())
            .then(d => {
                if (!d) return;
                document.getElementById('editId').value = d.id;
                document.getElementById('editNama').value = d.nama;
                document.getElementById('editNip').value = d.nip ?? '';
                document.getElementById('editEmail').value = d.email ?? '';
                document.getElementById('editNoTelp').value = d.no_telp ?? '';
                document.getElementById('editRole').value = d.role;
                document.getElementById('editJabatan').value = d.jabatan_id ?? '';
                document.getElementById('editJenisKelamin').value = d.jenis_kelamin ?? 'L';
                document.getElementById('editGajiPokok').value = d.gaji_pokok ?? 0;
                document.getElementById('editAlamat').value = d.alamat ?? '';
                document.getElementById('editIsActive').checked = !!d.is_active;
                new bootstrap.Modal(document.getElementById('modalEdit')).show();
            });
    }

    function deleteUser(id, nama) {
        document.getElementById('deleteId').value = id;
        document.getElementById('deleteNama').textContent = nama;
        new bootstrap.Modal(document.getElementById('modalDelete')).show();
    }

    $(document).ready(function() {
        $('#tableUser').DataTable({
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