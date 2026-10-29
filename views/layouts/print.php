<?php
$widthMm = ((int) ($width ?? 58)) === 80 ? 80 : 58;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="<?= asset('img/favicon.svg') ?>" type="image/svg+xml">
    <title><?= e($title ?? 'Struk') ?> — <?= e(APP_NAME) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <style>
        body { background: #0A0A0B; }
        .receipt {
            width: <?= $widthMm ?>mm;
            background: #fff;
            color: #111;
            font-family: 'Inter', ui-monospace, 'Courier New', monospace;
            font-size: <?= $widthMm === 80 ? '12px' : '10.5px' ?>;
            line-height: 1.55;
            padding: <?= $widthMm === 80 ? '8mm 6mm' : '5mm 4mm' ?>;
            margin: 0 auto;
        }
        .receipt hr { border: 0; border-top: 1px dashed #999; margin: 6px 0; }
        .receipt .row { display: flex; justify-content: space-between; gap: 8px; }
        .receipt .muted { color: #666; }
        .receipt .big { font-size: 1.15em; font-weight: 600; }
        .receipt .center { text-align: center; }
        @media print {
            @page { size: <?= $widthMm ?>mm auto; margin: 0; }
            body { background: #fff; }
            .no-print { display: none !important; }
            .receipt { width: 100%; padding: <?= $widthMm === 80 ? '3mm' : '2mm' ?>; box-shadow: none; }
            .print-shell { padding: 0 !important; }
        }
    </style>
</head>
<body class="min-h-screen">
<div class="grain no-print"></div>
<div class="print-shell px-4 py-8">
    <?= $content ?>
</div>
</body>
</html>
