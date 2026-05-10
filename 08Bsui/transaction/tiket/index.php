<?php
// transaction/tiket/index.php
$pageTitle  = 'Tiket';
$activeMenu = 'transaksi/tiket';
require BASE_PATH . '/08Bsui/layouts/app.php';
?>
<main class="page-content">
    <div class="page-header">
        <div>
            <h1 class="page-title">🎫 Tiket Perjalanan</h1>
            <p class="page-desc">Temukan dan pesan tiket bus sesuai rute Anda</p>
        </div>
        <?php if ($role === 'admin'): ?>
            <button class="btn btn-primary" onclick="openModal('modal-create')">＋ Tambah Tiket</button>
        <?php endif; ?>
    </div>

    <!-- Filter bar -->
    <div class="filter-bar">
        <form method="GET" class="filter-form">
            <div class="search-box">
                <span class="search-icon">🔍</span>
                <input type="text" name="search" class="search-input" placeholder="Cari tujuan..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
            </div>
            <input type="date" name="tanggal" class="form-input filter-input" value="<?= htmlspecialchars($_GET['tanggal'] ?? '') ?>">
            <button type="submit" class="btn btn-secondary">Filter</button>
            <a href="<?= url('/transaksi/tiket') ?>" class="btn btn-ghost">Reset</a>
        </form>
    </div>

    <!-- Tiket cards grid -->
    <?php if (empty($list)): ?>
        <div class="empty-state">
            <div class="empty-state-icon">🎫</div>
            <h3>Tidak ada tiket tersedia</h3>
            <p>Belum ada tiket yang sesuai dengan pencarian Anda</p>
        </div>
    <?php else: ?>
        <div class="tiket-grid">
            <?php foreach ($list as $t):
                $armada   = $t->getArmada();
                $isFull   = $t->getIsFull();
                $harga    = number_format($t->getHarga(), 0, ',', '.');
                $tgl      = $t->getTanggalBerangkat() ? date('d M Y', strtotime($t->getTanggalBerangkat())) : '—';
                $jam      = $t->getJamBerangkat() ? substr($t->getJamBerangkat(), 0, 5) : '—';
            ?>
                <div class="ticket-card <?= $isFull ? 'ticket-full' : '' ?>">
                    <div class="ticket-card-top">
                        <div class="ticket-status-badge <?= $isFull ? 'badge-full' : 'badge-available' ?>">
                            <?= $isFull ? '🔴 Penuh' : '🟢 Tersedia' ?>
                        </div>
                        <div class="ticket-route">
                            <div class="ticket-origin">
                                <div class="ticket-city">Keberangkatan</div>
                                <div class="ticket-time"><?= $jam ?></div>
                            </div>
                            <div class="ticket-arrow">✈</div>
                            <div class="ticket-dest">
                                <div class="ticket-city"><?= htmlspecialchars($t->getTujuan()) ?></div>
                                <div class="ticket-date"><?= $tgl ?></div>
                            </div>
                        </div>
                        <?php if ($armada): ?>
                            <div class="ticket-armada-info">
                                <span>🚌 <?= htmlspecialchars($armada->getNamaArmada()) ?></span>
                                <span>· <?= htmlspecialchars($armada->getPlatNomor()) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <hr class="ticket-divider">
                    <div class="ticket-card-bottom">
                        <div>
                            <div class="ticket-price-label">Harga / kursi</div>
                            <div class="ticket-price">Rp <?= $harga ?></div>
                        </div>
                        <div class="ticket-card-actions">
                            <?php if (!$isFull && !in_array($role, ['admin', 'pimpinan'])): ?>
                                <button class="btn btn-primary btn-sm" onclick="openSeatModal(<?= $t->getId() ?>, '<?= htmlspecialchars($t->getTujuan()) ?>')">
                                    Pilih Kursi
                                </button>
                            <?php endif; ?>
                            <?php if ($role === 'admin'): ?>
                                <button class="btn btn-ghost btn-sm btn-icon" title="Edit" onclick="loadEdit(<?= $t->getId() ?>)">✏️</button>
                                <form method="POST" action="<?= url('/transaksi/tiket/delete') ?>" data-confirm="Hapus tiket ini?" style="display:inline">
                                    <input type="hidden" name="id" value="<?= $t->getId() ?>">
                                    <button type="submit" class="btn btn-danger btn-sm btn-icon" title="Hapus">🗑</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>
