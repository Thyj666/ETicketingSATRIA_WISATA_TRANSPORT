<?php
// auth/login.php — Login Page
$success = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Satria Wisata Transport</title>
    <link rel="stylesheet" href="<?= url('08Bsui/wwwroot/css/app.css') ?>">
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
                    <div class="auth-feature"><span>✓</span> Pemesanan tiket mudah & cepat</div>
                    <div class="auth-feature"><span>✓</span> Pilih kursi favorit Anda</div>
                    <div class="auth-feature"><span>✓</span> Pembayaran aman & terjamin</div>
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

                <?php if ($error ?? null): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/login') ?>" class="auth-form">
                    <div class="form-group">
                        <label class="form-label">Username</label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">👤</span>
                            <input type="text" name="username" class="form-input input-has-icon" placeholder="Masukkan username" required autofocus>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password</label>
                        <div class="input-icon-wrap">
                            <span class="input-icon">🔒</span>
                            <input type="password" name="password" id="inp-password" class="form-input input-has-icon" placeholder="Masukkan password" required>
                            <button type="button" class="input-eye" onclick="togglePwd('inp-password',this)">👁</button>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full btn-lg">Masuk</button>
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