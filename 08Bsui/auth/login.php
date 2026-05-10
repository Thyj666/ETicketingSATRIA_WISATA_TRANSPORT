<?php
// auth/login.php — Login Page
// BUG FIX: tambahkan pembacaan flash_success (sebelumnya tidak dibaca)
$success = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Satria Wisata Transport</title>
    <!-- BUG FIX: url() dipanggil dengan leading slash agar path absolut -->
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
            margin-bottom: 48px;
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

        .auth-route-visual {
            display: flex;
            align-items: center;
            gap: 0;
            margin-top: 8px;
        }

        .route-dot {
            width: 12px;
            height: 12px;
            background: var(--amber);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--amber);
        }

        .route-dot-dest {
            background: var(--white);
            box-shadow: 0 0 10px rgba(255, 255, 255, .5);
        }

        .route-line {
            flex: 1;
            height: 2px;
            background: rgba(255, 255, 255, .2);
        }

        .route-bus {
            font-size: 1.4rem;
            margin: 0 6px;
            animation: busMove 3s ease-in-out infinite alternate;
        }

        @keyframes busMove {
            from {
                transform: translateX(-4px);
            }

            to {
                transform: translateX(4px);
            }
        }

        .auth-right {
            width: 480px;
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
            margin-bottom: 28px;
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
            gap: 18px;
        }

        /* Input with icon */
        .input-icon-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1rem;
            pointer-events: none;
            z-index: 1;
        }

        .input-has-icon {
            padding-left: 38px !important;
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
    </style>
</head>

<body class="auth-body">

    <div class="auth-split">
        <!-- Left panel -->
        <div class="auth-left">
            <div class="auth-left-inner">
                <div class="auth-brand-icon">🚌</div>
                <h1 class="auth-hero-title">Satria Wisata<br>Transport</h1>
                <p class="auth-hero-sub">Sistem manajemen tiket dan armada bus perjalanan terpercaya</p>
                <div class="auth-features">
                    <div class="auth-feature"><span>✓</span> Pemesanan tiket mudah &amp; cepat</div>
                    <div class="auth-feature"><span>✓</span> Pilih kursi favorit Anda</div>
                    <div class="auth-feature"><span>✓</span> Pembayaran aman &amp; terjamin</div>
                    <div class="auth-feature"><span>✓</span> Riwayat perjalanan lengkap</div>
                </div>
                <div class="auth-route-visual">
                    <span class="route-dot"></span>
                    <span class="route-line"></span>
                    <span class="route-bus">🚌</span>
                    <span class="route-line"></span>
                    <span class="route-dot route-dot-dest"></span>
                </div>
            </div>
        </div>

        <!-- Right panel -->
        <div class="auth-right">
            <div class="auth-card">
                <div class="auth-card-header">
                    <h2 class="auth-title">Selamat Datang</h2>
                    <p class="auth-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger" style="margin-bottom:18px;"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success" style="margin-bottom:18px;"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/login') ?>" class="auth-form">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">👤</span>
                            <input type="text" name="username" class="form-input input-has-icon"
                                placeholder="Masukkan username" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">🔒</span>
                            <input type="password" name="password" id="inp-password"
                                class="form-input input-has-icon"
                                placeholder="Masukkan password" required>
                            <button type="button" class="input-eye" onclick="togglePwd('inp-password',this)">👁</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full btn-lg" style="margin-top:4px;">
                        Masuk
                    </button>
                </form>

                <div class="auth-footer">
                    Belum punya akun? <a href="<?= url('/register') ?>" class="auth-link">Daftar sekarang</a>
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