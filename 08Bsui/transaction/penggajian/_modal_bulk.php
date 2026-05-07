<?php

/**
 * 08Bsui/transaction/penggajian/_modal_bulk.php
 * Partial: Modal Penggajian Massal
 *
 * Variabel yang diharapkan sudah tersedia dari index.php:
 *   $users  - array UserEntity (semua pegawai)
 */
?>

<!-- ===== MODAL PENGGAJIAN MASSAL ===== -->
<div class="modal fade" id="modalBulk" tabindex="-1" aria-labelledby="modalBulkLabel" aria-modal="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalBulkLabel">
                    <i class="bi bi-cash-coin me-2"></i>Penggajian Massal
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form method="POST" action="<?= base('/transaksi/penggajian/create-bulk') ?>" id="formBulkPenggajian">

                <!-- ---- HEADER FORM ---- -->
                <div class="modal-body pb-0">

                    <!-- Periode + Cari -->
                    <div class="row g-3 mb-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">
                                Periode <span class="text-danger">*</span>
                            </label>
                            <input type="month" name="periode" id="bulkPeriode"
                                class="form-control"
                                value="<?= htmlspecialchars($_GET['periode'] ?? date('Y-m')) ?>"
                                required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cari Pegawai</label>
                            <input type="text" id="bulkSearchGaji" class="form-control"
                                placeholder="Ketik nama / jabatan...">
                        </div>
                        <div class="col-md-3 d-flex gap-2 align-items-end">
                            <button type="button" class="btn btn-outline-success btn-sm w-50"
                                id="btnCeklisSemua" title="Centang semua yang belum digaji">
                                <i class="bi bi-check2-all"></i> Semua
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm w-50"
                                id="btnResetSemua" title="Hapus centang semua">
                                <i class="bi bi-x-lg"></i> Reset
                            </button>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-primary btn-sm w-100"
                                id="btnMuatData">
                                <i class="bi bi-arrow-clockwise me-1"></i>Muat Data
                            </button>
                        </div>
                    </div>

                    <!-- Keterangan warna baris -->
                    <div class="d-flex gap-3 flex-wrap mb-3 small text-muted">
                        <span><i class="bi bi-square-fill text-danger me-1"></i>Sudah digaji periode ini</span>
                        <span><i class="bi bi-square-fill text-success me-1"></i>Dipilih untuk diproses</span>
                        <span class="ms-auto">
                            <i class="bi bi-info-circle me-1"></i>
                            Gaji Pokok &amp; Tunjangan otomatis dari Golongan pegawai
                        </span>
                    </div>

                    <!-- Loading indicator -->
                    <div id="bulkGajiLoading" class="text-center py-3" style="display:none">
                        <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                        Memuat status penggajian...
                    </div>

                    <!-- Counter -->
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small text-muted">
                            Total pegawai: <strong><?= count($users) ?></strong>
                        </span>
                        <span class="badge bg-primary-subtle text-primary" id="bulkGajiSelectedCount">
                            0 dipilih
                        </span>
                    </div>
                </div>

                <!-- ---- TABEL PEGAWAI ---- -->
                <div class="modal-body pt-0" style="max-height:420px;overflow-y:auto">
                    <table class="table table-sm table-hover align-middle mb-0" id="tabelBulkGaji">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th style="width:38px">
                                    <input type="checkbox" class="form-check-input" id="chkAllGaji"
                                        title="Centang/batal semua yang aktif">
                                </th>
                                <th style="width:32px">#</th>
                                <th>Nama Pegawai</th>
                                <th>Jabatan / Golongan</th>
                                <th style="width:90px">Pot. Lain</th>
                                <th style="width:120px">Keterangan</th>
                                <th style="width:90px">Status</th>
                            </tr>
                        </thead>
                        <tbody id="tabelBulkGajiBody">
                            <?php foreach ($users as $i => $u): ?>
                                <?php
                                $uid     = $u->getId();
                                $jabatan = htmlspecialchars($u->getNamaJabatan() ?? $u->getRole());
                                $namaEsc = htmlspecialchars($u->getNama());
                                ?>
                                <tr class="bulk-row-gaji"
                                    data-uid="<?= $uid ?>"
                                    data-nama="<?= strtolower($u->getNama()) ?>"
                                    data-jabatan="<?= strtolower($u->getNamaJabatan() ?? '') ?>">

                                    <!-- Checkbox -->
                                    <td>
                                        <input type="checkbox"
                                            class="form-check-input bulk-chk-gaji"
                                            data-uid="<?= $uid ?>"
                                            id="chkGaji_<?= $uid ?>">
                                    </td>

                                    <!-- Nomor -->
                                    <td class="text-muted small"><?= $i + 1 ?></td>

                                    <!-- Nama -->
                                    <td>
                                        <label for="chkGaji_<?= $uid ?>" class="mb-0 fw-semibold small cursor-pointer">
                                            <?= $namaEsc ?>
                                        </label>
                                        <div class="text-muted" style="font-size:.72rem">
                                            <?= htmlspecialchars($u->getNip() ?: '-') ?>
                                        </div>
                                    </td>

                                    <!-- Jabatan / Golongan -->
                                    <td class="small text-muted"><?= $jabatan ?></td>

                                    <!-- Potongan Lain -->
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm bulk-potlain"
                                            data-uid="<?= $uid ?>"
                                            placeholder="0"
                                            disabled>
                                    </td>

                                    <!-- Keterangan -->
                                    <td>
                                        <input type="text"
                                            class="form-control form-control-sm bulk-ket-gaji"
                                            data-uid="<?= $uid ?>"
                                            placeholder="Opsional"
                                            disabled>
                                    </td>

                                    <!-- Badge status gaji -->
                                    <td class="text-center">
                                        <span class="badge-status-gaji" data-uid="<?= $uid ?>">
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
                        Pegawai yang sudah memiliki penggajian periode ini tidak dapat dipilih.
                    </small>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanBulkGaji" disabled>
                        <i class="bi bi-save me-1"></i>Simpan Penggajian
                    </button>
                </div>

            </form><!-- /formBulkPenggajian -->
        </div>
    </div>
