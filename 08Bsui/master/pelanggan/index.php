<?php
// master/pelanggan/index.php
$pageTitle  = 'Pelanggan';
$activeMenu = 'master/pelanggan';
require BASE_PATH . '/08Bsui/layouts/app.php';
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">👥 Manajemen Pelanggan</h1>
            <p class="page-desc">Kelola data pelanggan terdaftar</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('modal-create')">＋ Tambah Pelanggan</button>
    </div>

    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="search-input" placeholder="Cari nama / email / no. telp..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <button type="submit" class="btn btn-secondary">Cari</button>
            <a href="<?= url('/master/pelanggan') ?>" class="btn btn-ghost">Reset</a>
        </form>
    </div>

    <?php if (empty($list)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h3>Belum ada data pelanggan</h3>
            <p>Data pelanggan akan muncul setelah mereka mendaftar</p>
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
                    <?php foreach ($list as $i => $p): ?>
                        <tr>
                            <td class="td-num"><?= $i + 1 ?></td>
                            <td>
                                <div class="td-user">
                                    <div class="td-avatar"><?= strtoupper(substr($p->getNama(), 0, 1)) ?></div>
                                    <strong><?= htmlspecialchars($p->getNama()) ?></strong>
                                </div>
                            </td>
                            <td><code><?= htmlspecialchars($p->getUser()?->getUsername() ?? '—') ?></code></td>
                            <td><?= htmlspecialchars($p->getEmail() ?? '—') ?></td>
                            <td><?= htmlspecialchars($p->getNoTelp() ?? '—') ?></td>
                            <td>
                                <span class="badge <?= $p->getIsActive() ? 'badge-success' : 'badge-secondary' ?>">
                                    <?= $p->getIsActive() ? 'Aktif' : 'Nonaktif' ?>
                                </span>
                            </td>
                            <td class="td-action">
                                <button class="btn btn-ghost btn-sm btn-icon" title="Edit" onclick="loadEditPelanggan(<?= $p->getId() ?>)">✏️</button>
                                <form method="POST" action="<?= url('/master/pelanggan/delete') ?>" data-confirm="Pelanggan <?= htmlspecialchars($p->getNama()) ?> akan dihapus secara permanen." data-confirm-title="Hapus Pelanggan?" data-confirm-icon="🗑️" data-confirm-btn="Ya, Hapus" style="display:inline">
                                    <input type="hidden" name="id" value="<?= $p->getId() ?>">
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
</div>

<!-- MODAL TAMBAH -->
<div class="modal" id="modal-create">
    <div class="modal-dialog">
        <div class="modal-header">
            <h3 class="modal-title">👥 Tambah Pelanggan</h3>
            <button class="modal-close" onclick="closeModal('modal-create')">✕</button>
        </div>
        <form method="POST" action="<?= url('/master/pelanggan/create') ?>">
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
            <h3 class="modal-title">✏️ Edit Pelanggan</h3>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <form method="POST" action="<?= url('/master/pelanggan/update') ?>">
            <div class="modal-body">
                <input type="hidden" name="id" id="ep-id">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                        <input type="text" name="nama" id="ep-nama" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <input type="text" id="ep-username" class="form-input" disabled style="background:#f5f5f5">
                    </div>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="ep-email" class="form-input">
                    </div>
                    <div class="form-group">
                        <label class="form-label">No. Telepon</label>
                        <input type="tel" name="no_telp" id="ep-telp" class="form-input">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" id="ep-alamat" class="form-input" rows="2"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="is_active" id="ep-active" class="form-input">
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
    async function loadEditPelanggan(id) {
        try {
            const r = await fetch(`<?= url('/master/pelanggan/get') ?>?id=${id}`);
            const d = await r.json();
            if (!d) {
                showToast('Data tidak ditemukan', 'error');
                return;
            }
            document.getElementById('ep-id').value = d.id;
            document.getElementById('ep-nama').value = d.nama;
            document.getElementById('ep-username').value = d.username ?? '';
            document.getElementById('ep-email').value = d.email ?? '';
            document.getElementById('ep-telp').value = d.no_telp ?? '';
            document.getElementById('ep-alamat').value = d.alamat ?? '';
            document.getElementById('ep-active').value = d.is_active ? '1' : '0';
            openModal('modal-edit');
        } catch (e) {
            showToast('Gagal memuat data', 'error');
        }
    }
</script>
</body>

</html>