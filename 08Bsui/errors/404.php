<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 — Halaman Tidak Ditemukan</title>
    <link rel="stylesheet" href="<?= url('/08Bsui/wwwroot/css/app.css') ?>">
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            background: var(--gray-100);
        }

        .error-box {
            text-align: center;
            padding: 60px 40px;
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            max-width: 420px;
        }

        .error-code {
            font-family: 'Sora', sans-serif;
            font-size: 5rem;
            font-weight: 800;
            color: var(--navy);
            line-height: 1;
            margin-bottom: 12px;
        }

        .error-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 8px;
        }

        .error-desc {
            font-size: .88rem;
            color: var(--gray-500);
            margin-bottom: 28px;
        }
    </style>
</head>

<body>
    <div class="error-box">
        <div class="error-code">404</div>
        <div class="error-title">Halaman Tidak Ditemukan</div>
        <div class="error-desc">Halaman yang Anda cari tidak ada atau telah dipindahkan.</div>
        <a href="<?= url('/dashboard') ?>" class="btn btn-primary">Kembali ke Dashboard</a>
    </div>
</body>

</html>