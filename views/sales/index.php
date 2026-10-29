<?php
$query = array_filter([
    'q'      => $filters['q'] ?: null,
    'from'   => $filters['from'] ?: null,
    'to'     => $filters['to'] ?: null,
    'status' => $filters['status'] ?: null,
], fn ($value) => $value !== null && $value !== '');

$pageUrl = function (int $page) use ($query): string {
    $params = $query;
    $params['page'] = $page;
    return url('transaksi?' . http_build_query($params));
};
?>
<section class="animate-fade-up">
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div>
            <p class="eyebrow">Riwayat</p>
            <h2 class="mt-3 display text-4xl sm:text-5xl">Transaksi <span class="italic text-gold/90">Kasir</span></h2>
            <p class="mt-3 max-w-lg text-sm leading-relaxed text-muted">
                Semua transaksi tunai yang tercatat, lengkap dengan status dan detail item.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="text-[10px] uppercase tracking-[0.2em] text-muted">Total ditemukan</p>
                <p class="font-display text-3xl tnum"><?= number_format($result['total'], 0, ',', '.') ?></p>
            </div>
            <a href="<?= url('kasir') ?>" class="btn-primary !py-2.5 text-xs">Transaksi Baru</a>
        </div>
    </div>

    <form method="get" action="<?= url('transaksi') ?>" class="card mt-6 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[180px] flex-1">
            <label class="label" for="t-q">Cari</label>
            <input id="t-q" name="q" value="<?= e($filters['q']) ?>" class="input" placeholder="No. invoice atau nama pelanggan">
        </div>
        <div class="w-full sm:w-40">
            <label class="label" for="t-from">Dari</label>
            <input id="t-from" type="date" name="from" value="<?= e($filters['from']) ?>" class="input">
        </div>
        <div class="w-full sm:w-40">
            <label class="label" for="t-to">Sampai</label>
            <input id="t-to" type="date" name="to" value="<?= e($filters['to']) ?>" class="input">
        </div>
        <div class="w-full sm:w-36">
            <label class="label" for="t-status">Status</label>
            <select id="t-status" name="status" class="input">
                <option value="">Semua</option>
                <option value="paid" <?= $filters['status'] === 'paid' ? 'selected' : '' ?>>Lunas</option>
                <option value="void" <?= $filters['status'] === 'void' ? 'selected' : '' ?>>Void</option>
            </select>
        </div>
        <button type="submit" class="btn-ghost !py-2.5 text-xs">Filter</button>
        <a href="<?= url('transaksi') ?>" class="text-[11px] text-muted underline-offset-4 hover:text-gold hover:underline">Reset</a>
    </form>

    <div class="card mt-4 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-lux min-w-[820px]">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Waktu</th>
                        <th>Pelanggan</th>
                        <th class="text-center">Item</th>
                        <th class="text-right">Total</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result['rows'] === []): ?>
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <p class="font-display text-2xl text-ivory/70">Tidak ada transaksi</p>
                                <p class="mt-2 text-xs text-muted">Coba ubah filter tanggal atau mulai transaksi baru di kasir.</p>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($result['rows'] as $sale): ?>
                        <tr>
                            <td>
                                <a href="<?= url('transaksi/' . $sale['id']) ?>" class="font-mono text-xs text-ivory/85 underline-offset-4 hover:text-gold hover:underline">
                                    <?= e($sale['invoice_no']) ?>
                                </a>
                            </td>
                            <td class="whitespace-nowrap text-[11px] text-muted"><?= date_id($sale['created_at'], true) ?></td>
                            <td class="text-xs text-ivory/80"><?= e($sale['customer_name'] ?: 'Umum') ?></td>
                            <td class="text-center text-xs tabular-nums text-muted"><?= (int) $sale['items_count'] ?></td>
                            <td class="text-right text-[13px] tabular-nums"><?= money($sale['total']) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $sale['status'] === 'paid' ? 'badge-green' : 'badge-red' ?>">
                                    <?= $sale['status'] === 'paid' ? 'Lunas' : 'Void' ?>
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="<?= url('transaksi/' . $sale['id']) ?>" class="btn-icon" title="Detail">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <a href="<?= url('struk?invoice=' . urlencode($sale['invoice_no'])) ?>" target="_blank" class="btn-icon" title="Cetak struk">
                                        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M6 9V3h12v6M6 18H4a1 1 0 0 1-1-1v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a1 1 0 0 1-1 1h-2M6 14h12v7H6z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($result['pages'] > 1): ?>
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line px-4 py-3">
                <p class="text-[11px] text-muted">Halaman <?= $result['page'] ?> dari <?= $result['pages'] ?></p>
                <div class="flex items-center gap-1.5">
                    <?php if ($result['page'] > 1): ?>
                        <a href="<?= $pageUrl($result['page'] - 1) ?>" class="btn-icon">←</a>
                    <?php endif; ?>
                    <?php
                    $start = max(1, $result['page'] - 2);
                    $end = min($result['pages'], $result['page'] + 2);
                    for ($i = $start; $i <= $end; $i++):
                    ?>
                        <a href="<?= $pageUrl($i) ?>" class="flex h-8 min-w-8 items-center justify-center rounded-md border px-2 text-[11px] tabular-nums <?= $i === $result['page'] ? 'border-gold/50 bg-gold/10 text-gold' : 'border-line text-muted hover:text-ivory' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <?php if ($result['page'] < $result['pages']): ?>
                        <a href="<?= $pageUrl($result['page'] + 1) ?>" class="btn-icon">→</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>
