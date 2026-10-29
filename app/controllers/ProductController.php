<?php
declare(strict_types=1);

class ProductController
{
    public function index(): void
    {
        $q = (string) input('q', '');
        $categoryId = request_int('kategori');
        $status = (string) input('status', '');

        $products = Product::search($q, $categoryId ?: null, false);
        if ($status === 'low') {
            $products = array_values(array_filter($products, fn ($p) => (int) $p['stock'] <= (int) $p['min_stock']));
        } elseif ($status === 'inactive') {
            $products = array_values(array_filter($products, fn ($p) => (int) $p['is_active'] === 0));
        }

        view('products/index', [
            'title'      => 'Produk & Stok',
            'products'   => $products,
            'categories' => Product::categories(),
            'q'          => $q,
            'categoryId' => $categoryId,
            'status'     => $status,
            'lowCount'   => count(Product::lowStock()),
            'stockValue' => Product::stockValue(),
        ]);
    }

    public function save(): void
    {
        $id = request_int('id');
        $name = (string) input('name', '');
        $sku = (string) input('sku', '');

        if ($name === '') {
            flash('error', 'Nama produk wajib diisi.');
            redirect('produk');
        }

        if ($sku !== '') {
            $stmt = Database::pdo()->prepare('SELECT id FROM products WHERE sku = ? AND id <> ? LIMIT 1');
            $stmt->execute([$sku, $id]);
            if ($stmt->fetch()) {
                flash('error', 'SKU "' . $sku . '" sudah digunakan produk lain.');
                redirect('produk');
            }
        }

        $existingStock = 0;
        if ($id > 0) {
            $existing = Product::find($id);
            if (!$existing) {
                flash('error', 'Produk tidak ditemukan.');
                redirect('produk');
            }
            $existingStock = (int) $existing['stock'];
        }

        try {
            Product::save([
                'id'          => $id,
                'category_id' => request_int('category_id'),
                'sku'         => $sku,
                'barcode'     => (string) input('barcode', ''),
                'name'        => $name,
                'unit'        => (string) input('unit', 'pcs'),
                'cost_price'  => request_float('cost_price'),
                'sell_price'  => request_float('sell_price'),
                'stock'       => $id > 0 ? $existingStock : request_int('stock'),
                'min_stock'   => request_int('min_stock', 5),
                'is_active'   => isset($_POST['is_active']) ? 1 : 0,
            ]);
            flash('success', $id > 0 ? 'Produk berhasil diperbarui.' : 'Produk baru berhasil ditambahkan.');
        } catch (Throwable $e) {
            flash('error', APP_DEBUG ? $e->getMessage() : 'Gagal menyimpan produk.');
        }

        redirect('produk');
    }

    public function delete(): void
    {
        $id = request_int('id');
        if ($id > 0) {
            Product::delete($id);
            flash('success', 'Produk dinonaktifkan. Riwayat penjualan tetap tersimpan.');
        }
        redirect('produk');
    }

    public function adjustStock(): void
    {
        $id = request_int('id');
        $type = (string) input('type', 'in');
        $qty = request_int('qty');
        $note = (string) input('note', '');

        if (!in_array($type, ['in', 'out', 'adjust'], true)) {
            flash('error', 'Jenis penyesuaian tidak valid.');
            redirect('produk');
        }

        if ($type !== 'adjust' && $qty <= 0) {
            flash('error', 'Jumlah stok harus lebih dari nol.');
            redirect('produk');
        }

        try {
            Product::adjustStock($id, $qty, $type, $note, Auth::id());
            flash('success', 'Stok berhasil disesuaikan.');
        } catch (RuntimeException $e) {
            flash('error', $e->getMessage());
        }

        redirect('produk');
    }

    public function stockHistory(): void
    {
        view('products/stock', [
            'title'     => 'Riwayat Stok',
            'movements' => Product::movements(100),
        ]);
    }
}
