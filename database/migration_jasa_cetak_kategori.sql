-- =====================================================================
--  AWAY FOTOCOPY — Migrasi Kategori Jasa Cetak
--  Tambah kategori "Jasa Cetak" agar layanan cetak bisa diakses
--  sebagai chip kategori di halaman kasir (bukan hanya dropdown).
--  Jalankan: mysql -u root < database/migration_jasa_cetak_kategori.sql
-- =====================================================================
USE `away_fotocopy`;

INSERT IGNORE INTO `categories` (`name`, `slug`, `sort_order`)
VALUES ('Jasa Cetak', 'jasa-cetak', 5);
