-- =====================================================================
--  AWAY FOTOCOPY — Data awal
--  Login: admin / admin123
-- =====================================================================
USE `away_fotocopy`;

INSERT INTO `users` (`name`, `username`, `password_hash`) VALUES
('Administrator', 'admin', '$2y$10$GmrljqbCAE6VfQBUPK7skOJF00C1n/0CVLlx8qHEiwLwD5CwJyU3.');

INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('store_name',        'AWAY FOTOCOPY'),
('store_tagline',     'Fotokopi & Alat Tulis'),
('store_address',     'Jl. Raya Contoh No. 12, Kota Anda'),
('store_phone',       '0812-0000-0000'),
('receipt_footer',    'Terima kasih telah berbelanja.\nBarang yang sudah dibeli tidak dapat ditukar.'),
('receipt_width',     '58'),
('tax_percent',       '0'),
('invoice_prefix',    'AF');

INSERT INTO `categories` (`id`, `name`, `slug`, `sort_order`) VALUES
(1, 'Alat Tulis',   'alat-tulis',   1),
(2, 'Kertas',       'kertas',       2),
(3, 'Perlengkapan', 'perlengkapan', 3),
(4, 'Lainnya',      'lainnya',      4);

INSERT INTO `products`
(`category_id`, `sku`, `barcode`, `name`, `unit`, `cost_price`, `sell_price`, `stock`, `min_stock`) VALUES
(1, 'ATK-001', '8990000000011', 'Pulpen Standard AE7 Hitam', 'pcs', 2000, 3500, 48, 10),
(1, 'ATK-002', '8990000000028', 'Pulpen Gel Hitam 0.5', 'pcs', 3500, 5000, 32, 10),
(1, 'ATK-003', '8990000000035', 'Pensil 2B', 'pcs', 2500, 4000, 36, 10),
(1, 'ATK-004', '8990000000042', 'Buku Tulis 38 Lembar', 'pcs', 3000, 4500, 60, 12),
(1, 'ATK-005', '8990000000059', 'Buku Gambar A4', 'pcs', 5000, 7500, 20, 5),
(1, 'ATK-006', '8990000000066', 'Spidol Whiteboard', 'pcs', 7000, 10000, 10, 5),
(1, 'ATK-007', '8990000000073', 'Stabilo Kuning', 'pcs', 6000, 8500, 14, 5),
(2, 'KRT-001', '8990000001018', 'Kertas HVS A4 70gr (rim)', 'rim', 45000, 52000, 12, 4),
(2, 'KRT-002', '8990000001025', 'Kertas HVS F4 70gr (rim)', 'rim', 48000, 55000, 8, 4),
(3, 'PLG-001', '8990000002015', 'Map Plastik Bening', 'pcs', 1500, 2500, 40, 10),
(3, 'PLG-002', '8990000002022', 'Map Kertas Folio', 'pcs', 2000, 3000, 25, 10),
(3, 'PLG-003', '8990000002039', 'Lakban Bening', 'pcs', 8000, 11000, 15, 5),
(3, 'PLG-004', '8990000002046', 'Lem Kertas', 'pcs', 4000, 6000, 18, 5),
(4, 'LN-001',  '8990000003012', 'Amplop Coklat', 'pcs', 500, 1000, 100, 20);

INSERT INTO `print_services` (`name`, `paper_size`, `color_mode`, `price_per_page`) VALUES
('Fotokopi A4 Hitam Putih', 'A4', 'bw',    300),
('Fotokopi F4 Hitam Putih', 'F4', 'bw',    350),
('Cetak A4 Hitam Putih',    'A4', 'bw',    700),
('Cetak A4 Warna',          'A4', 'color', 2500),
('Cetak F4 Hitam Putih',    'F4', 'bw',    800),
('Cetak F4 Warna',          'F4', 'color', 3000),
('Cetak A3 Warna',          'A3', 'color', 6000);

INSERT INTO `finishings` (`name`, `price`) VALUES
('Laminasi A4',   6000),
('Laminasi F4',   7000),
('Jilid Lakban',  5000),
('Jilid Spiral',  15000),
('Staples',       1000),
('Potong Kertas', 1000);

-- Contoh transaksi agar dashboard & laporan tidak kosong
INSERT INTO `shifts` (`id`, `user_id`, `opened_at`, `opening_cash`, `status`) VALUES
(1, 1, CONCAT(CURDATE(), ' 08:00:00'), 100000, 'open');

INSERT INTO `sales`
(`invoice_no`, `shift_id`, `user_id`, `customer_name`, `subtotal`, `discount_amount`, `tax_amount`, `total`, `paid`, `change_due`, `created_at`) VALUES
(CONCAT('AF-', DATE_FORMAT(CURDATE(), '%Y%m%d'), '-0001'), 1, 1, 'Umum', 13500, 0, 0, 13500, 20000, 6500, CONCAT(CURDATE(), ' 09:12:00')),
(CONCAT('AF-', DATE_FORMAT(CURDATE(), '%Y%m%d'), '-0002'), 1, 1, 'Umum', 52000, 0, 0, 52000, 52000, 0, CONCAT(CURDATE(), ' 10:05:00'));

INSERT INTO `sale_items` (`sale_id`, `product_id`, `item_type`, `name`, `qty`, `unit_price`, `cost_price`, `subtotal`) VALUES
(1, 1, 'product', 'Pulpen Standard AE7 Hitam', 1, 3500, 2000, 3500),
(1, 2, 'product', 'Pulpen Gel Hitam 0.5', 2, 5000, 3500, 10000),
(2, 8, 'product', 'Kertas HVS A4 70gr (rim)', 1, 52000, 45000, 52000);

-- Riwayat stok awal (14 produk)
INSERT INTO `stock_movements`
(`product_id`, `type`, `qty`, `stock_before`, `stock_after`, `reference`, `note`, `user_id`, `created_at`) VALUES
(1,  'in', 48,  0, 48,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(2,  'in', 32,  0, 32,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(3,  'in', 36,  0, 36,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(4,  'in', 60,  0, 60,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(5,  'in', 20,  0, 20,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(6,  'in', 10,  0, 10,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(7,  'in', 14,  0, 14,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(8,  'in', 12,  0, 12,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(9,  'in', 8,   0, 8,   'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(10, 'in', 40,  0, 40,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(11, 'in', 25,  0, 25,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(12, 'in', 15,  0, 15,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(13, 'in', 18,  0, 18,  'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00')),
(14, 'in', 100, 0, 100, 'SEED', 'Stok awal', 1, CONCAT(CURDATE(), ' 08:05:00'));

-- Pengeluaran contoh
INSERT INTO `expenses` (`expense_date`, `category`, `description`, `amount`, `user_id`, `created_at`) VALUES
(CURDATE(), 'Operasional', 'Beli tinta printer', 8500, 1, CONCAT(CURDATE(), ' 08:30:00'));
