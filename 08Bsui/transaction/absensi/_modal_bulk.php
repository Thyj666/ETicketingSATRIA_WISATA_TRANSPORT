<?php

/**
 * 08Bsui/transaction/absensi/_modal_bulk.php
 * Partial: Modal Absensi Massal
 *
 * Variabel yang diharapkan sudah tersedia dari index.php:
 *   $users  - array UserEntity (semua pegawai)
 */
?>

<!-- ===== TOMBOL ABSENSI MASSAL (disisipkan di halaman utama) ===== -->
<?php /* tombol ini di-render oleh index.php; partial ini hanya berisi modal */ ?>

<!-- ===== MODAL ABSENSI MASSAL ===== -->
<div class="modal fade" id="modalBulk" tabindex="-1" aria-labelledby="modalBulkLabel" aria-modal="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalBulkLabel">
                    <i class="bi bi-calendar2-check me-2"></i>Absensi Massal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="<?= base('/transaksi/absensi/create-bulk') ?>" id="formBulk">

                <!-- ---- HEADER FORM ---- -->
                <div class="modal-body pb-0">

                    <!-- Tanggal + Jam Default -->
                    <div class="row g-3 mb-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Tanggal <span class="text-danger">*</span>
                            </label>
                            <input type="date" name="tanggal" id="bulkTanggal"
                                class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Jam Masuk Default</label>
                            <input type="time" name="jam_masuk_bulk" class="form-control" value="07:00">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Jam Keluar Default</label>
                            <input type="time" name="jam_keluar_bulk" class="form-control" value="15:00">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Cari Pegawai</label>
                            <input type="text" id="bulkSearch" class="form-control"
                                placeholder="Ketik nama / jabatan...">
                        </div>
                        <div class="col-md-2 d-flex gap-2">
                            <button type="button" class="btn btn-outline-success btn-sm w-50"
                                id="btnCeklisSemua" title="Centang semua yang belum absen">
                                <i class="bi bi-check2-all"></i> Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-50"
                                id="btnResetSemua" title="Hapus centang semua">
                                <i class="bi bi-x-lg"></i> Reset
                            </button>
                        </div>
                    </div>

                    <!-- Keterangan status warna -->
                    <div class="d-flex gap-3 flex-wrap mb-3 small text-muted">
                        <span><span class="badge bg-success">Hadir</span></span>
                        <span><span class="badge bg-warning text-dark">Izin</span></span>
                        <span><span class="badge bg-info text-dark">Sakit</span></span>
                        <span><span class="badge bg-danger">Alpha</span></span>
                        <span class="ms-auto">
                            <i class="bi bi-info-circle me-1"></i>
                            Baris <span class="text-danger fw-semibold">bergaris merah</span> = sudah absen hari ini
                        </span>
                    </div>

                    <!-- Loading indicator -->
                    <div id="bulkLoading" class="text-center py-3" style="display:none">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        Memuat status absensi...
                    </div>

                    <!-- Counter -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">
                            Total pegawai: <strong><?= count($users) ?></strong>
                        </span>
                        <span class="badge bg-primary-subtle text-primary" id="bulkSelectedCount">
                            0 dipilih
                        </span>
                    </div>
                </div>

                <!-- ---- TABEL PEGAWAI ---- -->
                <div class="modal-body pt-0" style="max-height:420px;overflow-y:auto">
                    <table class="table table-sm table-hover align-middle mb-0" id="tabelBulk">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:38px">
                                    <input type="checkbox" class="form-check-input" id="chkAll"
                                        title="Centang/batal semua yang aktif">
                                </th>
                                <th style="width:32px">#</th>
                                <th>Nama Pegawai</th>
                                <th>Jabatan</th>
                                <th style="width:130px">Status</th>
                                <th style="width:120px">Keterangan</th>
                                <th style="width:90px">Info</th>
                            </tr>
                        </thead>
                        <tbody id="tabelBulkBody">
                            <?php foreach ($users as $i => $u): ?>
                                <?php
                                $uid      = $u->getId();
                                $jabatan  = htmlspecialchars($u->getNamaJabatan() ?? $u->getRole());
                                $namaEsc  = htmlspecialchars($u->getNama());
                                ?>
                                <tr class="bulk-row"
                                    data-uid="<?= $uid ?>"
                                    data-nama="<?= strtolower($u->getNama()) ?>"
                                    data-jabatan="<?= strtolower($u->getNamaJabatan() ?? '') ?>">

                                    <!-- Checkbox -->
                                    <td>
                                        <input type="checkbox"
                                            class="form-check-input bulk-chk"
                                            data-uid="<?= $uid ?>"
                                            id="chk_<?= $uid ?>">
                                    </td>

                                    <!-- Nomor -->
                                    <td class="text-muted small"><?= $i + 1 ?></td>

                                    <!-- Nama -->
                                    <td>
                                        <label for="chk_<?= $uid ?>" class="mb-0 fw-semibold small cursor-pointer">
                                            <?= $namaEsc ?>
                                        </label>
                                        <div class="text-muted" style="font-size:.72rem">
                                            <?= htmlspecialchars($u->getNip() ?: '-') ?>
                                        </div>
                                    </td>

                                    <!-- Jabatan -->
                                    <td class="small text-muted"><?= $jabatan ?></td>

                                    <!-- Status dropdown — hidden input arrays digenerate via JS saat submit -->
                                    <td>
                                        <select class="form-select form-select-sm bulk-status"
                                            data-uid="<?= $uid ?>"
                                            disabled>
                                            <option value="hadir" selected>Hadir</option>
                                            <option value="izin">Izin</option>
                                            <option value="sakit">Sakit</option>
                                            <option value="alpha">Alpha</option>
                                        </select>
                                    </td>

                                    <!-- Keterangan -->
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm bulk-ket"
                                            data-uid="<?= $uid ?>"
                                            placeholder="Opsional"
                                            disabled>
                                    </td>

                                    <!-- Badge status absensi -->
                                    <td class="text-center">
                                        <span class="badge-status" data-uid="<?= $uid ?>">
                                            <span class="badge bg-secondary-subtle text-secondary">-</span>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- ---- FOOTER ---- -->
                <div class="modal-footer">
                    <small class="text-muted me-auto">
                        <i class="bi bi-info-circle me-1"></i>
                        Pegawai yang sudah absen hari ini tidak dapat dipilih.
                    </small>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanBulk" disabled>
                        <i class="bi bi-save me-1"></i>Simpan Absensi
                    </button>
                </div>

            </form><!-- /formBulk -->
        </div>
    </div>
