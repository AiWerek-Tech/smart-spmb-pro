<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Kesalahan Server | Smart SPMB Pro</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Arial, sans-serif;
            background-color: #f8f9fa;
            color: #343a40;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .error-container {
            text-align: center;
            max-width: 500px;
            background: #fff;
            padding: 48px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
        }
        .error-code { font-size: 96px; font-weight: 700; color: #fd7e14; line-height: 1; margin-bottom: 16px; }
        .error-title { font-size: 24px; font-weight: 600; margin-bottom: 12px; }
        .error-message { font-size: 16px; color: #6c757d; line-height: 1.6; margin-bottom: 32px; }
        .btn { display: inline-block; padding: 12px 28px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 8px; font-size: 15px; }
        .icon { font-size: 64px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="icon">⚠️</div>
        <div class="error-code">500</div>
        <h1 class="error-title">Kesalahan Server</h1>
        <p class="error-message">Terjadi kesalahan pada server. Tim teknis kami sedang menangani masalah ini. Silakan coba lagi beberapa saat.</p>
        <a href="<?= base_url('/') ?>" class="btn">Kembali ke Beranda</a>
    </div>
</body>
</html>
