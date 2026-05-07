<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server | SIMPEG SMAN 7 Bungo</title>
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
            max-width: 560px;
            width: 100%;
            text-align: center;
        }

        .error-code {
            font-size: 6rem;
            font-weight: 700;
            color: #64748b;
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

        .debug-box {
            background: #1e293b;
            color: #94a3b8;
            border-radius: 8px;
            padding: 1rem 1.25rem;
            font-family: monospace;
            font-size: .82rem;
            text-align: left;
            margin-top: 1rem;
            max-height: 160px;
            overflow-y: auto;
        }

        .debug-box .label {
            color: #f1f5f9;
            font-weight: 600;
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
            background: #f1f5f9;
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
            <i class="bi bi-gear-fill" style="font-size:2rem;color:#64748b"></i>
        </div>
        <div class="error-code">500</div>
        <div class="error-title">Kesalahan Internal Server</div>
        <p class="error-desc">
            Terjadi kesalahan pada server saat memproses permintaan Anda.
            Tim teknis telah diberitahu. Silakan coba beberapa saat lagi.
        </p>

        <?php if (defined('DEBUG_MODE') && DEBUG_MODE && isset($errorMessage)): ?>
            <div class="debug-box">
                <span class="label">Debug Info:</span><br>
                <?= htmlspecialchars($errorMessage) ?>
            </div>
        <?php endif; ?>

        <a href="<?= rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? ''), '/') ?>/dashboard" class="btn-home">
            <i class="bi bi-arrow-clockwise me-1"></i> Kembali ke Dashboard
        </a>
    </div>
</body>

</html>