</div>

<script>
    (function() {
        'use strict';

        /* ── State ─────────────────────────────────────── */
        let sudahAbsenIds = []; // user_id yang sudah absen di tanggal terpilih
        let currentDate = document.getElementById('bulkTanggal').value;

        /* ── Helpers ───────────────────────────────────── */
        const rows = () => [...document.querySelectorAll('.bulk-row')];
        const chkAll = document.getElementById('chkAll');
        const countBadge = document.getElementById('bulkSelectedCount');
        const btnSimpan = document.getElementById('btnSimpanBulk');

        function updateCounter() {
            const n = document.querySelectorAll('.bulk-chk:checked').length;
            countBadge.textContent = n + ' dipilih';
            btnSimpan.disabled = n === 0;
        }

        function isAlreadyAbsen(uid) {
            return sudahAbsenIds.includes(Number(uid));
        }

        function applyRowState(row) {
            const uid = row.dataset.uid;
            const chk = row.querySelector('.bulk-chk');
            const sel = row.querySelector('.bulk-status');
            const ket = row.querySelector('.bulk-ket');
            const badge = row.querySelector('.badge-status');

            if (isAlreadyAbsen(uid)) {
                /* Sudah absen — kunci baris */
                row.classList.add('table-danger', 'opacity-75');
                row.classList.remove('table-success');
                chk.disabled = true;
                chk.checked = false;
                sel.disabled = true;
                ket.disabled = true;
                badge.innerHTML = '<span class="badge bg-danger">Sudah Absen</span>';
            } else {
                row.classList.remove('table-danger', 'opacity-75');
                chk.disabled = false;
                /* aktifkan kontrol hanya jika dicentang */
                const checked = chk.checked;
                sel.disabled = !checked;
                ket.disabled = !checked;
                row.classList.toggle('table-success', checked);
                badge.innerHTML = '<span class="badge bg-secondary-subtle text-secondary">-</span>';
            }
        }

        /* ── Fetch status absensi ──────────────────────── */
        function fetchStatusHari(tanggal) {
            const loading = document.getElementById('bulkLoading');
            loading.style.display = '';

            fetch(base('/transaksi/absensi/status-hari') + '?tanggal=' + encodeURIComponent(tanggal))
                .then(r => r.json())
                .then(data => {
                    sudahAbsenIds = data.sudah_absen || [];
                    rows().forEach(row => applyRowState(row));
                    updateCounter();
                })
                .catch(() => {
                    sudahAbsenIds = [];
                })
                .finally(() => {
                    loading.style.display = 'none';
                });
        }

        /* ── Event: modal terbuka ──────────────────────── */
        document.getElementById('modalBulk').addEventListener('show.bs.modal', () => {
            currentDate = document.getElementById('bulkTanggal').value;
            fetchStatusHari(currentDate);
        });

        /* ── Event: tanggal berubah ────────────────────── */
        document.getElementById('bulkTanggal').addEventListener('change', function() {
            currentDate = this.value;
            /* Reset semua centang */
            document.querySelectorAll('.bulk-chk').forEach(c => {
                c.checked = false;
            });
            chkAll.checked = false;
            fetchStatusHari(currentDate);
        });

        /* ── Event: checkbox individual ───────────────── */
        document.getElementById('tabelBulkBody').addEventListener('change', function(e) {
            if (!e.target.classList.contains('bulk-chk')) return;
            const row = e.target.closest('tr');
            applyRowState(row);
            updateCounter();
            /* Sinkronisasi chkAll */
            const enabledChks = [...document.querySelectorAll('.bulk-chk:not(:disabled)')];
            chkAll.checked = enabledChks.length > 0 && enabledChks.every(c => c.checked);
            chkAll.indeterminate = enabledChks.some(c => c.checked) && !chkAll.checked;
        });

        /* ── Event: chkAll ────────────────────────────── */
        chkAll.addEventListener('change', function() {
            document.querySelectorAll('.bulk-chk:not(:disabled)').forEach(c => {
                /* hanya centang baris yang terlihat (tidak tersaring) */
                const row = c.closest('tr');
                if (row.style.display !== 'none') {
                    c.checked = this.checked;
                    applyRowState(row);
                }
            });
            updateCounter();
        });

        /* ── Event: Centang Semua / Reset ─────────────── */
        document.getElementById('btnCeklisSemua').addEventListener('click', () => {
            chkAll.checked = true;
            chkAll.dispatchEvent(new Event('change'));
        });
        document.getElementById('btnResetSemua').addEventListener('click', () => {
            chkAll.checked = false;
            chkAll.dispatchEvent(new Event('change'));
        });

        /* ── Pencarian ────────────────────────────────── */
        document.getElementById('bulkSearch').addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            rows().forEach(row => {
                const match = !q ||
                    row.dataset.nama.includes(q) ||
                    row.dataset.jabatan.includes(q);
                row.style.display = match ? '' : 'none';
            });
            /* Update chkAll state */
            const visibleEnabled = [...document.querySelectorAll('.bulk-chk:not(:disabled)')]
                .filter(c => c.closest('tr').style.display !== 'none');
            chkAll.checked = visibleEnabled.length > 0 && visibleEnabled.every(c => c.checked);
            chkAll.indeterminate = visibleEnabled.some(c => c.checked) && !chkAll.checked;
        });

        /* ── Submit: build hidden inputs dari state tabel ─ */
        document.getElementById('formBulk').addEventListener('submit', function(e) {
            /* Hapus hidden inputs lama */
            this.querySelectorAll('input[name^="rows["]').forEach(el => el.remove());

            let idx = 0;
            document.querySelectorAll('.bulk-chk:checked').forEach(chk => {
                const uid = chk.dataset.uid;
                const sel = document.querySelector(`.bulk-status[data-uid="${uid}"]`);
                const ket = document.querySelector(`.bulk-ket[data-uid="${uid}"]`);
                const status = sel ? sel.value : 'hadir';
                const ketVal = ket ? ket.value : '';

                const append = (name, val) => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = val;
                    this.appendChild(inp);
                };

                append(`rows[${idx}][user_id]`, uid);
                append(`rows[${idx}][status]`, status);
                append(`rows[${idx}][keterangan]`, ketVal);
                append(`rows[${idx}][potongan_gaji]`, '0');
                idx++;
            });

            if (idx === 0) {
                e.preventDefault();
                alert('Pilih minimal satu pegawai terlebih dahulu.');
            }
        });

        /* Expose base() jika belum tersedia (sudah ada di layout) */
        if (typeof base === 'undefined') {
            window.base = p => p;
        }
    })();
</script>