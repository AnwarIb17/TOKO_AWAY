<?php
declare(strict_types=1);

/**
 * Self-check logika harga grosir (tanpa database).
 * Jalankan: php tests/test_harga_grosir.php
 */

require __DIR__ . '/../app/models/Product.php';

$failures = 0;

function check(string $label, float $actual, float $expected): void
{
    global $failures;
    $ok = abs($actual - $expected) < 0.001;
    if (!$ok) {
        $failures++;
    }
    printf("[%s] %s: %s (expected %s)\n", $ok ? 'PASS' : 'FAIL', $label, $actual, $expected);
}

$product = ['sell_price' => 5000, 'price_wh' => 4000, 'min_wh' => 12];

check('qty 1 pakai harga satuan', Product::unitPrice($product, 1), 5000);
check('qty di bawah minimum pakai harga satuan', Product::unitPrice($product, 11), 5000);
check('qty tepat minimum pakai harga grosir', Product::unitPrice($product, 12), 4000);
check('qty di atas minimum pakai harga grosir', Product::unitPrice($product, 24), 4000);

check(
    'tanpa harga grosir tetap harga satuan',
    Product::unitPrice(['sell_price' => 5000, 'price_wh' => 0, 'min_wh' => 5], 100),
    5000
);
check(
    'harga grosir tanpa minimum dianggap tidak aktif',
    Product::unitPrice(['sell_price' => 5000, 'price_wh' => 3000, 'min_wh' => 0], 100),
    5000
);
check(
    'kolom belum ada (data lama) tetap harga satuan',
    Product::unitPrice(['sell_price' => 5000], 100),
    5000
);

exit($failures === 0 ? 0 : 1);
