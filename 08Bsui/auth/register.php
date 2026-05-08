<?php
// auth/register.php — Register Page
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — Satria Wisata Transport</title>
    <link rel="stylesheet" href="<?= url('08Bsui/wwwroot/css/app.css') ?>">
</head>

<body class="auth-body">

    <div class="auth-split">
        <div class="auth-left">
            <div class="auth-left-inner">
                <div class="auth-brand-icon">🚌</div>
                <h1 class="auth-hero-title">Bergabung<br>Bersama Kami</h1>
                <p class="auth-hero-sub">Daftarkan diri dan nikmati kemudahan memesan tiket bus perjalanan</p>
                <div class="auth-features">
                    <div class="auth-feature"><span>✓</span> Gratis mendaftar</div>
                    <div class="auth-feature"><span>✓</span> Akses semua rute tersedia</div>
                    <div class="auth-feature"><span>✓</span> Pilih kursi sendiri</div>
                    <div class="auth-feature"><span>✓</span> Notifikasi real-time</div>
                </div>
            </div>
        </div>

        <div class="auth-right">
            <div class="auth-card auth-card-wide">
                <div class="auth-card-header">
                    <h2 class="auth-title">Buat Akun Baru</h2>
                    <p class="auth-subtitle">Isi data diri Anda dengan benar</p>
                </div>

                <?php if ($error ?? null): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/register') ?>" class="auth-form">
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
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Password <span class="required">*</span></label>
                            <div class="input-icon-wrap">
                                <input type="password" name="password" id="inp-pwd" class="form-input" placeholder="Min. 6 karakter" required>
                                <button type="button" class="input-eye" onclick="togglePwd('inp-pwd',this)">👁</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password <span class="required">*</span></label>
                            <div class="input-icon-wrap">
                                <input type="password" name="password_confirm" id="inp-pwd2" class="form-input" placeholder="Ulangi password" required>
                                <button type="button" class="input-eye" onclick="togglePwd('inp-pwd2',this)">👁</button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:8px">Daftar Sekarang</button>
                </form>

                <div class="auth-footer">
                    Sudah punya akun? <a href="<?= url('/login') ?>" class="auth-link">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>

    <script>
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
    </script>
</body>

</html>