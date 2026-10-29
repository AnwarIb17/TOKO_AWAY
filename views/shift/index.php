<section class="animate-fade-up">
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div>
            <p class="eyebrow">Operasional</p>
            <h2 class="mt-3 display text-4xl sm:text-5xl">Shift <span class="italic text-gold/90">Kasir</span></h2>
            <p class="mt-3 max-w-lg text-sm leading-relaxed text-muted">
                Rekonsiliasi kas harian antara catatan sistem dan uang fisik di laci.
            </p>
        </div>
        <a href="<?= url('kasir') ?>" class="btn-ghost !py-2.5 text-xs">Buka Kasir</a>
    </div>

    <?php if ($shift && $summary): ?>
        <div class="mt-7 grid gap-4 xl:grid-cols-[1.4fr_1fr]">
            <div class="card p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="eyebrow">Sedang berjalan</p>
                        <h3 class="mt-2 font-display text-3xl">Shift terbuka</h3>
                    </div>
                    <span class="badge badge-green">Aktif</span>
                </div>

                <div class="mt-6 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-lg border border-line bg-ink/40 px-4 py-3.5">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-muted">Kas awal</p>
                        <p class="mt-1.5 font-display text-2xl tnum"><?= money($shift['opening_cash']) ?></p>
                    </div>
                    <div class="rounded-lg border border-line bg-ink/40 px-4 py-3.5">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-muted">Penjualan shift</p>
                        <p class="mt-1.5 font-display text-2xl tnum"><?= money($summary['sales_total']) ?></p>
                        <p class="mt-0.5 text-[10px] text-muted"><?= $summary['trx'] ?> transaksi</p>
                    </div>
                    <div class="rounded-lg border border-line bg-ink/40 px-4 py-3.5">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-muted">Pengeluaran</p>
                        <p class="mt-1.5 font-display text-2xl tnum text-red-300/90"><?= money($summary['expense_total']) ?></p>
                    </div>
                    <div class="rounded-lg border border-gold/25 bg-gold/[0.06] px-4 py-3.5">
                        <p class="text-[10px] uppercase tracking-[0.18em] text-gold/80">Kas seharusnya</p>
                        <p class="mt-1.5 font-display text-2xl tnum text-gold"><?= money($summary['expected_cash']) ?></p>
                    </div>
                </div>

                <p class="mt-5 text-[11px] leading-relaxed text-muted">
                    Dibuka <?= date_id($shift['opened_at'], true) ?>. Kas seharusnya = kas awal + penjualan tunai − pengeluaran.
                </p>
            </div>

            <div class="card p-6">
                <p class="eyebrow">Tutup Shift</p>
                <h3 class="mt-2 font-display text-2xl">Hitung uang fisik</h3>
                <p class="mt-2 text-[11px] leading-relaxed text-muted">
                    Masukkan total uang tunai yang ada di laci. Selisih akan dihitung otomatis.
                </p>

                <form method="post" action="<?= url('shift/tutup') ?>" class="mt-5 space-y-4" data-confirm="Tutup shift sekarang?">
                    <?= csrf_field() ?>
                    <div>
                        <label class="label" for="closing_cash">Kas fisik akhir</label>
                        <input id="closing_cash" name="closing_cash" type="number" min="0" step="1" class="input-lg tnum" placeholder="0" required>
                    </div>
                    <div>
                        <label class="label" for="close_note">Catatan</label>
                        <input id="close_note" name="note" class="input" placeholder="Opsional">
                    </div>
                    <button type="submit" class="btn-primary w-full !py-3">Tutup Shift</button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div class="mt-7 grid gap-4 xl:grid-cols-[1fr_1.2fr]">
            <div class="card p-6">
                <p class="eyebrow">Mulai</p>
                <h3 class="mt-2 font-display text-3xl">Buka shift</h3>
                <p class="mt-2 text-[11px] leading-relaxed text-muted">
                    Catat modal awal di laci kas. Transaksi kasir dapat dimulai setelah shift dibuka.
                </p>

                <form method="post" action="<?= url('shift/buka') ?>" class="mt-5 space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="label" for="opening_cash">Kas awal</label>
                        <input id="opening_cash" name="opening_cash" type="number" min="0" step="1" class="input-lg tnum" value="0" required>
                    </div>
                    <div>
                        <label class="label" for="open_note">Catatan</label>
                        <input id="open_note" name="note" class="input" placeholder="Opsional">
                    </div>
                    <button type="submit" class="btn-primary w-full !py-3">Buka Shift</button>
                </form>
            </div>

            <div class="card flex flex-col items-center justify-center p-8 text-center">
                <div class="flex h-14 w-14 items-center justify-center rounded-full border border-line">
                    <svg class="h-6 w-6 text-muted/70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"><path d="M12 8v5l3 2M21 12a9 9 0 1 1-9-9 9 9 0 0 1 9 9z"/></svg>
                </div>
                <p class="mt-4 font-display text-2xl">Belum ada shift aktif</p>
                <p class="mt-2 max-w-xs text-xs leading-relaxed text-muted">Setelah shift dibuka, ringkasan penjualan dan rekonsiliasi kas akan muncul di sini.</p>
            </div>
        </div>
    <?php endif; ?>

    <div class="card mt-4 overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4">
            <div>
                <p class="eyebrow">Riwayat</p>
                <h3 class="mt-1.5 font-display text-xl">Shift sebelumnya</h3>
            </div>
            <span class="badge badge-ivory"><?= count($history) ?> data</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table-lux min-w-[860px]">
                <thead>
                    <tr>
                        <th>Dibuka</th>
                        <th>Ditutup</th>
                        <th class="text-right">Kas Awal</th>
                        <th class="text-right">Penjualan</th>
                        <th class="text-right">Pengeluaran</th>
                        <th class="text-right">Kas Fisik</th>
                        <th class="text-right">Selisih</th>
                        <th class="text-center">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($history === []): ?>
                        <tr><td colspan="8" class="py-14 text-center text-sm text-muted">Belum ada shift yang ditutup.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($history as $row): ?>
                        <?php
                        $difference = $row['difference'] !== null ? (float) $row['difference'] : null;
                        $diffClass = $difference === null ? 'text-muted' : ($difference < 0 ? 'text-red-300' : ($difference > 0 ? 'text-gold' : 'text-emerald-300'));
                        ?>
                        <tr>
                            <td class="whitespace-nowrap text-[11px] text-muted"><?= date_id($row['opened_at'], true) ?></td>
                            <td class="whitespace-nowrap text-[11px] text-muted"><?= $row['closed_at'] ? date_id($row['closed_at'], true) : '—' ?></td>
                            <td class="text-right text-xs tabular-nums text-muted"><?= money($row['opening_cash']) ?></td>
                            <td class="text-right text-xs tabular-nums text-ivory/85"><?= money($row['sales_total']) ?></td>
                            <td class="text-right text-xs tabular-nums text-red-300/80"><?= money($row['expense_total']) ?></td>
                            <td class="text-right text-xs tabular-nums text-muted"><?= $row['closing_cash'] !== null ? money($row['closing_cash']) : '—' ?></td>
                            <td class="text-right text-xs tabular-nums <?= $diffClass ?>"><?= $difference === null ? '—' : ($difference > 0 ? '+' : '') . money($difference) ?></td>
                            <td class="text-center">
                                <span class="badge <?= $row['status'] === 'open' ? 'badge-green' : 'badge-ivory' ?>"><?= $row['status'] === 'open' ? 'Aktif' : 'Selesai' ?></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
