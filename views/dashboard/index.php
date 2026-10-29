<?php
$maxRevenue = max(array_column($series, 'revenue') ?: [0]);
$maxRevenue = $maxRevenue > 0 ? $maxRevenue : 1;
$dayNames = ['Min', 'Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab'];
$todayDate = date('Y-m-d');
?>
<section class="animate-fade-up">
    <!-- Hero -->
    <div class="flex flex-wrap items-end justify-between gap-6">
        <div>
            <p class="eyebrow"><?= day_id($todayDate) ?>, <?= date_id($todayDate) ?></p>
            <h2 class="mt-4 display text-[clamp(2.6rem,5vw,4.5rem)]">
                <?= e($greeting) ?>,<br>
                <span class="italic text-gold/90"><?= e(explode(' ', Auth::name())[0]) ?>.</span>
            </h2>
            <p class="mt-4 max-w-md text-sm leading-relaxed text-muted">
                Ringkasan singkat operasional toko hari ini. Semua angka diperbarui langsung dari transaksi kasir.
            </p>
        </div>

        <div class="card w-full max-w-xs p-5">
            <div class="flex items-center justify-between">
                <span class="eyebrow">Status Shift</span>
                <span class="badge <?= $shift ? 'badge-green' : 'badge-red' ?>"><?= $shift ? 'Terbuka' : 'Tertutup' ?></span>
            </div>
            <?php if ($shift): ?>
                <p class="mt-4 font-display text-2xl">Kas berjalan</p>
                <p class="mt-1 text-xs text-muted">Dibuka <?= date('H:i', strtotime($shift['opened_at'])) ?> · kas awal <?= money($shift['opening_cash']) ?></p>
                <a href="<?= url('shift') ?>" class="mt-4 inline-flex items-center gap-2 text-[11px] uppercase tracking-[0.18em] text-gold transition-colors hover:text-ivory">
                    Kelola shift
                    <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            <?php else: ?>
                <p class="mt-4 font-display text-2xl">Belum dibuka</p>
                <p class="mt-1 text-xs leading-relaxed text-muted">Buka shift untuk mulai mencatat kas hari ini.</p>
                <a href="<?= url('shift') ?>" class="btn-primary mt-4 w-full !py-2 text-xs">Buka Shift</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Stats -->
    <div class="mt-10 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card card-hover p-5 animate-fade-up reveal-1">
            <div class="flex items-center justify-between">
                <span class="eyebrow">Omzet Hari Ini</span>
                <span class="h-1.5 w-1.5 rounded-full bg-gold"></span>
            </div>
            <p class="mt-4 font-display text-[2.1rem] leading-none tnum"><?= money($today['revenue']) ?></p>
            <p class="mt-2 text-[11px] text-muted"><?= $today['trx'] ?> transaksi tercatat</p>
        </div>

        <div class="card card-hover p-5 animate-fade-up reveal-2">
            <div class="flex items-center justify-between">
                <span class="eyebrow">Transaksi</span>
                <span class="h-1.5 w-1.5 rounded-full bg-ivory/30"></span>
            </div>
            <p class="mt-4 font-display text-[2.1rem] leading-none tnum"><?= number_format($today['trx'], 0, ',', '.') ?></p>
            <p class="mt-2 text-[11px] text-muted">Hari ini · <?= qty($today['items']) ?> item terjual</p>
        </div>

        <div class="card card-hover p-5 animate-fade-up reveal-3">
            <div class="flex items-center justify-between">
                <span class="eyebrow">Laba Kotor Bulan Ini</span>
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400/60"></span>
            </div>
            <p class="mt-4 font-display text-[2.1rem] leading-none tnum"><?= money($month['gross']) ?></p>
            <p class="mt-2 text-[11px] text-muted">Omzet <?= money($month['revenue']) ?> · HPP <?= money($month['hpp']) ?></p>
        </div>

        <div class="card card-hover p-5 animate-fade-up reveal-4">
            <div class="flex items-center justify-between">
                <span class="eyebrow">Pengeluaran Bulan Ini</span>
                <span class="h-1.5 w-1.5 rounded-full bg-red-400/60"></span>
            </div>
            <p class="mt-4 font-display text-[2.1rem] leading-none tnum"><?= money($monthExpense) ?></p>
            <p class="mt-2 text-[11px] text-muted">Laba bersih <?= money($month['gross'] - $monthExpense) ?></p>
        </div>
    </div>

    <!-- Chart + Low stock -->
    <div class="mt-4 grid gap-4 xl:grid-cols-[1.6fr_1fr]">
        <div class="card animate-fade-up p-5 sm:p-6 reveal-2">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="eyebrow">Arus Omzet</p>
                    <h3 class="mt-2 font-display text-2xl">Tujuh hari terakhir</h3>
                </div>
                <a href="<?= url('laporan') ?>" class="text-[11px] uppercase tracking-[0.18em] text-muted transition-colors hover:text-gold">Laporan lengkap →</a>
            </div>

            <div class="mt-8 flex h-[190px] items-end gap-2 sm:gap-4">
                <?php foreach ($series as $index => $day): ?>
                    <?php
                    $height = $day['revenue'] > 0 ? max(4, ($day['revenue'] / $maxRevenue) * 100) : 2;
                    $isToday = $day['day'] === $todayDate;
                    ?>
                    <div class="group relative flex h-full flex-1 flex-col items-center justify-end gap-2" title="<?= e(date_id($day['day'])) ?> · <?= money($day['revenue']) ?>">
                        <span class="pointer-events-none absolute -top-1 rounded bg-charcoal px-2 py-1 text-[10px] tabular-nums text-ivory opacity-0 shadow-panel transition-opacity duration-300 group-hover:opacity-100">
                            <?= money($day['revenue'], false) ?>
                        </span>
                        <div class="w-full rounded-t-sm transition-all duration-500 ease-lux <?= $isToday ? 'bg-gold' : 'bg-gold/25 group-hover:bg-gold/50' ?>" style="height: <?= $height ?>%"></div>
                        <span class="text-[9px] uppercase tracking-widest <?= $isToday ? 'text-gold' : 'text-muted/60' ?>">
                            <?= $dayNames[(int) date('w', strtotime($day['day']))] ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card animate-fade-up p-5 sm:p-6 reveal-3">
            <div class="flex items-center justify-between">
                <div>
                    <p class="eyebrow">Perlu Perhatian</p>
                    <h3 class="mt-2 font-display text-2xl">Stok menipis</h3>
                </div>
                <a href="<?= url('produk?status=low') ?>" class="btn-icon" title="Lihat semua">
                    <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>

            <div class="mt-5 space-y-3.5">
                <?php if ($lowStock === []): ?>
                    <div class="rounded-md border border-dashed border-line px-4 py-8 text-center">
                        <p class="text-sm text-muted">Semua stok dalam kondisi aman.</p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($lowStock, 0, 5) as $product): ?>
                        <?php $ratio = min(100, ((int) $product['stock'] / max(1, (int) $product['min_stock'])) * 100); ?>
                        <div>
                            <div class="flex items-baseline justify-between gap-3">
                                <p class="truncate text-[13px] text-ivory/90"><?= e($product['name']) ?></p>
                                <span class="shrink-0 text-[11px] tabular-nums <?= (int) $product['stock'] <= 0 ? 'text-red-300' : 'text-gold' ?>">
                                    <?= (int) $product['stock'] ?> / <?= (int) $product['min_stock'] ?>
                                </span>
                            </div>
                            <div class="mt-2 h-1 overflow-hidden rounded-full bg-ivory/[0.06]">
                                <div class="h-full rounded-full <?= (int) $product['stock'] <= 0 ? 'bg-red-400/70' : 'bg-gold/60' ?>" style="width: <?= max(2, $ratio) ?>%"></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="mt-6 flex items-center justify-between border-t border-line pt-4 text-[11px] text-muted">
                <span><?= $totalProducts ?> produk aktif</span>
                <span>Nilai stok <?= money($stockValue) ?></span>
            </div>
        </div>
    </div>

    <!-- Recent -->
    <div class="card mt-4 animate-fade-up overflow-hidden reveal-4">
        <div class="flex items-center justify-between px-5 pt-5 sm:px-6">
            <div>
                <p class="eyebrow">Aktivitas</p>
                <h3 class="mt-2 font-display text-2xl">Transaksi terakhir</h3>
            </div>
            <a href="<?= url('transaksi') ?>" class="text-[11px] uppercase tracking-[0.18em] text-muted transition-colors hover:text-gold">Semua →</a>
        </div>

        <div class="mt-3 hidden overflow-x-auto sm:block">
            <table class="table-lux min-w-[620px]">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Pelanggan</th>
                        <th>Waktu</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($recent === []): ?>
                        <tr><td colspan="5" class="py-10 text-center text-sm text-muted">Belum ada transaksi.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($recent as $sale): ?>
                        <tr class="cursor-pointer" onclick="window.location='<?= url('transaksi/' . $sale['id']) ?>'">
                            <td class="font-mono text-xs text-ivory/80"><?= e($sale['invoice_no']) ?></td>
                            <td class="text-muted"><?= e($sale['customer_name'] ?: 'Umum') ?></td>
                            <td class="text-xs text-muted"><?= date('H:i', strtotime($sale['created_at'])) ?> · <?= date_id($sale['created_at']) ?></td>
                            <td class="text-right tnum"><?= money($sale['total']) ?></td>
                            <td class="text-right">
                                <span class="badge <?= $sale['status'] === 'paid' ? 'badge-green' : 'badge-red' ?>"><?= $sale['status'] === 'paid' ? 'Lunas' : 'Void' ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="mt-3 divide-y divide-line/60 sm:hidden">
            <?php if ($recent === []): ?>
                <p class="px-5 py-8 text-center text-xs text-muted">Belum ada transaksi.</p>
            <?php endif; ?>
            <?php foreach ($recent as $sale): ?>
                <a href="<?= url('transaksi/' . $sale['id']) ?>" class="flex items-center justify-between gap-3 px-5 py-3.5">
                    <div class="min-w-0">
                        <p class="truncate font-mono text-[11px] text-ivory/80"><?= e($sale['invoice_no']) ?></p>
                        <p class="mt-1 text-[10px] text-muted"><?= date('H:i', strtotime($sale['created_at'])) ?> · <?= date_id($sale['created_at']) ?> · <?= e($sale['customer_name'] ?: 'Umum') ?></p>
                    </div>
                    <div class="shrink-0 text-right">
                        <p class="text-[13px] tnum"><?= money($sale['total']) ?></p>
                        <span class="badge mt-1 <?= $sale['status'] === 'paid' ? 'badge-green' : 'badge-red' ?>"><?= $sale['status'] === 'paid' ? 'Lunas' : 'Void' ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <p class="mt-10 text-center text-[9px] uppercase tracking-[0.32em] text-muted/30">AWAY FOTOCOPY · Internal Panel</p>
</section>
