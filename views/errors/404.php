<?php $flashes = flashes(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <title>Halaman tidak ditemukan — <?= e(APP_NAME) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400&family=Inter:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="min-h-screen">
<div class="grain"></div>
<main class="relative z-10 flex min-h-screen items-center justify-center px-6">
    <div class="w-full max-w-lg text-center animate-fade-up">
        <p class="eyebrow">Error 404</p>
        <h1 class="mt-5 display text-6xl sm:text-7xl">Halaman <span class="italic text-gold/90">tidak ada</span></h1>
        <p class="mt-5 text-sm leading-relaxed text-muted">
            Alamat yang Anda tuju tidak tersedia atau sudah dipindahkan.
        </p>
        <div class="mt-8 flex items-center justify-center gap-3">
            <a href="<?= url('dashboard') ?>" class="btn-primary !py-2.5 text-xs">Kembali ke Dasbor</a>
            <a href="<?= url('kasir') ?>" class="btn-ghost !py-2.5 text-xs">Buka Kasir</a>
        </div>
    </div>
</main>
</body>
</html>
