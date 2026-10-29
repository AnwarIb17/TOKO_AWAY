<?php
$errorMessage = 'Koneksi database gagal.';
if (isset($e) && $e instanceof Throwable) {
    $errorMessage = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database belum siap — <?= e(APP_NAME) ?></title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #0A0A0B; color: #F3EEE4; font-family: system-ui, -apple-system, 'Segoe UI', sans-serif; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { width: 100%; max-width: 560px; border: 1px solid #262420; background: #141416; border-radius: 16px; padding: 36px; }
        .label { font-size: 10px; letter-spacing: .3em; text-transform: uppercase; color: #C6A664; margin: 0 0 14px; }
        h1 { font-family: Georgia, serif; font-weight: 300; font-size: 34px; margin: 0 0 12px; }
        p { font-size: 14px; line-height: 1.7; color: #8E887D; margin: 0 0 14px; }
        code { background: #0A0A0B; border: 1px solid #262420; border-radius: 6px; padding: 2px 6px; color: #F3EEE4; font-size: 12px; }
        ol { font-size: 13px; line-height: 2; color: #8E887D; padding-left: 18px; }
        .error { margin-top: 18px; padding: 12px 14px; border: 1px solid rgba(248,113,113,.25); background: rgba(239,68,68,.08); border-radius: 8px; font-size: 12px; color: #fca5a5; word-break: break-word; }
    </style>
</head>
<body>
    <div class="card">
        <p class="label">AWAY FOTOCOPY</p>
        <h1>Database belum siap</h1>
        <p>Aplikasi tidak dapat terhubung ke database MySQL. Ikuti langkah berikut:</p>
        <ol>
            <li>Jalankan <strong>MySQL</strong> dari XAMPP Control Panel.</li>
            <li>Buka <code>http://localhost/phpmyadmin</code>.</li>
            <li>Import file <code>database/schema.sql</code>, lalu <code>database/seed.sql</code>.</li>
            <li>Muat ulang halaman ini.</li>
        </ol>
        <div class="error"><?= e($errorMessage) ?></div>
    </div>
</body>
</html>
