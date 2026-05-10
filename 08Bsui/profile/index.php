<?php
// profile/index.php
$pageTitle  = 'Profil Saya';
$activeMenu = 'profile';
require BASE_PATH . '/08Bsui/layouts/app.php';

$profileNama  = $profile?->getNama()   ?? ($userEntity?->getUsername() ?? '');
$profileEmail = $profile?->getEmail()  ?? null;
$profileTelp  = $profile?->getNoTelp() ?? null;
$profileAlamat = $profile?->getAlamat() ?? null;
$initials     = strtoupper(substr($profileNama, 0, 2));
$roleLabels   = ['admin' => '👤 Admin', 'admin' => '🗂 Admin TU', 'pelanggan' => '👥 Pelanggan', 'pimpinan' => '🎓 Pimpinan'];
$roleLabel    = $roleLabels[$role] ?? ucfirst($role);
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">⚙️ Profil Saya</h1>
            <p class="page-desc">Kelola informasi akun dan data diri Anda</p>
        </div>
    </div>

    <?php if ($flash ?? null): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="profile-layout">
        <!-- Avatar card -->
        <div class="profile-avatar-card card-block">
            <div class="profile-avatar-big"><?= $initials ?></div>
            <div class="profile-name"><?= htmlspecialchars($profileNama) ?></div>
            <div class="profile-role-badge"><?= $roleLabel ?></div>
            <div class="profile-username">@<?= htmlspecialchars($userEntity?->getUsername() ?? '—') ?></div>
            <?php if ($profileEmail): ?>
                <div class="profile-info-row">✉️ <?= htmlspecialchars($profileEmail) ?></div>
            <?php endif; ?>
            <?php if ($profileTelp): ?>
                <div class="profile-info-row">📱 <?= htmlspecialchars($profileTelp) ?></div>
            <?php endif; ?>
            <?php if ($profileAlamat): ?>
                <div class="profile-info-row">📍 <?= htmlspecialchars($profileAlamat) ?></div>
            <?php endif; ?>
        </div>

        <!-- Edit form -->
        <div class="profile-form-card card-block">
            <h3 class="section-title-sm">Edit Informasi</h3>
            <form method="POST" action="<?= url('/profile/update') ?>">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-input" value="<?= htmlspecialchars($profileNama) ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-input" value="<?= htmlspecialchars($userEntity?->getUsername() ?? '') ?>" disabled style="background:#f5f5f5">
                    </div>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" value="<?= htmlspecialchars($profileEmail ?? '') ?>" placeholder="email@contoh.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" name="no_telp" class="form-input" value="<?= htmlspecialchars($profileTelp ?? '') ?>" placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-input" rows="2" placeholder="Alamat lengkap"><?= htmlspecialchars($profileAlamat ?? '') ?></textarea>
                </div>

                <hr style="border:none;border-top:1px solid var(--border);margin:20px 0">
                <h4 class="section-title-sm" style="margin-bottom:12px">Ganti Password</h4>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Password Baru <small style="color:#888">(kosongkan jika tidak diubah)</small></label>
                        <div class="input-icon-wrap">
                            <input type="password" name="password" id="prof-pwd" class="form-input" placeholder="Password baru">
                            <button type="button" class="input-eye" onclick="togglePwd('prof-pwd',this)">👁</button>
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</main>
</div><!-- .layout-wrapper -->

<div id="toast-container"></div>
<script src="<?= url('08Bsui/wwwroot/js/app.js') ?>"></script>
<script>
    function togglePwd(id, btn) {
        const inp = document.getElementById(id);
        inp.type = inp.type === 'password' ? 'text' : 'password';
        btn.textContent = inp.type === 'password' ? '👁' : '🙈';
    }
</script>
</body>

</html>