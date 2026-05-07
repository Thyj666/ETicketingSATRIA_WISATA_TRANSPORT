<?php
$title = 'Slip Gaji';
$pageTitle = 'Slip Gaji';
$breadcrumbs = ['Transaksi', 'Penggajian', 'Slip Gaji'];

function rpG(float $v): string
{
    return 'Rp ' . number_format($v, 0, ',', '.');
}

// Terbilang function
function terbilang($angka)
{
    $angka = (float)$angka;
    $bilangan = array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas');

    if ($angka < 12) {
        return $bilangan[$angka];
    } elseif ($angka < 20) {
        return $bilangan[$angka - 10] . ' Belas';
    } elseif ($angka < 100) {
        return $bilangan[floor($angka / 10)] . ' Puluh ' . $bilangan[$angka % 10];
    } elseif ($angka < 200) {
        return 'Seratus ' . terbilang($angka - 100);
    } elseif ($angka < 1000) {
        return $bilangan[floor($angka / 100)] . ' Ratus ' . terbilang($angka % 100);
    } elseif ($angka < 2000) {
        return 'Seribu ' . terbilang($angka - 1000);
    } elseif ($angka < 1000000) {
        return terbilang(floor($angka / 1000)) . ' Ribu ' . terbilang($angka % 1000);
    } elseif ($angka < 1000000000) {
        return terbilang(floor($angka / 1000000)) . ' Juta ' . terbilang($angka % 1000000);
    }
    return '';
}

$penggajian = $penggajian ?? null;
if (!$penggajian) {
    echo '<div class="alert alert-danger">Data penggajian tidak ditemukan</div>';
    exit;
}

$totalPotongan = $penggajian->getPotonganAbsensi() + $penggajian->getPotonganLain();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji - <?= htmlspecialchars($penggajian->getNamaUser()) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            .card {
                border: none !important;
                box-shadow: none !important;
            }

            .card-header {
                background: white !important;
                border-bottom: 2px solid #000 !important;
            }

            body {
                padding: 0 !important;
                margin: 0 !important;
            }
        }

        .slip-container {
            max-width: 800px;
            margin: 0 auto;
        }

        .header-title {
            border-bottom: 3px solid #0d6efd;
            display: inline-block;
            padding-bottom: 5px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 200px;
            margin-top: 5px;
        }
    </style>
</head>

<body class="bg-light">
    <div class="container py-4">
        <div class="slip-container">
            <!-- Tombol Print -->
            <div class="no-print mb-3 text-end">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="bi bi-printer"></i> Cetak / Simpan PDF
                </button>
                <a href="<?= base('/transaksi/penggajian') ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Slip Gaji Card -->
            <div class="card shadow">
                <div class="card-header bg-white text-center py-4">
                    <h3 class="mb-1"><?= htmlspecialchars(APP_NAME) ?></h3>
                    <p class="text-muted mb-0">Jl. Pendidikan No. 123, Bungo, Jambi</p>
                    <p class="text-muted small">Telp. (0747) 123456 | Email: sman7bungo@sch.id</p>
                    <hr>
                    <h4 class="header-title">SLIP GAJI PEGAWAI</h4>
                    <p class="mb-0 mt-2">Periode: <?= date('F Y', strtotime($penggajian->getPeriode() . '-01')) ?></p>
                </div>

                <div class="card-body">
                    <!-- Data Pegawai -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 120px;">Nama Pegawai</td>
                                    <td>: <strong><?= htmlspecialchars($penggajian->getNamaUser()) ?></strong></td>
                                </tr>
                                <tr>
                                    <td>NIP</td>
                                    <td>: <?= htmlspecialchars($penggajian->getNip() ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td>Jabatan</td>
                                    <td>: <?= htmlspecialchars($penggajian->getNamaJabatan() ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td style="width: 120px;">Golongan</td>
                                    <td>: <?= htmlspecialchars($penggajian->getNamaGolongan() ?? '-') ?></td>
                                </tr>
                                <tr>
                                    <td>Status</td>
                                    <td>:
                                        <?php if ($penggajian->getStatus() === 'dibayar'): ?>
                                            <span class="badge bg-success">Sudah Dibayar</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php if ($penggajian->getTanggalBayar()): ?>
                                    <tr>
                                        <td>Tgl. Bayar</td>
                                        <td>: <?= date('d/m/Y', strtotime($penggajian->getTanggalBayar())) ?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <!-- Rincian Gaji -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th colspan="2" class="text-center">RINCIAN PENGHASILAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 60%;">Gaji Pokok</td>
                                    <td class="text-end"><?= rpG($penggajian->getGajiPokok()) ?></td>
                                </tr>
                                <tr>
                                    <td>Tunjangan (Terintegrasi dari Golongan)</td>
                                    <td class="text-end"><?= rpG($penggajian->getTunjangan()) ?></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><strong>Total Penghasilan Kotor</strong></td>
                                    <td class="text-end"><strong><?= rpG($penggajian->getGajiPokok() + $penggajian->getTunjangan()) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered mt-3">
                            <thead class="table-light">
                                <tr>
                                    <th colspan="2" class="text-center">POTONGAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 60%;">Potongan Absensi (Alpha, Izin, Sakit)</td>
                                    <td class="text-end text-danger"><?= rpG($penggajian->getPotonganAbsensi()) ?></td>
                                </tr>
                                <tr>
                                    <td>Potongan Lain-lain</td>
                                    <td class="text-end text-danger"><?= rpG($penggajian->getPotonganLain()) ?></td>
                                </tr>
                                <tr class="table-danger">
                                    <td><strong>Total Potongan</strong></td>
                                    <td class="text-end"><strong><?= rpG($totalPotongan) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>

                        <table class="table table-bordered mt-3">
                            <thead class="table-success">
                                <tr>
                                    <th colspan="2" class="text-center">TOTAL GAJI BERSIH</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="width: 60%;"><strong>Gaji Bersih yang Diterima</strong></td>
                                    <td class="text-end"><strong class="fs-5"><?= rpG($penggajian->getTotalGaji()) ?></strong></td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="small text-muted">
                                        <i class="bi bi-info-circle"></i>
                                        Terbilang: <strong><?= ucwords(terbilang($penggajian->getTotalGaji())) ?> Rupiah</strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Keterangan -->
                    <?php if ($penggajian->getKeterangan()): ?>
                        <div class="alert alert-secondary mt-3">
                            <i class="bi bi-chat-text"></i> Keterangan: <?= htmlspecialchars($penggajian->getKeterangan()) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tanda Tangan -->
                <div class="card-footer bg-white">
                    <div class="row mt-3">
                        <div class="col-6 text-center">
                            <p class="mb-4">Pegawai,</p>
                            <div style="height: 40px;"></div>
                            <p class="fw-bold mb-0"><?= htmlspecialchars($penggajian->getNamaUser()) ?></p>
                            <p class="small text-muted">NIP. <?= htmlspecialchars($penggajian->getNip() ?? '-') ?></p>
                        </div>
                        <div class="col-6 text-center">
                            <p class="mb-4">Mengetahui,<br>Kepala Sekolah</p>
                            <div style="height: 40px;"></div>
                            <p class="fw-bold mb-0">___________________</p>
                            <p class="small text-muted">NIP. __________________</p>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <small class="text-muted">Slip gaji ini dicetak secara otomatis oleh sistem <?= APP_NAME ?></small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>