</div>

<script>
    (function() {
        'use strict';

        /* ── State ─────────────────────────────────────── */
        let sudahDigajiIds = []; // user_id yang sudah punya gaji di periode terpilih
        let currentPeriode = document.getElementById('bulkPeriode').value;

        /* ── Helpers ───────────────────────────────────── */
        const rows = () => [...document.querySelectorAll('.bulk-row-gaji')];
        const chkAll = document.getElementById('chkAllGaji');
        const countBadge = document.getElementById('bulkGajiSelectedCount');
        const btnSimpan = document.getElementById('btnSimpanBulkGaji');

        function updateCounter() {
            const n = document.querySelectorAll('.bulk-chk-gaji:checked').length;
            countBadge.textContent = n + ' dipilih';
            btnSimpan.disabled = n === 0;
        }

        function isSudahDigaji(uid) {
            return sudahDigajiIds.includes(Number(uid));
        }

        function applyRowState(row) {
            const uid = row.dataset.uid;
            const chk = row.querySelector('.bulk-chk-gaji');
            const pot = row.querySelector('.bulk-potlain');
            const ket = row.querySelector('.bulk-ket-gaji');
            const badge = row.querySelector('.badge-status-gaji');

            if (isSudahDigaji(uid)) {
                row.classList.add('table-danger', 'opacity-75');
                row.classList.remove('table-success');
                chk.disabled = true;
                chk.checked = false;
                pot.disabled = true;
                ket.disabled = true;
                badge.innerHTML = '<span class="badge bg-danger">Sudah Digaji</span>';
            } else {
                row.classList.remove('table-danger', 'opacity-75');
                chk.disabled = false;
                const checked = chk.checked;
                pot.disabled = !checked;
                ket.disabled = !checked;
                row.classList.toggle('table-success', checked);
                badge.innerHTML = '<span class="badge bg-secondary-subtle text-secondary">-</span>';
            }
        }

        /* ── Fetch status penggajian ──────────────────── */
        function fetchStatusPeriode(periode) {
            const loading = document.getElementById('bulkGajiLoading');
            loading.style.display = '';

            fetch(base('/transaksi/penggajian/status-periode') + '?periode=' + encodeURIComponent(periode))
                .then(r => r.json())
                .then(data => {
                    sudahDigajiIds = data.sudah_digaji || [];
                    rows().forEach(row => applyRowState(row));
                    updateCounter();
                })
                .catch(() => {
                    sudahDigajiIds = [];
                })
                .finally(() => {
                    loading.style.display = 'none';
                });
        }

        /* ── Event: modal terbuka ──────────────────────── */
        document.getElementById('modalBulk').addEventListener('show.bs.modal', () => {
            currentPeriode = document.getElementById('bulkPeriode').value;
            fetchStatusPeriode(currentPeriode);
        });

        /* ── Event: periode berubah ────────────────────── */
        document.getElementById('bulkPeriode').addEventListener('change', function() {
            currentPeriode = this.value;
            document.querySelectorAll('.bulk-chk-gaji').forEach(c => {
                c.checked = false;
            });
            chkAll.checked = false;
            fetchStatusPeriode(currentPeriode);
        });

        /* ── Tombol Muat Data ─────────────────────────── */
        document.getElementById('btnMuatData').addEventListener('click', () => {
            fetchStatusPeriode(document.getElementById('bulkPeriode').value);
        });

        /* ── Event: checkbox individual ───────────────── */
        document.getElementById('tabelBulkGajiBody').addEventListener('change', function(e) {
            if (!e.target.classList.contains('bulk-chk-gaji')) return;
            const row = e.target.closest('tr');
            applyRowState(row);
            updateCounter();
            const enabledChks = [...document.querySelectorAll('.bulk-chk-gaji:not(:disabled)')];
            chkAll.checked = enabledChks.length > 0 && enabledChks.every(c => c.checked);
            chkAll.indeterminate = enabledChks.some(c => c.checked) && !chkAll.checked;
        });

        /* ── Event: chkAll ────────────────────────────── */
        chkAll.addEventListener('change', function() {
            document.querySelectorAll('.bulk-chk-gaji:not(:disabled)').forEach(c => {
                const row = c.closest('tr');
                if (row.style.display !== 'none') {
                    c.checked = this.checked;
                    applyRowState(row);
                }
            });
            updateCounter();
        });

        /* ── Centang Semua / Reset ────────────────────── */
        document.getElementById('btnCeklisSemua').addEventListener('click', () => {
            chkAll.checked = true;
            chkAll.dispatchEvent(new Event('change'));
        });
        document.getElementById('btnResetSemua').addEventListener('click', () => {
            chkAll.checked = false;
            chkAll.dispatchEvent(new Event('change'));
        });

        /* ── Pencarian ────────────────────────────────── */
        document.getElementById('bulkSearchGaji').addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            rows().forEach(row => {
                const match = !q ||
                    row.dataset.nama.includes(q) ||
                    row.dataset.jabatan.includes(q);
                row.style.display = match ? '' : 'none';
            });
            const visibleEnabled = [...document.querySelectorAll('.bulk-chk-gaji:not(:disabled)')]
                .filter(c => c.closest('tr').style.display !== 'none');
            chkAll.checked = visibleEnabled.length > 0 && visibleEnabled.every(c => c.checked);
            chkAll.indeterminate = visibleEnabled.some(c => c.checked) && !chkAll.checked;
        });

        /* ── Submit: build hidden inputs ──────────────── */
        document.getElementById('formBulkPenggajian').addEventListener('submit', function(e) {
            this.querySelectorAll('input[name^="rows["]').forEach(el => el.remove());

            let idx = 0;
            document.querySelectorAll('.bulk-chk-gaji:checked').forEach(chk => {
                const uid = chk.dataset.uid;
                const pot = document.querySelector(`.bulk-potlain[data-uid="${uid}"]`);
                const ket = document.querySelector(`.bulk-ket-gaji[data-uid="${uid}"]`);
                const potVal = pot ? (parseFloat(pot.value) || 0) : 0;
                const ketVal = ket ? ket.value : '';

                const append = (name, val) => {
                    const inp = document.createElement('input');
                    inp.type = 'hidden';
                    inp.name = name;
                    inp.value = val;
                    this.appendChild(inp);
                };

                append(`rows[${idx}][user_id]`, uid);
                append(`rows[${idx}][potongan_lain]`, potVal);
                append(`rows[${idx}][keterangan]`, ketVal);
                idx++;
            });

            if (idx === 0) {
                e.preventDefault();
                alert('Pilih minimal satu pegawai terlebih dahulu.');
            }
        });

        if (typeof base === 'undefined') {
            window.base = p => p;
        }
    })();
</script>