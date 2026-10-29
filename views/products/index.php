<?php
$lowCount = $lowCount ?? 0;
?>
<section class="animate-fade-up">
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div>
            <p class="eyebrow">Inventaris</p>
            <h2 class="mt-3 display text-4xl sm:text-5xl">Produk &amp; <span class="italic text-gold/90">Stok</span></h2>
            <p class="mt-3 max-w-lg text-sm leading-relaxed text-muted">
                Kelola daftar barang, harga, dan ketersediaan stok. Perubahan stok tercatat pada riwayat.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="<?= url('produk/riwayat-stok') ?>" class="btn-ghost !py-2.5 text-xs">Riwayat Stok</a>
            <button type="button" class="btn-primary !py-2.5 text-xs" data-modal-open="product-modal" data-modal-reset="product-form" data-title="Produk Baru">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Produk Baru
            </button>
        </div>
    </div>

    <div class="mt-7 grid gap-3 sm:grid-cols-3">
        <div class="card px-5 py-4">
            <p class="eyebrow">Total Produk</p>
            <p class="mt-2 font-display text-3xl tnum"><?= count($products) ?></p>
        </div>
        <div class="card px-5 py-4">
            <p class="eyebrow">Stok Menipis</p>
            <p class="mt-2 font-display text-3xl tnum <?= $lowCount > 0 ? 'text-gold' : '' ?>"><?= $lowCount ?></p>
        </div>
        <div class="card px-5 py-4">
            <p class="eyebrow">Nilai Stok (HPP)</p>
            <p class="mt-2 font-display text-3xl tnum"><?= money($stockValue) ?></p>
        </div>
    </div>

    <form method="get" action="<?= url('produk') ?>" class="card mt-4 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[180px] flex-1">
            <label class="label" for="q">Cari</label>
            <input id="q" name="q" value="<?= e($q) ?>" class="input" placeholder="Nama, SKU, atau barcode">
        </div>
        <div class="w-full sm:w-44">
            <label class="label" for="kategori">Kategori</label>
            <select id="kategori" name="kategori" class="input">
                <option value="0">Semua kategori</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= (int) $category['id'] ?>" <?= (int) $categoryId === (int) $category['id'] ? 'selected' : '' ?>>
                        <?= e($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="w-full sm:w-40">
            <label class="label" for="status">Status</label>
            <select id="status" name="status" class="input">
                <option value="">Semua</option>
                <option value="low" <?= $status === 'low' ? 'selected' : '' ?>>Stok menipis</option>
                <option value="inactive" <?= $status === 'inactive' ? 'selected' : '' ?>>Nonaktif</option>
            </select>
        </div>
        <button type="submit" class="btn-ghost !py-2.5 text-xs">Terapkan</button>
        <a href="<?= url('produk') ?>" class="text-[11px] text-muted underline-offset-4 hover:text-gold hover:underline">Reset</a>
    </form>

    <div class="card mt-4 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-lux min-w-[860px]">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th class="text-right">Harga Beli</th>
                        <th class="text-right">Harga Jual</th>
                        <th class="text-center">Stok</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($products === []): ?>
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <p class="font-display text-2xl text-ivory/70">Belum ada produk</p>
                                <p class="mt-2 text-xs text-muted">Tambahkan produk pertama Anda untuk mulai berjualan.</p>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($products as $product): ?>
                        <?php
                        $stock = (int) $product['stock'];
                        $min = (int) $product['min_stock'];
                        if ($stock <= 0) {
                            $stockBadge = 'badge-red';
                            $stockText = 'Habis';
                        } elseif ($stock <= $min) {
                            $stockBadge = 'badge-gold';
                            $stockText = 'Menipis';
                        } else {
                            $stockBadge = 'badge-green';
                            $stockText = 'Aman';
                        }
                        $payload = [
                            'id'          => (int) $product['id'],
                            'name'        => $product['name'],
                            'sku'         => $product['sku'],
                            'barcode'     => $product['barcode'],
                            'category_id' => (int) $product['category_id'],
                            'unit'        => $product['unit'],
                            'cost_price'  => (float) $product['cost_price'],
                            'sell_price'  => (float) $product['sell_price'],
                            'stock'       => $stock,
                            'min_stock'   => $min,
                            'is_active'   => (int) $product['is_active'],
                        ];
                        $stockPayload = [
                            'id'    => (int) $product['id'],
                            'name'  => $product['name'],
                            'stock' => $stock,
                        ];
                        ?>
                        <tr>
                            <td>
                                <p class="text-[13px] font-medium text-ivory/90"><?= e($product['name']) ?></p>
                                <p class="mt-0.5 text-[10px] uppercase tracking-wider text-muted">
                                    <?= e($product['sku'] ?: 'Tanpa SKU') ?><?= $product['barcode'] ? ' · ' . e($product['barcode']) : '' ?>
                                </p>
                            </td>
                            <td class="text-xs text-muted"><?= e($product['category_name'] ?? '—') ?></td>
                            <td class="text-right text-xs tabular-nums text-muted"><?= money($product['cost_price']) ?></td>
                            <td class="text-right text-xs tabular-nums text-ivory"><?= money($product['sell_price']) ?></td>
                            <td class="text-center">
                                <div class="flex flex-col items-center gap-1">
                                    <span class="text-[13px] tabular-nums"><?= $stock ?> <span class="text-[10px] text-muted"><?= e($product['unit']) ?></span></span>
                                    <span class="badge <?= $stockBadge ?>"><?= $stockText ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <?php if (!(int) $product['is_active']): ?>
                                        <span class="badge badge-ivory">Nonaktif</span>
                                    <?php endif; ?>
                                    <button type="button" class="btn-icon" title="Edit produk"
                                            data-modal-open="product-modal"
                                            data-form-fill="product-form"
                                            data-title="Edit Produk"
                                            data-payload="<?= e(json_encode($payload, JSON_UNESCAPED_UNICODE)) ?>">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                    </button>
                                    <button type="button" class="btn-icon" title="Sesuaikan stok"
                                            data-modal-open="stock-modal"
                                            data-form-fill="stock-form"
                                            data-payload="<?= e(json_encode($stockPayload, JSON_UNESCAPED_UNICODE)) ?>">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M12 3v18M3 12h18"/></svg>
                                    </button>
                                    <form method="post" action="<?= url('produk/hapus') ?>" data-confirm="Nonaktifkan produk ini? Riwayat penjualan tetap tersimpan.">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="id" value="<?= (int) $product['id'] ?>">
                                        <button type="submit" class="btn-icon hover:!border-red-400/40 hover:!text-red-300" title="Nonaktifkan">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- ================= MODAL PRODUK ================= -->
<div id="product-modal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-panel">
        <div class="flex items-start justify-between">
            <div>
                <p class="eyebrow">Inventaris</p>
                <h3 class="mt-2 font-display text-3xl" data-modal-title>Produk Baru</h3>
            </div>
            <button type="button" class="btn-icon" data-modal-close>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="product-form" method="post" action="<?= url('produk/simpan') ?>" class="mt-6 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="0">

            <div>
                <label class="label" for="p-name">Nama produk</label>
                <input id="p-name" name="name" class="input" required placeholder="Contoh: Pulpen Standard AE7">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label" for="p-sku">SKU</label>
                    <input id="p-sku" name="sku" class="input" placeholder="Otomatis bila kosong">
                </div>
                <div>
                    <label class="label" for="p-barcode">Barcode</label>
                    <input id="p-barcode" name="barcode" class="input" placeholder="Opsional">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label" for="p-category">Kategori</label>
                    <select id="p-category" name="category_id" class="input">
                        <option value="0">Tanpa kategori</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label" for="p-unit">Satuan</label>
                    <input id="p-unit" name="unit" class="input" value="pcs" placeholder="pcs / rim / pack">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label" for="p-cost">Harga beli</label>
                    <input id="p-cost" name="cost_price" type="number" min="0" step="1" class="input tnum" value="0">
                </div>
                <div>
                    <label class="label" for="p-sell">Harga jual</label>
                    <input id="p-sell" name="sell_price" type="number" min="0" step="1" class="input tnum" value="0" required>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label" for="p-stock">Stok awal</label>
                    <input id="p-stock" name="stock" type="number" min="0" step="1" class="input tnum" value="0">
                    <p class="mt-1 text-[10px] leading-relaxed text-muted/70">Saat mengedit, gunakan tombol <span class="text-gold">+</span> untuk penyesuaian stok.</p>
                </div>
                <div>
                    <label class="label" for="p-min">Stok minimum</label>
                    <input id="p-min" name="min_stock" type="number" min="0" step="1" class="input tnum" value="5">
                </div>
            </div>

            <label class="flex cursor-pointer items-center gap-2.5 pt-1 text-xs text-muted">
                <input type="checkbox" name="is_active" value="1" checked class="h-3.5 w-3.5 rounded border-line bg-ink accent-[#C6A664]">
                Produk aktif dan dapat dijual
            </label>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-ghost" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary">Simpan Produk</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL STOK ================= -->
<div id="stock-modal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-panel !max-w-md">
        <div class="flex items-start justify-between">
            <div>
                <p class="eyebrow">Penyesuaian</p>
                <h3 class="mt-2 font-display text-3xl">Ubah Stok</h3>
            </div>
            <button type="button" class="btn-icon" data-modal-close>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <div class="mt-4 rounded-md border border-line bg-ink/50 px-4 py-3">
            <p class="text-[10px] uppercase tracking-[0.2em] text-muted">Produk</p>
            <p id="stock-product-name" class="mt-1 text-sm text-ivory">—</p>
        </div>

        <form id="stock-form" method="post" action="<?= url('produk/stok') ?>" class="mt-4 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="0">

            <div>
                <label class="label" for="s-type">Jenis penyesuaian</label>
                <select id="s-type" name="type" class="input">
                    <option value="in">Tambah stok (barang masuk)</option>
                    <option value="out">Kurangi stok (barang keluar / rusak)</option>
                    <option value="adjust">Set stok akhir (opname)</option>
                </select>
            </div>

            <div>
                <label class="label" for="s-qty">Jumlah</label>
                <input id="s-qty" name="qty" type="number" min="0" step="1" class="input tnum" value="1" required>
            </div>

            <div>
                <label class="label" for="s-note">Catatan</label>
                <input id="s-note" name="note" class="input" placeholder="Contoh: restock dari supplier">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-ghost" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
