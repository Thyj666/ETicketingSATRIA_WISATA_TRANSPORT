<?php
// auth/login.php — Login Page
// $error dan $success disiapkan oleh AuthController::loginPage() sebelum require file ini
$success = $success ?? ($_SESSION['flash_success'] ?? null);
$error   = $error   ?? ($_SESSION['flash_error']   ?? null);
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Satria Wisata Transport</title>
    <link rel="stylesheet" href="<?= url('08Bsui/wwwroot/css/app.css') ?>">
</head>

<body class="auth-page">

    <!-- Background layers -->
    <div class="bg-grid"></div>
    <div class="bg-mesh"></div>
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>

    <div class="auth-shell">

        <!-- ── LEFT HERO ── -->
        <div class="hero-panel">

            <div class="brand-mark">
                <div class="brand-icon">🚌</div>
                <div>
                    <div class="brand-name">Satria Wisata</div>
                    <div class="brand-tagline">Transport System</div>
                </div>
            </div>

            <h1 class="hero-heading">
                Perjalanan Nyaman,<br>
                Tiket <span class="accent">Mudah</span>
            </h1>
            <p class="hero-desc">
                Platform manajemen tiket dan armada bus modern untuk pengalaman perjalanan yang menyenangkan dan terpercaya.
            </p>

            <div class="feature-list">
                <div class="feature-item">
                    <div class="feature-check">✓</div>
                    <span class="feature-text">Pemesanan tiket online mudah &amp; cepat</span>
                </div>
                <div class="feature-item">
                    <div class="feature-check">✓</div>
                    <span class="feature-text">Pilih kursi favorit secara real-time</span>
                </div>
                <div class="feature-item">
                    <div class="feature-check">✓</div>
                    <span class="feature-text">Pembayaran aman &amp; terjamin</span>
                </div>
                <div class="feature-item">
                    <div class="feature-check">✓</div>
                    <span class="feature-text">Riwayat perjalanan lengkap</span>
                </div>
            </div>

            <div class="route-track">
                <span class="rt-dot"></span>
                <span class="rt-line"></span>
                <span class="rt-bus">🚌</span>
                <span class="rt-line"></span>
                <span class="rt-dot rt-dot-end"></span>
            </div>

            <div class="stats-bar">
                <div>
                    <div class="stat-num">500+</div>
                    <div class="stat-lbl">Penumpang</div>
                </div>
                <div>
                    <div class="stat-num">50+</div>
                    <div class="stat-lbl">Armada</div>
                </div>
                <div>
                    <div class="stat-num">30+</div>
                    <div class="stat-lbl">Rute</div>
                </div>
            </div>

        </div>

        <!-- ── RIGHT FORM ── -->
        <div class="form-panel">
            <div class="form-card">

                <h2 class="card-main-title">Selamat<br>Datang Kembali</h2>
                <p class="card-subtitle">Masuk untuk melanjutkan ke dashboard Anda</p>

                <?php if ($error): ?>
                    <div class="auth-alert auth-alert-danger">
                        <span class="alert-ico">⚠️</span>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="auth-alert auth-alert-success">
                        <span class="alert-ico">✅</span>
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= url('/login') ?>" class="auth-form">

                    <div class="form-group">
                        <label class="field-label" for="inp-username">Username</label>
                        <div class="field-wrap">
                            <span class="field-ico">👤</span>
                            <input
                                type="text" id="inp-username" name="username"
                                class="auth-input"
                                placeholder="Masukkan username Anda"
                                required autofocus autocomplete="username">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="field-label" for="inp-password">Password</label>
                        <div class="field-wrap">
                            <span class="field-ico">🔑</span>
                            <input
                                type="password" id="inp-password" name="password"
                                class="auth-input"
                                placeholder="Masukkan password Anda"
                                required autocomplete="current-password">
                            <button type="button" class="eye-btn" onclick="togglePwd('inp-password', this)" title="Tampilkan password">👁</button>
                        </div>
                    </div>

                    <button type="submit" class="btn-login">
                        Masuk Sekarang →
                    </button>

                </form>

                <div class="form-divider" style="margin-top:24px;">
                    <span class="divider-text">belum punya akun?</span>
                </div>

                <div class="form-footer">
                    <a href="<?= url('/register') ?>">Daftar Akun Baru</a>
                </div>

            </div>
        </div>

    </div>

    <script>
        // Toggle show/hide password — hanya dipakai di halaman ini
        function togglePwd(id, btn) {
            const inp = document.getElementById(id);
            if (inp.type === 'password') {
                inp.type = 'text';
                btn.textContent = '🙈';
                btn.title = 'Sembunyikan password';
            } else {
                inp.type = 'password';
                btn.textContent = '👁';
                btn.title = 'Tampilkan password';
            }
        }

        // Input focus glow effect — hanya dipakai di halaman ini
        document.querySelectorAll('.auth-input').forEach(input => {
            const ico = input.closest('.field-wrap')?.querySelector('.field-ico');
            if (!ico) return;
            input.addEventListener('focus', () => ico.style.opacity = '1');
            input.addEventListener('blur', () => ico.style.opacity = '.5');
        });
    </script>

</body>

</html>