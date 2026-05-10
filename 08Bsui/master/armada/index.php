<?php
// master/armada/index.php
$pageTitle  = 'Armada';
$activeMenu = 'master/armada';
require BASE_PATH . '/08Bsui/layouts/app.php';
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">🚌 Manajemen Armada</h1>
            <p class="page-desc">Kelola data bus dan armada perjalanan</p>
        </div>
        <button class="btn btn-primary" onclick="openModal('modal-create')">＋ Tambah Armada</button>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="search-input" placeholder="Cari plat / nama armada..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <select name="status" class="form-input filter-input">
                <option value="">Semua Status</option>
                <option value="tersedia" <?= ($_GET['status'] ?? '') === 'tersedia'  ? 'selected' : '' ?>>Tersedia</option>
                <option value="digunakan" <?= ($_GET['status'] ?? '') === 'digunakan' ? 'selected' : '' ?>>Digunakan</option>
                <option value="servis" <?= ($_GET['status'] ?? '') === 'servis'    ? 'selected' : '' ?>>Servis</option>
            </select>
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="<?= url('/master/armada') ?>" class="btn btn-ghost">Reset</a>
        </form>
    </div>

    <!-- Table -->
    <?php if (empty($list)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🚌</div>
            <h3>Belum ada data armada</h3>
            <p>Tambahkan armada pertama Anda dengan tombol di atas</p>
        </div>
    <?php else: ?>
        <div class="table-card">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Plat Nomor</th>
                        <th>Nama Armada</th>
                        <th>Tipe Seat</th>
                        <th>Jumlah Seat</th>
                        <th>Status</th>
                        <th class="th-action">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($list as $i => $a): ?>
                        <tr>
                            <td class="td-num"><?= $i + 1 ?></td>
                            <td><span class="badge badge-outline"><?= htmlspecialchars($a->getPlatNomor()) ?></span></td>
                            <td><strong><?= htmlspecialchars($a->getNamaArmada()) ?></strong></td>
                            <td><?= htmlspecialchars($a->getTipeSeat()) ?></td>
                            <td><?= $a->getJumlahSeat() ?> kursi</td>
                            <td>
                                <?php
                                $st = $a->getStatus();
                                $stClass = match ($st) {
                                    'tersedia'  => 'badge-success',
                                    'digunakan' => 'badge-warning',
                                    default     => 'badge-secondary',
                                };
                                ?>
                                <span class="badge <?= $stClass ?>"><?= ucfirst($st) ?></span>
                            </td>
                            <td class="td-action">
                                <button class="btn btn-ghost btn-sm btn-icon" title="Edit" onclick="loadEditArmada(<?= $a->getId() ?>)">✏️</button>
                                <form method="POST" action="<?= url('/master/armada/delete') ?>" data-confirm="Hapus armada <?= htmlspecialchars($a->getNamaArmada()) ?>?" style="display:inline">
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
            <h3 class="modal-title">🚌 Tambah Armada</h3>
            <button class="modal-close" onclick="closeModal('modal-create')">✕</button>
        </div>
        <form method="POST" action="<?= url('/master/armada/create') ?>">
            <div class="modal-body">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Plat Nomor <span class="required">*</span></label>
                        <input type="text" name="plat_nomor" class="form-input" placeholder="BA 1234 XY" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Armada <span class="required">*</span></label>
                        <input type="text" name="nama_armada" class="form-input" placeholder="Bus Satria 01" required>
                    </div>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Tipe Seat</label>
                        <select name="tipe_seat" class="form-input">
                            <option value="2-2">2-2 (Standard)</option>
                            <option value="2-3">2-3 (Economy)</option>
                            <option value="1-2">1-2 (Executive)</option>
                            <option value="2-1">2-1 (VIP)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Seat <span class="required">*</span></label>
                        <input type="number" name="jumlah_seat" class="form-input" placeholder="40" min="1" max="100" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-input">
                        <option value="tersedia">Tersedia</option>
                        <option value="digunakan">Digunakan</option>
                        <option value="servis">Servis</option>
                    </select>
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
            <h3 class="modal-title">✏️ Edit Armada</h3>
            <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
        </div>
        <form method="POST" action="<?= url('/master/armada/update') ?>">
            <div class="modal-body">
                <input type="hidden" name="id" id="edit-id">
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Plat Nomor <span class="required">*</span></label>
                        <input type="text" name="plat_nomor" id="edit-plat" class="form-input" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Nama Armada <span class="required">*</span></label>
                        <input type="text" name="nama_armada" id="edit-nama" class="form-input" required>
                    </div>
                </div>
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label">Tipe Seat</label>
                        <select name="tipe_seat" id="edit-tipe" class="form-input">
                            <option value="2-2">2-2 (Standard)</option>
                            <option value="2-3">2-3 (Economy)</option>
                            <option value="1-2">1-2 (Executive)</option>
                            <option value="2-1">2-1 (VIP)</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Seat <span class="required">*</span></label>
                        <input type="number" name="jumlah_seat" id="edit-jumlah" class="form-input" min="1" max="100" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="edit-status" class="form-input">
                        <option value="tersedia">Tersedia</option>
                        <option value="digunakan">Digunakan</option>
                        <option value="servis">Servis</option>
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
    async function loadEditArmada(id) {
        try {
            const r = await fetch(`<?= url('/master/armada/get') ?>?id=${id}`);
            const d = await r.json();
            if (!d) {
                showToast('Data tidak ditemukan', 'error');
                return;
            }
            document.getElementById('edit-id').value = d.id;
            document.getElementById('edit-plat').value = d.plat_nomor;
            document.getElementById('edit-nama').value = d.nama_armada;
            document.getElementById('edit-tipe').value = d.tipe_seat;
            document.getElementById('edit-jumlah').value = d.jumlah_seat;
            document.getElementById('edit-status').value = d.status;
            openModal('modal-edit');
        } catch (e) {
            showToast('Gagal memuat data', 'error');
        }
    }
</script>
</body>

</html>