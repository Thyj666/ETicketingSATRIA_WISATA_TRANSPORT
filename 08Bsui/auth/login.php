<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMPEG SMAN 7 Bungo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1e3a5f 0%, #2a5298 50%, #1e3a5f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='28'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .login-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #1e3a5f, #2a5298);
            padding: 32px 32px 24px;
            text-align: center;
        }

        .login-header .logo {
            width: 60px;
            height: 60px;
            background: #e8a020;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            font-weight: 800;
            color: #1e3a5f;
            margin: 0 auto 12px;
        }

        .login-header h4 {
            color: #fff;
            font-weight: 700;
            margin: 0;
            font-size: 1.1rem;
        }

        .login-header p {
            color: rgba(255, 255, 255, .65);
            font-size: .78rem;
            margin: 4px 0 0;
        }

        .login-body {
            padding: 28px 32px 32px;
        }

        .form-label {
            font-size: .82rem;
            font-weight: 600;
            color: #4a5568;
        }

        .form-control {
            border-radius: 10px;
            border: 1.5px solid #e2e8f0;
            padding: 10px 14px;
            font-size: .88rem;
            transition: all .2s;
        }

        .form-control:focus {
            border-color: #2a5298;
            box-shadow: 0 0 0 3px rgba(42, 82, 152, .1);
        }

        .input-group .input-group-text {
            background: #f8fafc;
            border: 1.5px solid #e2e8f0;
            border-right: none;
            border-radius: 10px 0 0 10px;
            color: #718096;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .input-group .form-control:focus {
            border-color: #2a5298;
        }

        .input-group:focus-within .input-group-text {
            border-color: #2a5298;
        }

        .btn-login {
            background: linear-gradient(135deg, #1e3a5f, #2a5298);
            border: none;
            color: #fff;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            font-size: .9rem;
            width: 100%;
            transition: all .2s;
        }

        .btn-login:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(42, 82, 152, .4);
            color: #fff;
        }

        .alert {
            border-radius: 10px;
            font-size: .83rem;
            border: none;
        }

        .hint-box {
            background: #f0f7ff;
            border: 1px solid #bfdbfe;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: .75rem;
            color: #1e40af;
            margin-top: 16px;
        }

        .hint-box strong {
            display: block;
            margin-bottom: 4px;
            color: #1e3a5f;
        }

        .footer-text {
            text-align: center;
            font-size: .72rem;
            color: #a0aec0;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="logo">S7</div>
                <h4>SIMPEG SMAN 7 Bungo</h4>
                <p>Sistem Informasi Penggajian Guru &amp; Staff</p>
            </div>
            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger d-flex align-items-center gap-2 mb-4">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= rtrim(dirname($_SERVER['SCRIPT_NAME']), '/') ?>/login">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="username" class="form-control"
                                placeholder="Masukkan username" required autofocus
                                value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" name="password" id="pwInput" class="form-control"
                                placeholder="Masukkan password" required>
                            <button type="button" class="input-group-text border-start-0 bg-white"
                                onclick="togglePw()" style="cursor:pointer;border:1.5px solid #e2e8f0;border-left:none;border-radius:0 10px 10px 0">
                                <i class="bi bi-eye" id="pwIcon"></i>
                            </button>
                        </div>
                    </div>
                    <button type="submit" class="btn-login">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                    </button>
                </form>

                <!-- <div class="hint-box">
                    <strong><i class="bi bi-info-circle me-1"></i>Akun Default</strong>
                    Admin TU: <code>admin</code> / <code>admin123</code><br>
                    Kepala Sekolah: <code>kepala</code> / <code>kepala123</code><br>
                    Guru: <code>guru1</code> / <code>guru123</code>
                </div> -->
            </div>
        </div>
        <div class="footer-text">
            &copy; <?= date('Y') ?> SMAN 7 Bungo &mdash; Sistem Informasi Penggajian
        </div>
    </div>
    <script>
        function togglePw() {
            const inp = document.getElementById('pwInput');
            const icon = document.getElementById('pwIcon');
            if (inp.type === 'password') {
                inp.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                inp.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>

</html>