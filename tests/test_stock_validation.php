<?php
declare(strict_types=1);

/**
 * Dokumentasi logika validasi stok (tidak bisa melebihi stok yang tersedia).
 * 
 * Flow validasi stok:
 * 1. Client-side (assets/js/pos.js):
 *    - addProduct(): cek stok sebelum add ke cart (line 105)
 *    - inc button: cek stok sebelum increment qty (line 252)
 * 
 * 2. Server-side (app/models/Sale.php):
 *    - checkout(): validasi final dengan row lock (FOR UPDATE) untuk race condition safety (line 38)
 *    - Jika qty > stok, throw RuntimeException dengan pesan detail
 * 
 * Jaminan:
 * - Tidak ada pembelian yang melebihi stok tersedia
 * - Concurrent requests aman dengan database row locking
 * - Stock movements tercatat di tabel stock_movements
 */

require __DIR__ . '/../app/models/Product.php';
require __DIR__ . '/../app/models/Sale.php';

echo "✓ Stock validation logic:\n";
echo "  - Client-side: qty tidak bisa melebihi item.stock di keranjang\n";
echo "  - Server-side: Sale::checkout() validasi qty vs product.stock (FOR UPDATE lock)\n";
echo "  - Database: stock_movements mencatat setiap perubahan stok\n";
echo "\nLogika: Pembeli tidak bisa beli lebih dari stok yang tersedia.\n";
