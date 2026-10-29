<?php
$monthLabel = date_id($from . '-01');
$categories = ['Operasional', 'Bahan Baku', 'Gaji', 'Sewa', 'Listrik & Air', 'Peralatan', 'Lainnya'];
?>
<section class="animate-fade-up">
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div>
            <p class="eyebrow">Keuangan</p>
            <h2 class="mt-3 display text-4xl sm:text-5xl">Arus <span class="italic text-gold/90">Kas</span></h2>
            <p class="mt-3 max-w-lg text-sm leading-relaxed text-muted">
                Pantau omzet, HPP, dan pengeluaran operasional dalam satu tampilan.
            </p>
        </div>
        <div class="flex flex-wrap items-end gap-2">
            <form method="get" action="<?= url('keuangan') ?>" class="flex items-end gap-2">
                <div>
                    <label class="label" for="bulan">Periode</label>
                    <input id="bulan" type="month" name="bulan" value="<?= e($month) ?>" class="input !py-2.5 text-xs">
                </div>
                <button type="submit" class="btn-ghost !py-2.5 text-xs">Lihat</button>
            </form>
            <button type="button" class="btn-primary !py-2.5 text-xs" data-modal-open="expense-modal" data-modal-reset="expense-form">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Pengeluaran
            </button>
        </div>
    </div>

    <p class="mt-4 text-[10px] uppercase tracking-[0.24em] text-muted/60">Periode <?= e($monthLabel) ?></p>

    <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-4">
        <div class="card px-5 py-4">
            <p class="eyebrow">Omzet</p>
            <p class="mt-2 font-display text-[1.9rem] leading-none tnum"><?= money($stats['revenue']) ?></p>
            <p class="mt-2 text-[10px] text-muted"><?= $stats['trx'] ?> transaksi lunas</p>
        </div>
        <div class="card px-5 py-4">
            <p class="eyebrow">HPP</p>
            <p class="mt-2 font-display text-[1.9rem] leading-none tnum text-muted"><?= money($stats['hpp']) ?></p>
            <p class="mt-2 text-[10px] text-muted">Harga pokok penjualan</p>
        </div>
        <div class="card px-5 py-4">
            <p class="eyebrow">Laba Kotor</p>
            <p class="mt-2 font-display text-[1.9rem] leading-none tnum text-emerald-300/90"><?= money($stats['gross']) ?></p>
            <p class="mt-2 text-[10px] text-muted">Omzet − HPP</p>
        </div>
        <div class="card px-5 py-4">
            <p class="eyebrow">Pengeluaran</p>
            <p class="mt-2 font-display text-[1.9rem] leading-none tnum text-red-300/90"><?= money($expenseTotal) ?></p>
            <p class="mt-2 text-[10px] text-muted"><?= count($expenses) ?> catatan</p>
        </div>
    </div>

    <div class="mt-4 grid gap-4 xl:grid-cols-[1.7fr_1fr]">
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <div>
                    <p class="eyebrow">Catatan</p>
                    <h3 class="mt-1.5 font-display text-xl">Pengeluaran</h3>
                </div>
                <span class="badge badge-red"><?= money($expenseTotal) ?></span>
            </div>

            <div class="overflow-x-auto">
                <table class="table-lux min-w-[560px]">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Keterangan</th>
                            <th class="text-right">Nominal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($expenses === []): ?>
                            <tr>
                                <td colspan="5" class="py-14 text-center">
                                    <p class="font-display text-xl text-ivory/70">Belum ada pengeluaran</p>
                                    <p class="mt-1.5 text-xs text-muted">Catat pembelian bahan atau biaya operasional di sini.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                        <?php foreach ($expenses as $expense): ?>
                            <tr>
                                <td class="whitespace-nowrap text-[11px] text-muted"><?= date_id($expense['expense_date']) ?></td>
                                <td><span class="badge badge-ivory"><?= e($expense['category']) ?></span></td>
                                <td>
                                    <p class="text-[13px] text-ivory/85"><?= e($expense['description']) ?></p>
                                    <p class="mt-0.5 text-[10px] text-muted/70">oleh <?= e($expense['user_name'] ?? 'Admin') ?></p>
                                </td>
                                <td class="text-right text-[13px] tabular-nums text-red-300/90"><?= money($expense['amount']) ?></td>
                                <td>
                                    <div class="flex justify-end">
                                        <form method="post" action="<?= url('keuangan/pengeluaran/hapus') ?>" data-confirm="Hapus catatan pengeluaran ini?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $expense['id'] ?>">
                                            <input type="hidden" name="bulan" value="<?= e($month) ?>">
                                            <button type="submit" class="btn-icon hover:!border-red-400/40 hover:!text-red-300" title="Hapus">
                                                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6"/></svg>
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

        <div class="space-y-4">
            <div class="card p-5">
                <p class="eyebrow">Rekap</p>
                <h3 class="mt-1.5 font-display text-xl">Laba bersih</h3>
                <p class="mt-4 font-display text-[2.4rem] leading-none tnum <?= $netProfit >= 0 ? 'text-gold' : 'text-red-300' ?>">
                    <?= money($netProfit) ?>
                </p>
                <div class="mt-5 space-y-2.5 border-t border-line pt-4 text-[12px]">
                    <div class="flex justify-between text-muted"><span>Omzet</span><span class="tnum text-ivory/85"><?= money($stats['revenue']) ?></span></div>
                    <div class="flex justify-between text-muted"><span>HPP</span><span class="tnum text-ivory/85">−<?= money($stats['hpp']) ?></span></div>
                    <div class="flex justify-between text-muted"><span>Laba kotor</span><span class="tnum text-ivory/85"><?= money($stats['gross']) ?></span></div>
                    <div class="flex justify-between text-muted"><span>Pengeluaran</span><span class="tnum text-red-300/90">−<?= money($expenseTotal) ?></span></div>
                    <div class="flex justify-between border-t border-line pt-2.5 text-ivory"><span>Laba bersih</span><span class="tnum"><?= money($netProfit) ?></span></div>
                </div>
            </div>

            <div class="card p-5">
                <div class="flex items-center justify-between">
                    <p class="eyebrow">Kas Hari Ini</p>
                    <a href="<?= url('shift') ?>" class="text-[10px] uppercase tracking-[0.16em] text-muted hover:text-gold">Shift →</a>
                </div>
                <?php if ($shift && $shiftSummary): ?>
                    <div class="mt-4 space-y-2.5 text-[12px]">
                        <div class="flex justify-between text-muted"><span>Kas awal</span><span class="tnum text-ivory/85"><?= money($shift['opening_cash']) ?></span></div>
                        <div class="flex justify-between text-muted"><span>Penjualan shift</span><span class="tnum text-ivory/85"><?= money($shiftSummary['sales_total']) ?></span></div>
                        <div class="flex justify-between text-muted"><span>Pengeluaran</span><span class="tnum text-red-300/90">−<?= money($shiftSummary['expense_total']) ?></span></div>
                        <div class="flex justify-between border-t border-line pt-2.5 text-ivory"><span>Kas seharusnya</span><span class="tnum text-gold"><?= money($shiftSummary['expected_cash']) ?></span></div>
                    </div>
                <?php else: ?>
                    <p class="mt-4 text-xs leading-relaxed text-muted">Shift belum dibuka hari ini.</p>
                    <a href="<?= url('shift') ?>" class="btn-ghost mt-3 w-full !py-2 text-[11px]">Buka Shift</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- ================= MODAL PENGELUARAN ================= -->
<div id="expense-modal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-panel !max-w-md">
        <div class="flex items-start justify-between">
            <div>
                <p class="eyebrow">Keuangan</p>
                <h3 class="mt-2 font-display text-3xl">Catat Pengeluaran</h3>
            </div>
            <button type="button" class="btn-icon" data-modal-close>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="expense-form" method="post" action="<?= url('keuangan/pengeluaran') ?>" class="mt-6 space-y-4">
            <?= csrf_field() ?>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label" for="e-date">Tanggal</label>
                    <input id="e-date" type="date" name="expense_date" value="<?= date('Y-m-d') ?>" class="input" required>
                </div>
                <div>
                    <label class="label" for="e-category">Kategori</label>
                    <select id="e-category" name="category" class="input">
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category) ?>"><?= e($category) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div>
                <label class="label" for="e-description">Keterangan</label>
                <input id="e-description" name="description" class="input" placeholder="Contoh: beli tinta printer" required>
            </div>

            <div>
                <label class="label" for="e-amount">Nominal</label>
                <input id="e-amount" name="amount" type="number" min="1" step="1" class="input tnum" placeholder="0" required>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-ghost" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary">Simpan Pengeluaran</button>
            </div>
        </form>
    </div>
</div>
