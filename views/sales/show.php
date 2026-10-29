<?php $isVoid = $sale['status'] === 'void'; ?>
<section class="animate-fade-up">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <a href="<?= url('transaksi') ?>" class="inline-flex items-center gap-2 text-[10px] uppercase tracking-[0.18em] text-muted transition-colors hover:text-gold">
                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
                Riwayat transaksi
            </a>
            <h2 class="mt-4 font-mono text-2xl text-ivory sm:text-3xl"><?= e($sale['invoice_no']) ?></h2>
            <div class="mt-3 flex flex-wrap items-center gap-2.5 text-xs text-muted">
                <span class="badge <?= $isVoid ? 'badge-red' : 'badge-green' ?>"><?= $isVoid ? 'Dibatalkan' : 'Lunas' ?></span>
                <span><?= date_id($sale['created_at'], true) ?></span>
                <span class="h-1 w-1 rounded-full bg-line"></span>
                <span>Kasir <?= e($sale['user_name'] ?? 'Admin') ?></span>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= url('struk?invoice=' . urlencode($sale['invoice_no'])) ?>" target="_blank" class="btn-ghost !py-2.5 text-xs">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M6 9V3h12v6M6 18H4a1 1 0 0 1-1-1v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a1 1 0 0 1-1 1h-2M6 14h12v7H6z"/></svg>
                Cetak Struk
            </a>
            <?php if (!$isVoid): ?>
                <button type="button" class="btn-danger !py-2.5" data-modal-open="void-modal">Batalkan Transaksi</button>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($isVoid): ?>
        <div class="mt-6 rounded-lg border border-red-400/25 bg-red-500/[0.07] px-4 py-3.5">
            <p class="text-xs font-medium text-red-300">Transaksi ini telah dibatalkan.</p>
            <p class="mt-1 text-[11px] leading-relaxed text-red-200/70">
                Alasan: <?= e($sale['void_reason'] ?: '—') ?><?= $sale['voided_at'] ? ' · ' . date_id($sale['voided_at'], true) : '' ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="mt-6 grid gap-4 xl:grid-cols-[1.7fr_1fr]">
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <div>
                    <p class="eyebrow">Rincian</p>
                    <h3 class="mt-1.5 font-display text-xl">Item transaksi</h3>
                </div>
                <span class="badge badge-ivory"><?= count($items) ?> baris</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table-lux min-w-[520px]">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <?php $meta = json_decode((string) $item['meta_json'], true) ?: []; ?>
                            <tr>
                                <td>
                                    <p class="text-[13px] text-ivory/90"><?= e($item['name']) ?></p>
                                    <?php if (!empty($meta['pages'])): ?>
                                        <p class="mt-0.5 text-[10px] text-muted">
                                            <?= (int) $meta['pages'] ?> halaman × <?= (int) ($meta['copies'] ?? 1) ?> copy ·
                                            <?= e($meta['paper_size'] ?? '') ?> ·
                                            <?= ($meta['color_mode'] ?? 'bw') === 'color' ? 'Warna' : 'Hitam Putih' ?>
                                        </p>
                                    <?php else: ?>
                                        <p class="mt-0.5 text-[10px] uppercase tracking-wider text-muted"><?= $item['item_type'] === 'print' ? 'Jasa cetak' : 'Produk' ?></p>
                                    <?php endif; ?>
                                </td>
                                <td class="text-right text-xs tabular-nums text-muted"><?= qty($item['qty']) ?></td>
                                <td class="text-right text-xs tabular-nums text-muted"><?= money($item['unit_price']) ?></td>
                                <td class="text-right text-[13px] tabular-nums"><?= money($item['subtotal']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-4">
            <div class="card p-5">
                <p class="eyebrow">Pembayaran</p>
                <div class="mt-4 space-y-2 text-[13px]">
                    <div class="flex justify-between text-muted"><span>Subtotal</span><span class="tnum text-ivory/85"><?= money($sale['subtotal']) ?></span></div>
                    <?php if ((float) $sale['discount_amount'] > 0): ?>
                        <div class="flex justify-between text-muted"><span>Diskon</span><span class="tnum text-gold">−<?= money($sale['discount_amount']) ?></span></div>
                    <?php endif; ?>
                    <?php if ((float) $sale['tax_amount'] > 0): ?>
                        <div class="flex justify-between text-muted"><span>Pajak</span><span class="tnum text-ivory/85"><?= money($sale['tax_amount']) ?></span></div>
                    <?php endif; ?>
                    <div class="flex items-baseline justify-between border-t border-line pt-3">
                        <span class="text-[10px] uppercase tracking-[0.2em] text-muted">Total</span>
                        <span class="font-display text-3xl tnum text-gold"><?= money($sale['total']) ?></span>
                    </div>
                    <div class="flex justify-between text-muted"><span>Tunai</span><span class="tnum text-ivory/85"><?= money($sale['paid']) ?></span></div>
                    <div class="flex justify-between text-muted"><span>Kembalian</span><span class="tnum text-ivory/85"><?= money($sale['change_due']) ?></span></div>
                </div>
            </div>

            <div class="card p-5">
                <p class="eyebrow">Informasi</p>
                <div class="mt-4 space-y-3 text-[12px]">
                    <div class="flex justify-between gap-4"><span class="text-muted">Pelanggan</span><span class="text-right text-ivory/85"><?= e($sale['customer_name'] ?: 'Umum') ?></span></div>
                    <div class="flex justify-between gap-4"><span class="text-muted">Metode</span><span class="text-right text-ivory/85">Tunai</span></div>
                    <div class="flex justify-between gap-4"><span class="text-muted">Shift dibuka</span><span class="text-right text-ivory/85"><?= $sale['shift_opened'] ? date('H:i', strtotime($sale['shift_opened'])) : '—' ?></span></div>
                    <?php if ($sale['note']): ?>
                        <div class="border-t border-line pt-3"><span class="text-muted">Catatan</span><p class="mt-1 leading-relaxed text-ivory/85"><?= e($sale['note']) ?></p></div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if (!$isVoid): ?>
<div id="void-modal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-panel !max-w-md">
        <div class="flex items-start justify-between">
            <div>
                <p class="eyebrow text-red-300">Peringatan</p>
                <h3 class="mt-2 font-display text-3xl">Batalkan transaksi?</h3>
            </div>
            <button type="button" class="btn-icon" data-modal-close>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <p class="mt-4 text-xs leading-relaxed text-muted">
            Transaksi <span class="text-ivory"><?= e($sale['invoice_no']) ?></span> akan ditandai void.
            Stok produk yang terjual akan dikembalikan otomatis. Tindakan ini tidak dapat diurungkan.
        </p>

        <form method="post" action="<?= url('transaksi/void') ?>" class="mt-5 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $sale['id'] ?>">
            <div>
                <label class="label" for="void-reason">Alasan pembatalan</label>
                <input id="void-reason" name="reason" class="input" placeholder="Contoh: salah input qty" required>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" class="btn-ghost" data-modal-close>Batal</button>
                <button type="submit" class="btn-danger !px-4 !py-2.5 text-xs">Ya, batalkan transaksi</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
