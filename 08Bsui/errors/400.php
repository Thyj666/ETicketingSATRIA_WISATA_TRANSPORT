<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 - Permintaan Tidak Valid | SIMPEG SMAN 7 Bungo</title>
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
            color: var(--accent);
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

        .btn-home {
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

        .btn-home:hover {
            background: #2a5298;
            color: #fff;
        }

        .icon-wrap {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: #fef3e2;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.25rem;
        }
    </style>
</head>

<body>
    <div class="error-card">
        <div class="icon-wrap">
            <i class="bi bi-exclamation-triangle-fill" style="font-size:2rem;color:var(--accent)"></i>
        </div>
        <div class="error-code">400</div>
        <div class="error-title">Permintaan Tidak Valid</div>
        <p class="error-desc">
            Server tidak dapat memproses permintaan karena data yang dikirim tidak valid atau tidak lengkap.
            Periksa kembali data yang Anda masukkan.
        </p>
        <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/') ?>/dashboard" class="btn-home">
            <i class="bi bi-house-fill me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</body>

</html>