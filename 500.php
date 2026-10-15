<?php
// 500 Error Page - Custom 500 for Alfayyadh.ID
$pageTitle = 'Kesalahan Server';
$pageDescription = 'Terjadi kesalahan internal pada server.';

// Don't include header.php here as it might cause another error
// Simple standalone error page
http_response_code(500);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kesalahan Server - Alfayyadh.ID</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <style>
        :root { --color-primary: #0d9488; --color-primary-dark: #0f766e; --color-muted: #6b7280; --color-bg: #f9fafb; --color-card: #ffffff; }
        body { background: var(--color-bg); font-family: 'Inter', system-ui, -apple-system, sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 2rem; margin: 0; }
        .error-card { background: var(--color-card); border-radius: 1rem; box-shadow: 0 10px 40px rgba(0,0,0,0.08); padding: 3rem 2rem; max-width: 480px; width: 100%; text-align: center; }
        .error-code { font-size: 6rem; font-weight: 800; color: var(--color-primary); line-height: 1; opacity: 0.15; margin-bottom: -1rem; }
        h1 { font-size: 1.75rem; margin-bottom: 1rem; color: #111827; }
        .lead { color: var(--color-muted); margin-bottom: 2rem; }
        .error-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
        .btn { padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; }
        .btn-primary { background: var(--color-primary); border-color: var(--color-primary); color: white; }
        .btn-primary:hover { background: var(--color-primary-dark); border-color: var(--color-primary-dark); }
        .btn-outline-secondary { border: 2px solid #d1d5db; color: #374151; background: transparent; }
        .btn-outline-secondary:hover { background: #f3f4f6; border-color: #9ca3af; }
        .error-help { margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e5e7eb; }
        .error-help small { color: var(--color-muted); line-height: 1.6; }
        .retry-btn { background: none; border: none; color: var(--color-primary); cursor: pointer; font-weight: 600; padding: 0; font-size: inherit; font-family: inherit; }
        .retry-btn:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="error-card">
        <div class="error-code">500</div>
        <h1>Kesalahan Server Internal</h1>
        <p class="lead">Terjadi kesalahan tidak terduga pada server. Tim kami telah diberitahu.</p>
        <div class="error-actions">
            <a href="/" class="btn btn-primary">
                <i class="fa-solid fa-house"></i> Kembali ke Beranda
            </a>
            <button class="btn btn-outline-secondary retry-btn" onclick="location.reload()">
                <i class="fa-solid fa-rotate"></i> Coba Lagi
            </button>
        </div>
        <div class="error-help">
            <small>Jika masalah berlanjut, silakan hubungi kami melalui email <strong>halo@alfayyadh.id</strong> atau coba lagi dalam beberapa menit.</small>
        </div>
    </div>
</body>
</html>
