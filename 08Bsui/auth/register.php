<?php
// auth/register.php — Register Page
// BUG FIX: $error variable harus sudah di-set oleh registerPage() di AuthController
// (variabel $error dikirim dari controller via require, tidak perlu dibaca ulang dari session di sini)
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — Satria Wisata Transport</title>
    <!-- BUG FIX: url() dengan leading slash untuk path absolut -->
    <link rel="stylesheet" href="<?= url('/08Bsui/wwwroot/css/app.css') ?>">
    <style>
        /* ── Tambahan style untuk halaman auth ── */
        .auth-split {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        .auth-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 60px;
            position: relative;
        }

        .auth-left-inner {
            max-width: 420px;
        }

        .auth-brand-icon {
            font-size: 2.5rem;
            width: 64px;
            height: 64px;
            background: var(--amber);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 40px rgba(245, 158, 11, .35);
            margin-bottom: 28px;
        }

        .auth-hero-title {
            font-family: 'Sora', sans-serif;
            font-size: 2.4rem;
            font-weight: 800;
            color: var(--white);
            line-height: 1.2;
            margin-bottom: 16px;
        }

        .auth-hero-sub {
            font-size: .92rem;
            color: rgba(255, 255, 255, .55);
            line-height: 1.7;
            margin-bottom: 36px;
        }

        .auth-features {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .auth-feature {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, .75);
            font-size: .88rem;
        }

        .auth-feature span {
            width: 22px;
            height: 22px;
            background: rgba(245, 158, 11, .2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--amber);
            font-size: .7rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* Register panel wider */
        .auth-right {
            width: 580px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            background: rgba(255, 255, 255, .03);
            backdrop-filter: blur(20px);
            border-left: 1px solid rgba(255, 255, 255, .06);
        }

        .auth-card {
            width: 100%;
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 40px 36px;
            box-shadow: var(--shadow-lg);
        }

        .auth-card-header {
            margin-bottom: 24px;
        }

        .auth-title {
            font-family: 'Sora', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 6px;
        }

        .auth-subtitle {
            font-size: .85rem;
            color: var(--gray-500);
        }

        .auth-form {
            display: flex;
            flex-direction: column;
            gap: 0;
        }

        /* BUG FIX: form-row-2 butuh display:grid (sebelumnya hanya set grid-template-columns) */
        .form-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 16px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        /* Input with eye button */
        .input-icon-wrap {
            position: relative;
        }

        .input-eye {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            font-size: .9rem;
            padding: 4px;
            line-height: 1;
            color: var(--gray-400);
        }

        .input-eye:hover {
            color: var(--gray-700);
        }

        .btn-full {
            width: 100%;
        }

        .btn-lg {
            padding: 12px 20px;
            font-size: .95rem;
        }

        .auth-footer {
            text-align: center;
            font-size: .84rem;
            color: var(--gray-500);
            margin-top: 20px;
        }

        .auth-footer .auth-link {
            color: var(--amber-dark);
            font-weight: 600;
        }

        .auth-footer .auth-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 900px) {
            .auth-left {
                display: none;
            }

            .auth-right {
                width: 100%;
                border-left: none;
            }

            .form-row-2 {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body class="auth-body">

    <div class="auth-split">
        <!-- Left panel -->
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

        <!-- Right panel -->
        <div class="auth-right">
            <div class="auth-card">
                <div class="auth-card-header">
                    <h2 class="auth-title">Buat Akun Baru</h2>
                    <p class="auth-subtitle">Isi data diri Anda dengan benar</p>
                </div>

                <?php if ($error ?? null): ?>
                    <div class="alert alert-danger" style="margin-bottom:18px;"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>

                <!-- BUG FIX: action menggunakan url('/register') dengan leading slash -->
                <form method="POST" action="<?= url('/register') ?>" class="auth-form">
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Nama Lengkap <span class="required">*</span></label>
                            <input type="text" name="nama" class="form-input"
                                placeholder="Nama lengkap" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Username <span class="required">*</span></label>
                            <input type="text" name="username" class="form-input"
                                placeholder="username unik" required>
                        </div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-input"
                                placeholder="email@contoh.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">No. Telepon</label>
                            <input type="tel" name="no_telp" class="form-input"
                                placeholder="08xx-xxxx-xxxx">
                        </div>
                    </div>
                    <div class="form-row-2">
                        <div class="form-group">
                            <label class="form-label">Password <span class="required">*</span></label>
                            <div class="input-icon-wrap">
                                <input type="password" name="password" id="inp-pwd"
                                    class="form-input" placeholder="Min. 6 karakter" required>
                                <button type="button" class="input-eye" onclick="togglePwd('inp-pwd',this)">👁</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Konfirmasi Password <span class="required">*</span></label>
                            <div class="input-icon-wrap">
                                <input type="password" name="password_confirm" id="inp-pwd2"
                                    class="form-input" placeholder="Ulangi password" required>
                                <button type="button" class="input-eye" onclick="togglePwd('inp-pwd2',this)">👁</button>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:8px;">
                        Daftar Sekarang
                    </button>
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