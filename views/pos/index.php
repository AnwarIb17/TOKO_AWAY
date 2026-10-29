<?php $pageScripts = '<script src="' . asset('js/pos.js') . '"></script>'; ?>
<div id="pos-root" data-tax="<?= e((string) $taxPercent) ?>" class="animate-fade-in pb-20 lg:pb-0">
    <?php if (!$shift): ?>
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gold/25 bg-gold/[0.06] px-4 py-3">
            <p class="text-xs leading-relaxed text-gold/90">Shift belum dibuka. Transaksi pertama akan membuka shift otomatis dengan kas awal Rp 0.</p>
            <a href="<?= url('shift') ?>" class="text-[10px] uppercase tracking-[0.18em] text-gold underline-offset-4 hover:underline">Atur shift</a>
        </div>
    <?php endif; ?>

    <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_390px] lg:items-start">
        <!-- ================= PRODUK ================= -->
        <section class="min-w-0">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="relative flex-1">
                    <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                        <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
                    </svg>
                    <input id="pos-search" type="text" class="input !py-3 pl-10 pr-16" placeholder="Cari produk, SKU, atau scan barcode…" autocomplete="off">
                    <kbd class="absolute right-3 top-1/2 -translate-y-1/2 rounded border border-line bg-charcoal px-1.5 py-0.5 text-[9px] uppercase tracking-widest text-muted">F2</kbd>
                </div>
                <button type="button" class="btn-ghost shrink-0 !py-3" data-modal-open="print-modal" data-modal-reset="print-form">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 9V3h12v6M6 18H4a1 1 0 0 1-1-1v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a1 1 0 0 1-1 1h-2M6 14h12v7H6z"/></svg>
                    Jasa Cetak
                </button>
            </div>

            <div class="mt-3 flex gap-2 overflow-x-auto pb-1 no-scrollbar">
                <button type="button" class="chip active" data-category="all">Semua</button>
                <?php foreach ($categories as $category): ?>
                    <button type="button" class="chip" data-category="<?= (int) $category['id'] ?>"><?= e($category['name']) ?></button>
                <?php endforeach; ?>
            </div>

            <div id="product-grid" class="mt-3 grid grid-cols-2 gap-2.5 sm:gap-3 xl:grid-cols-3 2xl:grid-cols-4 lg:max-h-[calc(100vh-15.5rem)] lg:overflow-y-auto lg:pr-1">
                <?php foreach ($products as $product): ?>
                    <?php
                    $payload = [
                        'id'       => (int) $product['id'],
                        'name'     => $product['name'],
                        'price'    => (float) $product['sell_price'],
                        'stock'    => (int) $product['stock'],
                        'unit'     => $product['unit'],
                        'sku'      => $product['sku'],
                        'barcode'  => $product['barcode'],
                    ];
                    $out = (int) $product['stock'] <= 0;
                    ?>
                    <button type="button"
                            class="product-card <?= $out ? 'opacity-40' : '' ?>"
                            data-product="<?= e(json_encode($payload, JSON_UNESCAPED_UNICODE)) ?>"
                            data-category="<?= (int) $product['category_id'] ?>"
                            data-search="<?= e(mb_strtolower($product['name'] . ' ' . $product['sku'] . ' ' . $product['barcode'])) ?>"
                            <?= $out ? 'disabled' : '' ?>>
                        <div class="flex items-start justify-between gap-2">
                            <span class="truncate text-[9px] uppercase tracking-[0.18em] text-muted/70"><?= e($product['category_name'] ?? 'Tanpa kategori') ?></span>
                            <?php if ($out): ?>
                                <span class="badge badge-red">Habis</span>
                            <?php else: ?>
                                <span class="text-[10px] tabular-nums text-muted/60"><?= (int) $product['stock'] ?> <?= e($product['unit']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="mt-2">
                            <h3 class="line-clamp-2 min-h-[2.4em] text-[13px] font-medium leading-snug text-ivory/90"><?= e($product['name']) ?></h3>
                            <p class="pc-price mt-1.5"><?= money($product['sell_price']) ?></p>
                        </div>
                    </button>
                <?php endforeach; ?>

                <?php if ($products === []): ?>
                    <div class="col-span-full rounded-lg border border-dashed border-line px-6 py-16 text-center">
                        <p class="font-display text-2xl">Belum ada produk</p>
                        <p class="mt-2 text-xs text-muted">Tambahkan produk terlebih dahulu di menu Produk.</p>
                    </div>
                <?php endif; ?>
            </div>

            <p class="mt-3 hidden text-[10px] uppercase tracking-[0.2em] text-muted/40 sm:block">
                Klik produk untuk menambah · Scan barcode lalu Enter
            </p>
        </section>

        <!-- ================= KERANJANG ================= -->
        <aside class="lg:sticky lg:top-[78px]">
            <div id="cart-panel" class="card flex scroll-mt-24 flex-col overflow-hidden lg:h-[calc(100vh-7.5rem)]">
                <div class="flex items-center justify-between border-b border-line px-4 py-3.5">
                    <div class="flex items-baseline gap-2">
                        <h3 class="font-display text-xl">Keranjang</h3>
                        <span id="cart-count" class="badge badge-gold">0 item</span>
                    </div>
                    <button type="button" id="cart-clear" class="text-[10px] uppercase tracking-[0.16em] text-muted transition-colors hover:text-red-300">Kosongkan</button>
                </div>

                <div id="cart-items" class="flex-1 overflow-y-auto px-4">
                    <div id="cart-empty" class="flex h-full flex-col items-center justify-center py-12 text-center">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full border border-line">
                            <svg class="h-5 w-5 text-muted/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"><path d="M3 4h2l2.4 11.2a1 1 0 0 0 1 .8h8.8a1 1 0 0 0 1-.8L20 7H6"/><circle cx="9" cy="20" r="1"/><circle cx="17" cy="20" r="1"/></svg>
                        </div>
                        <p class="mt-3 font-display text-lg text-ivory/70">Keranjang kosong</p>
                        <p class="mt-1 max-w-[190px] text-[11px] leading-relaxed text-muted/60">Pilih produk di sebelah kiri atau tambahkan jasa cetak.</p>
                    </div>
                </div>

                <div class="border-t border-line bg-charcoal/50 px-4 py-3.5">
                    <div id="cart-message" class="mb-2 hidden rounded border border-red-400/25 bg-red-500/10 px-3 py-2 text-[11px] leading-relaxed text-red-300"></div>

                    <input id="customer-input" type="text" class="input-sm mb-2.5" placeholder="Nama pelanggan (opsional)" autocomplete="off">

                    <div class="space-y-1.5 text-[12px]">
                        <div class="flex justify-between text-muted"><span>Subtotal</span><span id="sum-subtotal" class="tnum text-ivory/80">Rp 0</span></div>

                        <div class="flex items-center justify-between gap-2 text-muted">
                            <span>Diskon</span>
                            <div class="flex items-center gap-1.5">
                                <input id="discount-input" type="number" min="0" step="1" value="0" class="input-sm w-20 text-right tnum">
                                <select id="discount-type" class="input-sm w-14 !px-2 !text-[10px]">
                                    <option value="nominal">Rp</option>
                                    <option value="percent">%</option>
                                </select>
                            </div>
                        </div>

                        <div id="tax-row" class="flex justify-between text-muted <?= $taxPercent <= 0 ? 'hidden' : '' ?>"><span>Pajak (<?= qty($taxPercent) ?>%)</span><span id="sum-tax" class="tnum text-ivory/80">Rp 0</span></div>

                        <div class="flex items-baseline justify-between border-t border-line pt-2.5">
                            <span class="text-[10px] uppercase tracking-[0.2em] text-muted">Total</span>
                            <span id="sum-total" class="font-display text-[28px] leading-none tnum text-gold">Rp 0</span>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="label" for="paid-input">Uang diterima</label>
                        <input id="paid-input" type="text" inputmode="numeric" class="input !py-2.5 text-lg tabular-nums" placeholder="0" autocomplete="off">
                        <div class="mt-2 grid grid-cols-4 gap-1.5">
                            <button type="button" class="btn-soft !px-1 !py-1.5 text-[10px]" data-cash="exact">Uang pas</button>
                            <button type="button" class="btn-soft !px-1 !py-1.5 text-[10px]" data-cash="20000">20 rb</button>
                            <button type="button" class="btn-soft !px-1 !py-1.5 text-[10px]" data-cash="50000">50 rb</button>
                            <button type="button" class="btn-soft !px-1 !py-1.5 text-[10px]" data-cash="100000">100 rb</button>
                        </div>
                    </div>

                    <div class="mt-2.5 flex items-center justify-between text-[12px]">
                        <span class="text-muted">Kembalian</span>
                        <span id="sum-change" class="tnum text-ivory/80">Rp 0</span>
                    </div>

                    <button type="button" id="pay-button" class="btn-primary mt-3 w-full !py-3" disabled>
                        <span id="pay-label">Bayar</span>
                        <span id="pay-total" class="tnum">Rp 0</span>
                        <kbd class="ml-1 rounded border border-ink/20 px-1 text-[9px] uppercase tracking-wider opacity-60">F4</kbd>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    <!-- Pintasan keranjang (mobile) -->
    <button type="button" id="mobile-cart-bar"
            class="fixed inset-x-3 bottom-3 z-40 hidden items-center justify-between gap-3 rounded-lg border border-gold/30 bg-panel/95 px-4 py-3 text-left shadow-panel backdrop-blur-md lg:hidden">
        <span class="flex items-center gap-2.5">
            <span id="mobile-cart-count" class="badge badge-gold">0 item</span>
            <span id="mobile-cart-total" class="font-display text-lg leading-none text-gold tnum">Rp 0</span>
        </span>
        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold uppercase tracking-[0.16em] text-ivory">
            Lihat keranjang
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
        </span>
    </button>
</div>

<!-- ================= MODAL JASA CETAK ================= -->
<div id="print-modal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-panel wide">
        <div class="flex items-start justify-between">
            <div>
                <p class="eyebrow">Layanan</p>
                <h3 class="mt-2 font-display text-3xl">Jasa Cetak</h3>
            </div>
            <button type="button" class="btn-icon" data-modal-close>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="print-form" class="mt-6 space-y-4" onsubmit="return false;">
            <div>
                <label class="label" for="print-service">Jenis layanan</label>
                <select id="print-service" class="input">
                    <?php foreach ($services as $service): ?>
                        <option value="<?= (int) $service['id'] ?>"
                                data-price="<?= (float) $service['price_per_page'] ?>"
                                data-name="<?= e($service['name']) ?>"
                                data-paper="<?= e($service['paper_size']) ?>"
                                data-mode="<?= e($service['color_mode']) ?>">
                            <?= e($service['name']) ?> — <?= money($service['price_per_page']) ?>/lembar
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label" for="print-pages">Jumlah halaman</label>
                    <input id="print-pages" type="number" min="1" value="1" class="input tnum">
                </div>
                <div>
                    <label class="label" for="print-copies">Jumlah copy</label>
                    <input id="print-copies" type="number" min="1" value="1" class="input tnum">
                </div>
            </div>

            <div>
                <span class="label">Finishing (opsional)</span>
                <div class="grid gap-2 sm:grid-cols-2">
                    <?php foreach ($finishings as $finishing): ?>
                        <label class="flex cursor-pointer items-center justify-between gap-2 rounded-md border border-line px-3 py-2.5 transition-colors duration-300 hover:border-gold/30">
                            <span class="flex items-center gap-2.5">
                                <input type="checkbox" class="finishing-check"
                                       value="<?= (int) $finishing['id'] ?>"
                                       data-price="<?= (float) $finishing['price'] ?>"
                                       data-name="<?= e($finishing['name']) ?>">
                                <span class="text-[12px] text-ivory/85"><?= e($finishing['name']) ?></span>
                            </span>
                            <span class="text-[11px] tabular-nums text-muted"><?= money($finishing['price']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="flex items-center justify-between rounded-md border border-line bg-ink/50 px-4 py-3">
                <span class="text-[10px] uppercase tracking-[0.2em] text-muted">Subtotal jasa</span>
                <span id="print-total" class="font-display text-2xl tnum text-gold">Rp 0</span>
            </div>

            <div class="flex justify-end gap-2 pt-1">
                <button type="button" class="btn-ghost" data-modal-close>Batal</button>
                <button type="button" id="print-add" class="btn-primary">Tambahkan ke Keranjang</button>
            </div>
        </form>
    </div>
</div>