</div><!-- .layout-wrapper -->

<!-- ================================================================
     SEAT PICKER MODAL
     ================================================================ -->
<div class="modal" id="modal-seat">
    <div class="modal-box modal-box-wide">
        <div class="modal-header">
            <h3 class="modal-title" id="seat-modal-title">Pilih Kursi</h3>
            <button class="modal-close" onclick="closeModal('modal-seat')">✕</button>
        </div>
        <div class="modal-body">
            <div class="seat-legend">
                <div class="seat-legend-item">
                    <div class="seat-sample seat-available"></div><span>Tersedia</span>
                </div>
                <div class="seat-legend-item">
                    <div class="seat-sample seat-taken"></div><span>Terisi</span>
                </div>
                <div class="seat-legend-item">
                    <div class="seat-sample seat-selected"></div><span>Pilihan Anda</span>
                </div>
                <div class="seat-legend-item">
                    <div class="seat-sample seat-driver"></div><span>Supir</span>
                </div>
            </div>

            <!-- Bus visual -->
            <div class="bus-visual-wrap">
                <div class="bus-front">
                    <div class="bus-windshield"></div>
                    <div class="bus-wheel-row">
                        <div class="bus-wheel"></div>
                    </div>
                </div>
                <div class="seat-map" id="seat-map">
                    <div class="seat-loading">Memuat denah kursi…</div>
                </div>
                <div class="bus-back">
                    <div class="bus-wheel-row">
                        <div class="bus-wheel"></div>
                        <div class="bus-wheel"></div>
                    </div>
                </div>
            </div>

            <div class="seat-selected-info" id="seat-selected-info" style="display:none">
                <span>Kursi dipilih:</span> <strong id="seat-selected-label">—</strong>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn btn-ghost" onclick="closeModal('modal-seat')">Batal</button>
            <form method="POST" action="<?= url('/transaksi/pemesanan/create') ?>" id="seat-form">
                <input type="hidden" name="tiket_id" id="seat-tiket-id">
                <input type="hidden" name="no_seat" id="seat-no-seat">
                <button type="submit" class="btn btn-primary" id="btn-pesan" disabled>Pesan Sekarang</button>
            </form>
        </div>
    </div>
</div>

