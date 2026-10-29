<div class="no-print mx-auto mb-6 flex max-w-2xl flex-wrap items-center justify-between gap-3">
    <a href="<?= url('transaksi') ?>" class="btn-ghost !py-2 text-xs">
        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M19 12H5M11 18l-6-6 6-6"/></svg>
        Riwayat
    </a>
    <div class="flex items-center gap-2">
        <a href="<?= url('kasir') ?>" class="btn-ghost !py-2 text-xs">Transaksi Baru</a>
        <button type="button" onclick="window.print()" class="btn-primary !py-2 text-xs">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"><path d="M6 9V3h12v6M6 18H4a1 1 0 0 1-1-1v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a1 1 0 0 1-1 1h-2M6 14h12v7H6z"/></svg>
            Cetak Struk
        </button>
    </div>
</div>

<div class="receipt mx-auto shadow-2xl">
    <div class="center">
        <div style="font-size:1.35em;font-weight:600;letter-spacing:.12em;"><?= e($store['store_name'] ?? APP_NAME) ?></div>
        <div style="font-size:.9em;"><?= e($store['store_tagline'] ?? '') ?></div>
        <div class="muted" style="margin-top:4px;"><?= nl2br(e($store['store_address'] ?? '')) ?></div>
        <div class="muted"><?= e($store['store_phone'] ?? '') ?></div>
    </div>
    <hr>
    <div class="row"><span class="muted">No. Invoice</span><span><?= e($sale['invoice_no']) ?></span></div>
    <div class="row"><span class="muted">Tanggal</span><span><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></span></div>
    <div class="row"><span class="muted">Kasir</span><span><?= e($sale['user_name'] ?? 'Administrator') ?></span></div>
    <div class="row"><span class="muted">Pelanggan</span><span><?= e($sale['customer_name'] ?: 'Umum') ?></span></div>
    <hr>
    <?php foreach ($items as $item): ?>
        <?php $meta = json_decode((string) $item['meta_json'], true) ?: []; ?>
        <div style="margin-bottom:5px;">
            <div><?= e($item['name']) ?></div>
            <?php if (!empty($meta['pages'])): ?>
                <div class="muted" style="font-size:.9em;"><?= (int) $meta['pages'] ?> hlm × <?= (int) ($meta['copies'] ?? 1) ?> copy · <?= e($meta['paper_size'] ?? '') ?></div>
            <?php endif; ?>
            <div class="row">
                <span class="muted"><?= qty($item['qty']) ?> × <?= money($item['unit_price']) ?></span>
                <span><?= money($item['subtotal']) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
    <hr>
    <div class="row"><span class="muted">Subtotal</span><span><?= money($sale['subtotal']) ?></span></div>
    <?php if ((float) $sale['discount_amount'] > 0): ?>
        <div class="row"><span class="muted">Diskon</span><span>-<?= money($sale['discount_amount']) ?></span></div>
    <?php endif; ?>
    <?php if ((float) $sale['tax_amount'] > 0): ?>
        <div class="row"><span class="muted">Pajak</span><span><?= money($sale['tax_amount']) ?></span></div>
    <?php endif; ?>
    <div class="row big" style="margin-top:4px;"><span>TOTAL</span><span><?= money($sale['total']) ?></span></div>
    <div class="row"><span class="muted">Tunai</span><span><?= money($sale['paid']) ?></span></div>
    <div class="row"><span class="muted">Kembali</span><span><?= money($sale['change_due']) ?></span></div>
    <hr>
    <?php if ($sale['status'] === 'void'): ?>
        <div class="center" style="font-weight:600;">*** TRANSAKSI DIBATALKAN ***</div>
        <div class="center muted"><?= e($sale['void_reason'] ?? '') ?></div>
        <div class="center muted"><?= $sale['voided_at'] ? date('d/m/Y H:i', strtotime($sale['voided_at'])) : '' ?></div>
        <hr>
    <?php endif; ?>
    <div class="center muted" style="white-space:pre-line;font-size:.92em;"><?= e($store['receipt_footer'] ?? 'Terima kasih') ?></div>
    <div class="center muted" style="margin-top:8px;font-size:.85em;"><?= date('d/m/Y H:i:s', strtotime($sale['created_at'])) ?></div>
</div>
