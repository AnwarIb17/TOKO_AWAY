<?php
$maxRevenue = max(1, $maxRevenue ?? 1);
$exportUrl = url('laporan/export?' . http_build_query(['from' => $from, 'to' => $to]));
$totalCategory = array_sum(array_map(fn ($row) => (float) $row['revenue'], $categories)) ?: 1;
?>
<section class="animate-fade-up">
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div>
            <p class="eyebrow">Analitik</p>
            <h2 class="mt-3 display text-4xl sm:text-5xl">Laporan <span class="italic text-gold/90">Penjualan</span></h2>
            <p class="mt-3 max-w-lg text-sm leading-relaxed text-muted">
                Ringkasan performa toko berdasarkan rentang tanggal yang dipilih.
            </p>
        </div>
        <a href="<?= $exportUrl ?>" class="btn-ghost !py-2.5 text-xs">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M12 3v12M7 10l5 5 5-5M5 21h14"/></svg>
            Export CSV
        </a>
    </div>

    <form method="get" action="<?= url('laporan') ?>" class="card mt-6 flex flex-wrap items-end gap-3 p-4">
        <div class="w-full sm:w-44">
            <label class="label" for="r-from">Dari tanggal</label>
            <input id="r-from" type="date" name="from" value="<?= e($from) ?>" class="input">
        </div>
        <div class="w-full sm:w-44">
            <label class="label" for="r-to">Sampai tanggal</label>
            <input id="r-to" type="date" name="to" value="<?= e($to) ?>" class="input">
        </div>
        <button type="submit" class="btn-primary !py-2.5 text-xs">Tampilkan</button>
        <div class="flex flex-wrap gap-1.5 sm:ml-auto">
            <a href="<?= url('laporan?from=' . date('Y-m-d') . '&to=' . date('Y-m-d')) ?>" class="chip">Hari ini</a>
            <a href="<?= url('laporan?from=' . date('Y-m-d', strtotime('-6 days')) . '&to=' . date('Y-m-d')) ?>" class="chip">7 hari</a>
            <a href="<?= url('laporan?from=' . date('Y-m-01') . '&to=' . date('Y-m-d')) ?>" class="chip">Bulan ini</a>
        </div>
    </form>

    <p class="mt-4 text-[10px] uppercase tracking-[0.24em] text-muted/60"><?= date_id($from) ?> — <?= date_id($to) ?></p>

    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card px-5 py-4">
            <p class="eyebrow">Omzet</p>
            <p class="mt-2 font-display text-[1.9rem] leading-none tnum"><?= money($stats['revenue']) ?></p>
            <p class="mt-2 text-[10px] text-muted"><?= $stats['trx'] ?> transaksi</p>
        </div>
        <div class="card px-5 py-4">
            <p class="eyebrow">HPP</p>
            <p class="mt-2 font-display text-[1.9rem] leading-none tnum text-muted"><?= money($stats['hpp']) ?></p>
            <p class="mt-2 text-[10px] text-muted">Harga pokok penjualan</p>
        </div>
        <div class="card px-5 py-4">
            <p class="eyebrow">Laba Kotor</p>
            <p class="mt-2 font-display text-[1.9rem] leading-none tnum text-emerald-300/90"><?= money($stats['gross']) ?></p>
            <p class="mt-2 text-[10px] text-muted">Margin <?= percent($stats['gross'], $stats['revenue']) ?>%</p>
        </div>
        <div class="card px-5 py-4">
            <p class="eyebrow">Laba Bersih</p>
            <p class="mt-2 font-display text-[1.9rem] leading-none tnum <?= $netProfit >= 0 ? 'text-gold' : 'text-red-300' ?>"><?= money($netProfit) ?></p>
            <p class="mt-2 text-[10px] text-muted">Setelah pengeluaran <?= money($expenseTotal) ?></p>
        </div>
    </div>

    <div class="card mt-4 p-5 sm:p-6">
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <p class="eyebrow">Tren Harian</p>
                <h3 class="mt-1.5 font-display text-2xl">Omzet per hari</h3>
            </div>
            <p class="text-[10px] uppercase tracking-[0.18em] text-muted">Skala tertinggi <?= money($maxRevenue) ?></p>
        </div>

        <div class="mt-6 overflow-x-auto pb-2">
            <div class="flex h-[200px] min-w-full items-end gap-1.5" style="min-width: <?= max(100, count($series) * 27) ?>px;">
                <?php foreach ($series as $day): ?>
                    <?php $height = $day['revenue'] > 0 ? max(3, ($day['revenue'] / $maxRevenue) * 100) : 2; ?>
                    <div class="group relative flex h-full flex-1 flex-col items-center justify-end gap-1.5" title="<?= e(date_id($day['day'])) ?> · <?= money($day['revenue']) ?>">
                        <span class="pointer-events-none absolute -top-1 z-10 whitespace-nowrap rounded bg-charcoal px-2 py-1 text-[9px] tabular-nums text-ivory opacity-0 shadow-panel transition-opacity group-hover:opacity-100">
                            <?= money($day['revenue'], false) ?>
                        </span>
                        <div class="w-full rounded-t-sm bg-gold/30 transition-colors duration-300 group-hover:bg-gold" style="height: <?= $height ?>%"></div>
                        <span class="text-[8px] tabular-nums text-muted/50"><?= date('j', strtotime($day['day'])) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-2">
        <div class="card overflow-hidden">
            <div class="border-b border-line px-5 py-4">
                <p class="eyebrow">Terlaris</p>
                <h3 class="mt-1.5 font-display text-xl">Produk &amp; layanan</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="table-lux min-w-[420px]">
                    <thead>
                        <tr>
                            <th class="w-8">#</th>
                            <th>Nama</th>
                            <th class="text-right">Qty</th>
                            <th class="text-right">Omzet</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($top === []): ?>
                            <tr><td colspan="4" class="py-12 text-center text-sm text-muted">Belum ada penjualan pada periode ini.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($top as $index => $row): ?>
                            <tr>
                                <td class="text-[10px] tabular-nums text-muted/60"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></td>
                                <td class="text-[12.5px] text-ivory/90"><?= e($row['name']) ?></td>
                                <td class="text-right text-xs tabular-nums text-muted"><?= qty($row['qty']) ?></td>
                                <td class="text-right text-xs tabular-nums text-ivory"><?= money($row['revenue']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card p-5 sm:p-6">
            <p class="eyebrow">Komposisi</p>
            <h3 class="mt-1.5 font-display text-xl">Per kategori</h3>

            <div class="mt-5 space-y-4">
                <?php if ($categories === []): ?>
                    <p class="py-10 text-center text-sm text-muted">Belum ada data kategori.</p>
                <?php endif; ?>
                <?php foreach ($categories as $category): ?>
                    <?php $share = percent((float) $category['revenue'], $totalCategory); ?>
                    <div>
                        <div class="flex items-baseline justify-between gap-3">
                            <span class="text-[12.5px] text-ivory/85"><?= e($category['category_name']) ?></span>
                            <span class="text-[11px] tabular-nums text-muted"><?= money($category['revenue']) ?> · <?= $share ?>%</span>
                        </div>
                        <div class="mt-2 h-1 overflow-hidden rounded-full bg-ivory/[0.06]">
                            <div class="h-full rounded-full bg-gold/60" style="width: <?= max(1, $share) ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-3 border-t border-line pt-4 text-[11px]">
                <div>
                    <p class="text-muted">Rata-rata / transaksi</p>
                    <p class="mt-1 font-display text-xl tnum"><?= money($stats['trx'] > 0 ? $stats['revenue'] / $stats['trx'] : 0) ?></p>
                </div>
                <div>
                    <p class="text-muted">Total diskon</p>
                    <p class="mt-1 font-display text-xl tnum"><?= money($stats['discount']) ?></p>
                </div>
            </div>
        </div>
    </div>
</section>
