<?php
$title     = 'Profil Saya';
$pageTitle = 'Profil';
$breadcrumbs = ['Profil'];

$roleLabels = [
    'admin_tu'       => 'Admin TU',
    'kepala_sekolah' => 'Kepala Sekolah',
    'guru'           => 'Guru',
    'staff'          => 'Staff TU',
];

ob_start();
?>

<?php if (!empty($flash)): ?>
    <div class="alert alert-<?= $flash['type'] ?> alert-dismissible d-flex align-items-center gap-2">
        <i class="bi bi-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-triangle' ?>-fill"></i>
        <?= htmlspecialchars($flash['msg']) ?>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="page-header">
    <h4><i class="bi bi-person-circle me-2 text-primary"></i>Profil Saya</h4>
    <p>Kelola informasi akun dan data pribadi Anda</p>
</div>

<div class="row g-4">
    <!-- CARD PROFIL -->
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center py-4">
                <div class="mx-auto mb-3 d-flex align-items-center justify-content-center rounded-circle"
                    style="width:90px;height:90px;background:linear-gradient(135deg,#1e3a5f,#2a5298);font-size:2rem;font-weight:700;color:#fff">
                    <?= strtoupper(substr($user->getNama(), 0, 2)) ?>
                </div>
                <h5 class="fw-bold mb-1"><?= htmlspecialchars($user->getNama()) ?></h5>
                <p class="text-muted small mb-2"><?= htmlspecialchars($user->getUsername()) ?></p>
                <span class="badge bg-primary-subtle text-primary">
                    <?= $roleLabels[$user->getRole()] ?? ucfirst($user->getRole()) ?>
                </span>
                <?php if ($user->getNamaJabatan()): ?>
                    <div class="mt-2">
                        <span class="badge bg-secondary-subtle text-secondary">
                            <?= htmlspecialchars($user->getNamaJabatan()) ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer bg-transparent">
                <div class="row g-0 text-center">
                    <div class="col-6 py-2 border-end">
                        <div class="small text-muted">NIP</div>
                        <div class="fw-semibold small"><?= htmlspecialchars($user->getNip() ?? '-') ?></div>
                    </div>
                    <div class="col-6 py-2">
                        <div class="small text-muted">Jenis Kelamin</div>
                        <div class="fw-semibold small"><?= $user->getJenisKelamin() === 'L' ? 'Laki-laki' : 'Perempuan' ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FORM EDIT -->
    <div class="col-md-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" id="profileTabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabInfo">
                            <i class="bi bi-person me-1"></i>Informasi
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPassword">
                            <i class="bi bi-lock me-1"></i>Ubah Password
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- TAB INFO -->
                    <div class="tab-pane fade show active" id="tabInfo">
                        <form method="POST" action="<?= base('/profile/update') ?>">
                            <input type="hidden" name="jenis_kelamin" value="<?= htmlspecialchars($user->getJenisKelamin()) ?>">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="nama" class="form-control" required
                                        value="<?= htmlspecialchars($user->getNama()) ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Username</label>
                                    <input type="text" class="form-control" readonly
                                        value="<?= htmlspecialchars($user->getUsername()) ?>">
                                    <div class="form-text text-muted">Username tidak dapat diubah</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Email</label>
                                    <input type="email" name="email" class="form-control"
                                        value="<?= htmlspecialchars($user->getEmail() ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">NIP</label>
                                    <input type="text" name="nip" class="form-control"
                                        value="<?= htmlspecialchars($user->getNip() ?? '') ?>">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">No. Telepon</label>
                                    <input type="text" name="no_telp" class="form-control"
                                        value="<?= htmlspecialchars($user->getNoTelp() ?? '') ?>">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Alamat</label>
                                    <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($user->getAlamat() ?? '') ?></textarea>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save me-1"></i>Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB PASSWORD -->
                    <div class="tab-pane fade" id="tabPassword">
                        <form method="POST" action="<?= base('/profile/update') ?>">
                            <input type="hidden" name="nama" value="<?= htmlspecialchars($user->getNama()) ?>">
                            <input type="hidden" name="email" value="<?= htmlspecialchars($user->getEmail() ?? '') ?>">
                            <input type="hidden" name="nip" value="<?= htmlspecialchars($user->getNip() ?? '') ?>">
                            <input type="hidden" name="no_telp" value="<?= htmlspecialchars($user->getNoTelp() ?? '') ?>">
                            <input type="hidden" name="alamat" value="<?= htmlspecialchars($user->getAlamat() ?? '') ?>">
                            <input type="hidden" name="jenis_kelamin" value="<?= htmlspecialchars($user->getJenisKelamin()) ?>">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="alert alert-info small py-2">
                                        <i class="bi bi-info-circle me-1"></i>
                                        Kosongkan field password jika tidak ingin mengubah password.
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Password Baru</label>
                                    <div class="input-group">
                                        <input type="password" name="password_baru" id="newPass" class="form-control"
                                            placeholder="Minimal 6 karakter" minlength="6">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePass('newPass',this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Konfirmasi Password</label>
                                    <div class="input-group">
                                        <input type="password" id="confirmPass" class="form-control"
                                            placeholder="Ulangi password baru">
                                        <button class="btn btn-outline-secondary" type="button"
                                            onclick="togglePass('confirmPass',this)">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div id="passMsg" class="form-text"></div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-warning" id="btnSavePass">
                                    <i class="bi bi-lock me-1"></i>Ubah Password
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePass(id, btn) {
        const inp = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (inp.type === 'password') {
            inp.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            inp.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }

    document.getElementById('confirmPass').addEventListener('input', function() {
        const p1 = document.getElementById('newPass').value;
        const p2 = this.value;
        const msg = document.getElementById('passMsg');
        const btn = document.getElementById('btnSavePass');
        if (!p2) {
            msg.textContent = '';
            btn.disabled = false;
            return;
        }
        if (p1 === p2) {
            msg.className = 'form-text text-success';
            msg.textContent = '✓ Password cocok';
            btn.disabled = false;
        } else {
            msg.className = 'form-text text-danger';
            msg.textContent = '✗ Password tidak cocok';
            btn.disabled = true;
        }
    });

    // auto dismiss alerts
    document.querySelectorAll('.alert').forEach(a => setTimeout(() => a.style.opacity = '0', 4000));
</script>

<?php
$content = ob_get_clean();
require BASE_PATH . '/08Bsui/layouts/app.php';
?>