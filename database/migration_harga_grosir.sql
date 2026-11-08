-- =====================================================================
--  AWAY FOTOCOPY — Migrasi Harga Grosir
--  Tambah kolom harga grosir & minimum pembelian grosir pada produk.
--  Jalankan: mysql -u root < database/migration_harga_grosir.sql
-- =====================================================================
USE `away_fotocopy`;

ALTER TABLE `products`
    ADD COLUMN `price_wh` DECIMAL(12,2) NULL AFTER `sell_price`,
    ADD COLUMN `min_wh`   INT         NOT NULL DEFAULT 0 AFTER `price_wh`;