<?php if ($role === 'admin'): ?>
    <!-- Create Modal -->
    <div class="modal" id="modal-create">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Tiket</h3>
                <button class="modal-close" onclick="closeModal('modal-create')">✕</button>
            </div>
            <form method="POST" action="<?= url('/transaksi/tiket/create') ?>">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Armada</label>
                        <select name="armada_id" class="form-input" required>
                            <option value="">— Pilih Armada —</option>
                            <?php foreach ($armadas as $a): ?>
                                <option value="<?= $a->getId() ?>"><?= htmlspecialchars($a->getNamaArmada()) ?> (<?= htmlspecialchars($a->getPlatNomor()) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tujuan</label>
                        <input type="text" name="tujuan" class="form-input" placeholder="Kota tujuan" required>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Tanggal Berangkat</label>
                            <input type="date" name="tanggal_berangkat" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jam Berangkat</label>
                            <input type="time" name="jam_berangkat" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" class="form-input" placeholder="0" min="0" step="1000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-ghost" onclick="closeModal('modal-create')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="modal-edit">
        <div class="modal-box">
            <div class="modal-header">
                <h3 class="modal-title">Edit Tiket</h3>
                <button class="modal-close" onclick="closeModal('modal-edit')">✕</button>
            </div>
            <form method="POST" action="<?= url('/transaksi/tiket/update') ?>" id="form-edit">
                <div class="modal-body">
                    <input type="hidden" name="id" id="edit-id">
                    <div class="form-group">
                        <label class="form-label">Armada</label>
                        <select name="armada_id" id="edit-armada" class="form-input" required>
                            <?php foreach ($armadas as $a): ?>
                                <option value="<?= $a->getId() ?>"><?= htmlspecialchars($a->getNamaArmada()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tujuan</label>
                        <input type="text" name="tujuan" id="edit-tujuan" class="form-input" required>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Tanggal Berangkat</label>
                            <input type="date" name="tanggal_berangkat" id="edit-tgl" class="form-input">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Jam Berangkat</label>
                            <input type="time" name="jam_berangkat" id="edit-jam" class="form-input">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga (Rp)</label>
                        <input type="number" name="harga" id="edit-harga" class="form-input" min="0" step="1000">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status Tiket</label>
                        <select name="is_full" id="edit-full" class="form-input">
                            <option value="0">Tersedia</option>
                            <option value="1">Penuh</option>
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
<?php endif; ?>

<div id="toast-container"></div>
<script src="<?= url('08Bsui/wwwroot/js/app.js') ?>"></script>
<script>
    /* ---- Seat Picker ---- */
    let currentTiketId = null;
    let selectedSeat = null;

    function openSeatModal(tiketId, tujuan) {
        currentTiketId = tiketId;
        selectedSeat = null;
        document.getElementById('seat-modal-title').textContent = 'Pilih Kursi — ' + tujuan;
        document.getElementById('seat-tiket-id').value = tiketId;
        document.getElementById('seat-no-seat').value = '';
        document.getElementById('btn-pesan').disabled = true;
        document.getElementById('seat-selected-info').style.display = 'none';
        document.getElementById('seat-map').innerHTML = '<div class="seat-loading">Memuat denah kursi…</div>';
        openModal('modal-seat');
        loadSeats(tiketId);
    }

    async function loadSeats(tiketId) {
        try {
            const res = await fetch('<?= url('/transaksi/tiket/seats') ?>?tiket_id=' + tiketId);
            const data = await res.json();
            renderSeats(data.total, data.tipe_seat, data.taken);
        } catch (e) {
            document.getElementById('seat-map').innerHTML = '<div class="seat-loading">Gagal memuat denah kursi.</div>';
        }
    }

    function renderSeats(total, tipeSeat, taken) {
        const map = document.getElementById('seat-map');
        const takenSet = new Set(taken);
        const cols = tipeSeat === '2-3' ? 5 : tipeSeat === '1-1' ? 2 : 4; // 2-2 default
        const mid = tipeSeat === '2-3' ? 2 : tipeSeat === '1-1' ? 1 : 2;
        let html = '<div class="driver-row"><div class="seat seat-driver" title="Supir">🧑‍✈️</div></div><div class="seats-grid" style="--seat-cols:' + cols + '">';
        for (let i = 1; i <= total; i++) {
            const seatNo = String(i).padStart(2, '0');
            const isTaken = takenSet.has(i) || takenSet.has(String(i)) || takenSet.has(seatNo);
            let cls = isTaken ? 'seat seat-taken' : 'seat seat-available';
            // Aisle gap
            const posInRow = ((i - 1) % cols) + 1;
            if (posInRow === mid + 1) cls += ' seat-aisle';
            html += isTaken ?
                '<div class="' + cls + '" title="Kursi ' + seatNo + ' — Terisi">' + seatNo + '</div>' :
                '<div class="' + cls + '" title="Kursi ' + seatNo + '" onclick="selectSeat(\'' + seatNo + '\',this)">' + seatNo + '</div>';
        }
        html += '</div>';
        map.innerHTML = html;
    }

    function selectSeat(seatNo, el) {
        // Deselect previous
        document.querySelectorAll('.seat-selected').forEach(s => {
            s.classList.remove('seat-selected');
            s.classList.add('seat-available');
        });
        el.classList.remove('seat-available');
        el.classList.add('seat-selected');
        selectedSeat = seatNo;
        document.getElementById('seat-no-seat').value = seatNo;
        document.getElementById('seat-selected-label').textContent = seatNo;
        document.getElementById('seat-selected-info').style.display = 'flex';
        document.getElementById('btn-pesan').disabled = false;
    }

    /* ---- Admin edit ---- */
    async function loadEdit(id) {
        const res = await fetch('<?= url('/transaksi/tiket/get') ?>?id=' + id);
        const data = await res.json();
        if (!data) return;
        document.getElementById('edit-id').value = data.id;
        document.getElementById('edit-armada').value = data.armada_id;
        document.getElementById('edit-tujuan').value = data.tujuan;
        document.getElementById('edit-tgl').value = data.tanggal_berangkat || '';
        document.getElementById('edit-jam').value = data.jam_berangkat || '';
        document.getElementById('edit-harga').value = data.harga;
        document.getElementById('edit-full').value = data.is_full ? '1' : '0';
        openModal('modal-edit');
    }
</script>
</body>

</html>