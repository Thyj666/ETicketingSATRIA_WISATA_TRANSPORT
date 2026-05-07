<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | SIMPEG SMAN 7 Bungo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a5f;
            --accent: #e8a020;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f0f4f8;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .error-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(30, 58, 95, .08);
            padding: 3rem 2.5rem;
            max-width: 520px;
            width: 100%;
            text-align: center;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #ef4444;
            line-height: 1;
        }

        .error-title {
            font-size: 1.4rem;
            font-weight: 600;
            color: var(--primary);
            margin: .75rem 0 .5rem;
        }

        .error-desc {
            color: #64748b;
            font-size: .95rem;
            line-height: 1.7;
        }

        .role-badge {
            display: inline-block;
            background: #fef2f2;
            color: #b91c1c;
            border-radius: 6px;
            padding: .25rem .75rem;
            font-size: .85rem;
            font-weight: 500;
            margin-top: .5rem;
        }

        .btn-back {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: .6rem 1.6rem;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            margin-top: 1.5rem;
            transition: background .2s;
        }

        .btn-back:hover {
            background: #2a5298;
            color: #fff;
        }

        .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fef2f2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }
    </style>
</head>

<body>
    <?php $user = \Base\Auth\Auth::user() ?? [];
    $role = $user['role'] ?? ''; ?>
    <div class="error-card">
        <div class="icon-wrap">
            <i class="bi bi-shield-lock-fill" style="font-size:2rem;color:#ef4444"></i>
        </div>
        <div class="error-code">403</div>
        <div class="error-title">Akses Ditolak</div>
        <p class="error-desc">
            Anda tidak memiliki izin untuk mengakses halaman atau fitur ini.
            Hubungi Admin TU jika Anda merasa ini adalah kesalahan.
        </p>
        <?php if ($role): ?>
            <div>
                <span class="role-badge"><i class="bi bi-person-fill me-1"></i>Role Anda: <?= htmlspecialchars($role) ?></span>
            </div>
        <?php endif; ?>
        <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/') ?>/dashboard" class="btn-back">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</body>

</html>