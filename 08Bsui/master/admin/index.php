<?php
// master/admin/index.php
$pageTitle  = 'Admin';
$activeMenu = 'master/admin';
require BASE_PATH . '/08Bsui/layouts/app.php';
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">👤 Manajemen Admin</h1>
            <p class="page-desc">Kelola akun administrator sistem</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('modal-create')">＋ Tambah Admin</button>
    </div>

    <?php if ($flash ?? null): ?>
        <div class="alert alert-<?= $flash['type'] ?>"><?= htmlspecialchars($flash['msg']) ?></div>
    <?php endif; ?>

    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="search-input" placeholder="Cari nama / email / username..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-secondary">Cari</button>
            <a href="<?= url('/master/admin') ?>" class="btn btn-ghost">Reset</a>
        </form>
    </div>

    <?php if (empty($list)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">👤</div>
            <h3>Belum ada data admin</h3>
            <p>Tambahkan admin pertama dengan tombol di atas</p>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>No. Telepon</th>
                        <th>Status</th>
                        <th class="th-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $i => $a): ?>
                        <tr>
                            <td class="td-num"><?= $i + 1 ?></td>
                            <td>
                                <div class="td-user">
                                    <div class="td-avatar"><?= strtoupper(substr($a->getNama(), 0, 1)) ?></div>
                                    <strong><?= htmlspecialchars($a->getNama()) ?></strong>
                                </div>
                            </td>
                            <td><code><?= htmlspecialchars($a->getUser()?->getUsername() ?? '—') ?></code></td>
                            <td><?= htmlspecialchars($a->getEmail() ?? '—') ?></td>
                            <td><?= htmlspecialchars($a->getNoTelp() ?? '—') ?></td>
                            <td>
                                <span class="badge <?= $a->getIsActive() ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $a->getIsActive() ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="td-action">
                                <button class="btn btn-ghost btn-sm btn-icon" title="Edit" onclick="loadEditAdmin(<?= $a->getId() ?>)">✏️</button>
                                <form method="POST" action="<?= url('/master/admin/delete') ?>" data-confirm="Admin <?= htmlspecialchars($a->getNama()) ?> akan dihapus secara permanen." data-confirm-title="Hapus Admin?" data-confirm-icon="🗑️" data-confirm-btn="Ya, Hapus" style="display:inline">
                                    <input type="hidden" name="id" value="<?= $a->getId() ?>">
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
            <h3 class="modal-title">👤 Tambah Admin</h3>
            <button class="modal-close" onclick="closeModal('modal-create')">✕</button>
        </div>
        <form method="POST" action="<?= url('/master/admin/create') ?>">
            <div class="modal-body">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" class="form-input" placeholder="Nama lengkap" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username <span class="required">*</span></label>
                        <input type="text" name="username" class="form-input" placeholder="username unik" required>
                    </div>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-input" placeholder="email@contoh.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" name="no_telp" class="form-input" placeholder="08xx-xxxx-xxxx">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" class="form-input" rows="2" placeholder="Alamat lengkap"></textarea>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Password <span class="required">*</span></label>
                        <input type="password" name="password" class="form-input" placeholder="Min. 6 karakter" required>
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
            <h3 class="modal-title">✏️ Edit Admin</h3>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <form method="POST" action="<?= url('/master/admin/update') ?>">
            <div class="modal-body">
                <input type="hidden" name="id" id="ea-id">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" id="ea-nama" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" id="ea-username" class="form-input" disabled style="background:#f5f5f5">
                    </div>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="ea-email" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" name="no_telp" id="ea-telp" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" id="ea-alamat" class="form-input" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="is_active" id="ea-active" class="form-input">
                        <option value="1">Aktif</option>
                        <option value="0">Nonaktif</option>
                    </select>
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
    async function loadEditAdmin(id) {
        try {
            const r = await fetch(`<?= url('/master/admin/get') ?>?id=${id}`);
            const d = await r.json();
            if (!d) {
                showToast('Data tidak ditemukan', 'error');
                return;
            }
            document.getElementById('ea-id').value = d.id;
            document.getElementById('ea-nama').value = d.nama;
            document.getElementById('ea-username').value = d.username ?? '';
            document.getElementById('ea-email').value = d.email ?? '';
            document.getElementById('ea-telp').value = d.no_telp ?? '';
            document.getElementById('ea-alamat').value = d.alamat ?? '';
            document.getElementById('ea-active').value = d.is_active ? '1' : '0';
            openModal('modal-edit');
        } catch (e) {
            showToast('Gagal memuat data', 'error');
        }
    }
</script>
</body>

</html>