<?php
// auth/register.php — Register Page
// $error disiapkan oleh AuthController::registerPage() sebelum require file ini
$error = $error ?? ($_SESSION['flash_error'] ?? null);
unset($_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — Satria Wisata Transport</title>
    <link rel="stylesheet" href="<?= url('08Bsui/wwwroot/css/app.css') ?>">
</head>

<body class="auth-page">

    <div class="bg-grid"></div>
    <div class="bg-mesh"></div>
    <div class="orb orb-a"></div>
    <div class="orb orb-b"></div>

    <div class="auth-shell">

        <!-- ── LEFT SIDEBAR ── -->
        <div class="side-panel">

            <div class="brand-row">
                <div class="brand-ico">🚌</div>
                <div>
                    <div class="brand-nm">Satria Wisata</div>
                    <div class="brand-sub">Transport</div>
                </div>
            </div>

            <h2 class="side-heading">Mulai<br>Perjalanan <span class="ac">Anda</span></h2>
            <p class="side-desc">Bergabung dan nikmati kemudahan memesan tiket bus kapan saja, di mana saja.</p>

            <div class="perk-list">
                <div class="perk-item">
                    <div class="perk-badge">🎟️</div>
                    <span class="perk-txt">Gratis mendaftar, tanpa biaya</span>
                </div>
                <div class="perk-item">
                    <div class="perk-badge">🗺️</div>
                    <span class="perk-txt">Akses semua rute tersedia</span>
                </div>
                <div class="perk-item">
                    <div class="perk-badge">💺</div>
                    <span class="perk-txt">Pilih kursi favorit sendiri</span>
                </div>
                <div class="perk-item">
                    <div class="perk-badge">🔔</div>
                    <span class="perk-txt">Notifikasi status perjalanan</span>
                </div>
            </div>

            <a href="<?= url('/login') ?>" class="back-link">← Kembali ke halaman masuk</a>

        </div>

        <!-- ── MAIN FORM ── -->
        <div class="main-area">
            <div class="register-card">

                <div class="card-badge"><span class="badge-dot"></span> Registrasi Akun</div>
                <h1 class="reg-card-title">Buat Akun Baru</h1>
                <p class="card-sub">Lengkapi data diri Anda untuk melanjutkan</p>

                <?php if ($error): ?>
                    <div class="reg-alert">
                        <span>⚠️</span> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/register') ?>" class="form-body" id="regForm">

                    <!-- Row 1: Nama & Username -->
                    <div class="frow">
                        <div>
                            <label class="field-lbl" for="reg-nama">Nama Lengkap <span class="req">*</span></label>
                            <div class="field-box">
                                <input type="text" id="reg-nama" name="nama" class="reg-input"
                                    placeholder="Nama lengkap Anda" required autocomplete="name">
                            </div>
                        </div>
                        <div>
                            <label class="field-lbl" for="reg-username">Username <span class="req">*</span></label>
                            <div class="field-box">
                                <input type="text" id="reg-username" name="username" class="reg-input"
                                    placeholder="username unik" required autocomplete="username"
                                    pattern="[a-zA-Z0-9_\-]+" title="Hanya huruf, angka, underscore, dan strip">
                            </div>
                        </div>
                    </div>

                    <div class="section-divider"><span class="sdivider-txt">Kontak (opsional)</span></div>

                    <!-- Row 2: Email & No Telp -->
                    <div class="frow">
                        <div>
                            <label class="field-lbl" for="reg-email">Email</label>
                            <div class="field-box">
                                <input type="email" id="reg-email" name="email" class="reg-input"
                                    placeholder="email@contoh.com" autocomplete="email">
                            </div>
                        </div>
                        <div>
                            <label class="field-lbl" for="reg-telp">No. Telepon</label>
                            <div class="field-box">
                                <input type="tel" id="reg-telp" name="no_telp" class="reg-input"
                                    placeholder="08xx-xxxx-xxxx" autocomplete="tel">
                            </div>
                        </div>
                    </div>

                    <div class="section-divider"><span class="sdivider-txt">Keamanan akun</span></div>

                    <!-- Row 3: Password & Konfirmasi -->
                    <div class="frow">
                        <div>
                            <label class="field-lbl" for="reg-pwd">Password <span class="req">*</span></label>
                            <div class="field-box">
                                <input type="password" id="reg-pwd" name="password" class="reg-input has-eye"
                                    placeholder="Min. 6 karakter" required minlength="6" autocomplete="new-password"
                                    oninput="checkStrength(this.value)">
                                <button type="button" class="eye-toggle" onclick="togglePwd('reg-pwd',this)">👁</button>
                            </div>
                            <div class="pwd-hint" id="pwd-strength-msg">Masukkan password minimal 6 karakter</div>
                        </div>
                        <div>
                            <label class="field-lbl" for="reg-pwd2">Konfirmasi Password <span class="req">*</span></label>
                            <div class="field-box">
                                <input type="password" id="reg-pwd2" name="password_confirm" class="reg-input has-eye"
                                    placeholder="Ulangi password" required autocomplete="new-password"
                                    oninput="checkConfirm(this.value)">
                                <button type="button" class="eye-toggle" onclick="togglePwd('reg-pwd2',this)">👁</button>
                            </div>
                            <div class="pwd-hint" id="pwd-match-msg"></div>
                        </div>
                    </div>

                    <button type="submit" class="btn-reg" id="btnReg">
                        Daftar Sekarang →
                    </button>

                </form>

                <div class="form-footer">
                    Sudah punya akun? <a href="<?= url('/login') ?>">Masuk di sini</a>
                </div>
                <p class="terms-note">Dengan mendaftar, Anda menyetujui syarat dan ketentuan layanan Satria Wisata Transport.</p>

            </div>
        </div>

    </div>

    <script>
        // Semua fungsi di bawah hanya dipakai di halaman register, jadi tetap di sini

        function togglePwd(id, btn) {
            const inp = document.getElementById(id);
            if (inp.type === 'password') {
                inp.type = 'text';
                btn.textContent = '🙈';
            } else {
                inp.type = 'password';
                btn.textContent = '👁';
            }
        }

        function checkStrength(val) {
            const el = document.getElementById('pwd-strength-msg');
            if (!val) {
                el.textContent = 'Masukkan password minimal 6 karakter';
                el.style.color = 'rgba(255,255,255,.2)';
                return;
            }
            if (val.length < 6) {
                el.textContent = '⚠ Password terlalu pendek';
                el.style.color = '#fca5a5';
            } else if (val.length < 10) {
                el.textContent = '✓ Password cukup';
                el.style.color = '#fcd34d';
            } else {
                el.textContent = '✓✓ Password kuat';
                el.style.color = '#6ee7b7';
            }
        }

        function checkConfirm(val) {
            const pwd = document.getElementById('reg-pwd').value;
            const el = document.getElementById('pwd-match-msg');
            if (!val) {
                el.textContent = '';
                return;
            }
            if (val === pwd) {
                el.textContent = '✓ Password cocok';
                el.style.color = '#6ee7b7';
            } else {
                el.textContent = '✗ Password tidak cocok';
                el.style.color = '#fca5a5';
            }
        }

        // Cegah submit jika password tidak cocok
        document.getElementById('regForm').addEventListener('submit', function(e) {
            const p1 = document.getElementById('reg-pwd').value;
            const p2 = document.getElementById('reg-pwd2').value;
            if (p1 !== p2) {
                e.preventDefault();
                const el = document.getElementById('pwd-match-msg');
                el.textContent = '✗ Password tidak cocok!';
                el.style.color = '#fca5a5';
                document.getElementById('reg-pwd2').focus();
            }
        });
    </script>

</body>

</html>