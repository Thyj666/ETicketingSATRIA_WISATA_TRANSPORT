<?php
// master/user/index.php
$pageTitle  = 'User';
$activeMenu = 'master/user';
require BASE_PATH . '/08Bsui/layouts/app.php';

$roles = ['admin' => '👤 Admin', 'pelanggan' => '👥 Pelanggan', 'pimpinan' => '🏆 Pimpinan'];
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">🔑 Manajemen User</h1>
            <p class="page-desc">Kelola akun dan hak akses pengguna sistem</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('modal-create')">＋ Tambah User</button>
    </div>

    <?php if ($flash ?? null): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="search-input" placeholder="Cari username..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <select name="role" class="form-input filter-input">
                <option value="">Semua Role</option>
                <?php foreach ($roles as $val => $label): ?>
                    <option value="<?= $val ?>" <?= ($_GET['role'] ?? '') === $val ? 'selected' : '' ?>><?= $label ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="<?= url('/master/user') ?>" class="btn btn-ghost">Reset</a>
        </form>
    </div>

    <?php if (empty($list)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🔑</div>
            <h3>Belum ada data user</h3>
            <p>Tambahkan user pertama dengan tombol di atas</p>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                        <th class="th-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $i => $u): ?>
                        <tr>
                            <td class="td-num"><?= $i + 1 ?></td>
                            <td>
                                <div class="td-user">
                                    <div class="td-avatar"><?= strtoupper(substr($u->getUsername(), 0, 1)) ?></div>
                                    <code><?= htmlspecialchars($u->getUsername()) ?></code>
                                </div>
                            </td>
                            <td>
                                <?php $roleLabel = $roles[$u->getRole()] ?? $u->getRole(); ?>
                                <span class="badge badge-outline"><?= $roleLabel ?></span>
                            </td>
                            <td>
                                <span class="badge <?= $u->getIsActive() ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $u->getIsActive() ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="td-muted"><?= $u->getCreatedAt() ? date('d/m/Y', strtotime($u->getCreatedAt())) : '—' ?></td>
                            <td class="td-action">
                                <button class="btn btn-ghost btn-sm btn-icon" title="Edit" onclick="loadEditUser(<?= $u->getId() ?>)">✏️</button>
                                <form method="POST" action="<?= url('/master/user/delete') ?>" data-confirm="User <?= htmlspecialchars($u->getUsername()) ?> akan dihapus secara permanen." data-confirm-title="Hapus User?" data-confirm-icon="🔑" data-confirm-btn="Ya, Hapus" style="display:inline">
                                    <input type="hidden" name="id" value="<?= $u->getId() ?>">
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus">🗑</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>
</div><!-- .layout-wrapper -->

<!-- MODAL TAMBAH -->
<div class="modal" id="modal-create">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">🔑 Tambah User</h3>
            <button class="modal-close" onclick="closeModal('modal-create')">✕</button>
        </div>
        <form method="POST" action="<?= url('/master/user/create') ?>">
            <div class="modal-body">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Username <span class="required">*</span></label>
                        <input type="text" name="username" class="form-input" placeholder="username unik" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" class="form-input" placeholder="Min. 6 karakter" required>
                    </div>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Role <span class="required">*</span></label>
                        <select name="role" class="form-input" required>
                            <?php foreach ($roles as $val => $label): ?>
                                <option value="<?= $val ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="is_active" class="form-input">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modal-create')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal" id="modal-edit">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">✏️ Edit User</h3>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <form method="POST" action="<?= url('/master/user/update') ?>">
            <div class="modal-body">
                <input type="hidden" name="id" id="eu-id">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" id="eu-username" class="form-input" disabled style="background:#f5f5f5">
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select name="role" id="eu-role" class="form-input">
                            <?php foreach ($roles as $val => $label): ?>
                                <option value="<?= $val ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="is_active" id="eu-active" class="form-input">
                            <option value="1">Aktif</option>
                            <option value="0">Nonaktif</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Password Baru <small style="color:#888">(kosongkan jika tidak diubah)</small></label>
                    <input type="password" name="password" class="form-input" placeholder="Password baru (opsional)">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-ghost" onclick="closeModal('modal-edit')">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<div id="toast-container"></div>
<script src="<?= url('08Bsui/wwwroot/js/app.js') ?>"></script>
<script>
    async function loadEditUser(id) {
        try {
            const r = await fetch(`<?= url('/master/user/get') ?>?id=${id}`);
            const d = await r.json();
            if (!d) {
                showToast('Data tidak ditemukan', 'error');
                return;
            }
            document.getElementById('eu-id').value = d.id;
            document.getElementById('eu-username').value = d.username;
            document.getElementById('eu-role').value = d.role;
            document.getElementById('eu-active').value = d.is_active ? '1' : '0';
            openModal('modal-edit');
        } catch (e) {
            showToast('Gagal memuat data', 'error');
        }
    }
</script>
</body>

</html>