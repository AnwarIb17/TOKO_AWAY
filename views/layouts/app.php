<?php $flashes = flashes(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="theme-color" content="#0A0A0B">
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <title><?= e($title ?? 'Dasbor') ?> — <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <?php require VIEW_PATH . '/partials/sidebar-style.php'; ?>
    <script>
        window.APP = {
            base: <?= json_encode(BASE_PATH) ?>,
            csrf: <?= json_encode(csrf_token()) ?>
        };
    </script>
</head>
<body class="min-h-screen">
<div class="grain"></div>

<div id="sidebar-overlay" class="fixed inset-0 z-40 hidden bg-black/70 backdrop-blur-sm lg:hidden"></div>

<?php require VIEW_PATH . '/partials/sidebar.php'; ?>

<div class="relative z-10 min-h-screen lg:pl-[248px]">
    <?php require VIEW_PATH . '/partials/topbar.php'; ?>

    <main class="px-5 pb-16 pt-6 sm:px-8 lg:px-10">
        <?= $content ?>
    </main>
</div>

<!-- Toast -->
<div class="pointer-events-none fixed right-4 top-4 z-[90] flex w-[min(370px,calc(100vw-2rem))] flex-col gap-2">
    <?php foreach ($flashes as $flash): ?>
        <?php
        $tone = match ($flash['type']) {
            'success' => 'text-emerald-300 border-emerald-400/25',
            'error'   => 'text-red-300 border-red-400/25',
            default   => 'text-gold border-gold/30',
        };
        ?>
        <div class="toast <?= $tone ?>">
            <span class="mt-1 h-1.5 w-1.5 shrink-0 rounded-full bg-current"></span>
            <p class="flex-1 leading-relaxed text-ivory/90"><?= e($flash['message']) ?></p>
            <button type="button" class="text-muted transition-colors hover:text-ivory" onclick="this.closest('.toast').remove()">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>
    <?php endforeach; ?>
</div>

<script src="<?= asset('js/app.js') ?>"></script>
<?= $pageScripts ?? '' ?>
</body>
</html>
