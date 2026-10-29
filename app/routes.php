<?php
declare(strict_types=1);

/* ------------------------------------------------------------------
 |  DAFTAR ROUTE
 * ------------------------------------------------------------------ */

$router->get('/login',   [AuthController::class, 'showLogin']);
$router->post('/login',  [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout']);

$router->get('/',          [DashboardController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);

/* Kasir / POS */
$router->get('/kasir',           [PosController::class, 'index']);
$router->post('/kasir/checkout', [PosController::class, 'checkout']);
$router->get('/struk',           [PosController::class, 'receipt']);

/* Produk */
$router->get('/produk',              [ProductController::class, 'index']);
$router->post('/produk/simpan',      [ProductController::class, 'save']);
$router->post('/produk/hapus',       [ProductController::class, 'delete']);
$router->post('/produk/stok',        [ProductController::class, 'adjustStock']);
$router->get('/produk/riwayat-stok', [ProductController::class, 'stockHistory']);

/* Jasa cetak & finishing */
$router->get('/jasa',                  [ServiceController::class, 'index']);
$router->post('/jasa/simpan',          [ServiceController::class, 'saveService']);
$router->post('/jasa/hapus',           [ServiceController::class, 'deleteService']);
$router->post('/finishing/simpan',     [ServiceController::class, 'saveFinishing']);
$router->post('/finishing/hapus',      [ServiceController::class, 'deleteFinishing']);

/* Transaksi */
$router->get('/transaksi',            [SaleController::class, 'index']);
$router->get('/transaksi/{id}',       [SaleController::class, 'show']);
$router->post('/transaksi/void',      [SaleController::class, 'void']);

/* Keuangan */
$router->get('/keuangan',               [FinanceController::class, 'index']);
$router->post('/keuangan/pengeluaran',  [FinanceController::class, 'saveExpense']);
$router->post('/keuangan/pengeluaran/hapus', [FinanceController::class, 'deleteExpense']);

/* Shift kasir */
$router->get('/shift',        [ShiftController::class, 'index']);
$router->post('/shift/buka',  [ShiftController::class, 'open']);
$router->post('/shift/tutup', [ShiftController::class, 'close']);

/* Laporan */
$router->get('/laporan',        [ReportController::class, 'index']);
$router->get('/laporan/export', [ReportController::class, 'export']);

/* Pengaturan */
$router->get('/pengaturan',           [SettingController::class, 'index']);
$router->post('/pengaturan/simpan',   [SettingController::class, 'save']);
$router->post('/pengaturan/profil',   [SettingController::class, 'saveProfile']);
$router->post('/pengaturan/password', [SettingController::class, 'savePassword']);
