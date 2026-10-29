# AWAY FOTOCOPY — Panel Kasir & Keuangan

Aplikasi web internal untuk toko **ATK & Jasa Cetak**: kasir (POS), stok, jasa cetak,
keuangan, shift kasir, dan laporan. Dibangun dengan **PHP 8 native + MySQL** dan
antarmuka **dark luxury** berbasis Tailwind CSS.

![PHP](https://img.shields.io/badge/PHP-8.2-777BB4) ![MySQL](https://img.shields.io/badge/MySQL-MariaDB-4479A1) ![Tailwind](https://img.shields.io/badge/Tailwind-3.4-38BDF8)

---

## ✨ Fitur

| Modul | Kemampuan |
|---|---|
| **Kasir (POS)** | Cari/scan barcode, keranjang, diskon nominal/%, pajak, uang tunai + kembalian, shortcut `F2` `F4` |
| **Jasa Cetak** | Kalkulator halaman × copy × tarif per lembar + finishing, langsung masuk keranjang |
| **Produk & Stok** | CRUD, kategori, SKU/barcode, stok minimum, penyesuaian stok (masuk/keluar/opname), riwayat stok |
| **Transaksi** | Riwayat + filter, detail, cetak ulang struk, void dengan pengembalian stok otomatis |
| **Keuangan** | Omzet, HPP, laba kotor/bersih, pencatatan pengeluaran per kategori |
| **Shift Kasir** | Buka/tutup shift, kas awal, kas seharusnya, selisih kas fisik |
| **Laporan** | Rentang tanggal, tren harian, produk terlaris, komposisi kategori, export CSV |
| **Pengaturan** | Identitas toko, footer struk, lebar struk 58/80 mm, pajak, prefix invoice, akun & password |

Struk dirancang untuk printer thermal **58 mm** atau **80 mm** (bisa diganti di Pengaturan).

---

## 🧱 Kebutuhan

- XAMPP dengan **PHP ≥ 8.1** dan **MySQL/MariaDB**
- Node.js ≥ 18 (hanya untuk build Tailwind; CSS hasil build sudah disertakan)
- Browser modern

---

## 🚀 Instalasi

1. **Letakkan folder** ini di `C:\xampp\htdocs\TOKO`.
2. Jalankan **Apache** dan **MySQL** dari XAMPP Control Panel.
3. Import database melalui phpMyAdmin:
   - Buat/import `database/schema.sql`
   - Lalu import `database/seed.sql`
   
   Atau lewat terminal:
   ```bat
   C:\xampp\mysql\bin\mysql.exe -u root < database\schema.sql
   C:\xampp\mysql\bin\mysql.exe -u root < database\seed.sql
   ```
4. Buka **http://localhost/TOKO**
5. Login:

   | Username | Password |
   |---|---|
   | `admin` | `admin123` |

   > Segera ganti password di **Pengaturan → Ganti Password**.

### Jika password MySQL Anda berbeda

Edit `app/config/config.php`:

```php
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Jika nama folder diubah

`.htaccess` memakai `RewriteBase /TOKO/`. Bila folder diganti, sesuaikan baris tersebut
dan `BASE_PATH` akan mengikuti otomatis.

---

## 🎨 Build CSS (opsional)

CSS sudah tersedia di `assets/css/app.css`. Untuk mengubah tampilan:

```bash
npm install
npm run dev     # watch
npm run build   # minify
```

Token warna & font ada di `tailwind.config.js`; komponen (tombol, kartu, tabel, modal)
ada di `assets/src/app.css`.

---

## 🗂️ Struktur

```
TOKO/
├─ index.php                 # Front controller + guard auth
├─ .htaccess                 # Clean URL
├─ app/
│  ├─ config/config.php      # Kredensial DB, timezone, path
│  ├─ core/                  # Database, Auth, Router
│  ├─ helpers/functions.php  # e(), url(), money(), csrf, view, dll
│  ├─ controllers/           # Auth, Dashboard, Pos, Product, Service, Sale,
│  │                         # Finance, Shift, Report, Setting
│  ├─ models/                # Product, PrintService, Sale, Expense, Shift, Setting
│  └─ routes.php
├─ views/                    # Layout, partial, halaman per modul
├─ assets/
│  ├─ src/app.css            # sumber Tailwind
│  ├─ css/app.css            # hasil build
│  └─ js/app.js, js/pos.js
├─ database/                 # schema.sql, seed.sql
└─ storage/                  # ruang cadangan/upload (tidak diakses publik)
```

---

## 🔐 Keamanan

- Password di-hash dengan `password_hash()` (bcrypt)
- CSRF token pada seluruh form dan request JSON
- Semua query memakai **PDO prepared statement**
- Escaping output `e()` untuk mencegah XSS
- Transaksi database + `SELECT … FOR UPDATE` saat memotong stok
- Folder `database/` dan `storage/` tidak dapat diakses publik

---

## 🧾 Alur Singkat

1. **Shift → Buka Shift** (isi kas awal).
2. **Kasir** → pilih produk / tambah jasa cetak → masukkan uang → bayar.
3. Struk terbuka otomatis; klik **Cetak Struk**.
4. Akhir hari: **Shift → Tutup Shift**, masukkan kas fisik untuk melihat selisih.
5. Pantau **Keuangan** dan **Laporan**.

---

## 🩺 Troubleshooting

| Masalah | Solusi |
|---|---|
| `ConnectionRefused` saat membuka preview | Pastikan Apache berjalan, dan gunakan `http://127.0.0.1/TOKO` atau `http://localhost/TOKO` (bukan port lain). |
| “Database belum siap” | Import `schema.sql` lalu `seed.sql`; cek kredensial di `app/config/config.php`. |
| Halaman 404 di semua menu | `mod_rewrite` belum aktif atau `RewriteBase` tidak sesuai nama folder. |
| CSS tidak berubah | Jalankan `npm run build` setelah mengedit tampilan. |
| Struk terpotong | Samakan lebar struk di Pengaturan dengan setting printer (58/80 mm). |

---

Dibuat untuk operasional harian **AWAY FOTOCOPY**.
