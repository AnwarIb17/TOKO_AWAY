<?php
$typeLabel = ['in' => 'Masuk', 'out' => 'Keluar', 'adjust' => 'Opname'];
$typeBadge = ['in' => 'badge-green', 'out' => 'badge-red', 'adjust' => 'badge-gold'];
?>
<section class="animate-fade-up">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="eyebrow">Inventaris</p>
            <h2 class="mt-3 display text-4xl sm:text-5xl">Riwayat <span class="italic text-gold/90">Stok</span></h2>
            <p class="mt-3 max-w-lg text-sm leading-relaxed text-muted">Jejak setiap perubahan stok, baik dari penjualan maupun penyesuaian manual.</p>
        </div>
        <a href="<?= url('produk') ?>" class="btn-ghost !py-2.5 text-xs">Kembali ke Produk</a>
    </div>

    <div class="card mt-6 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-lux min-w-[860px]">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Produk</th>
                        <th>Jenis</th>
                        <th class="text-right">Perubahan</th>
                        <th class="text-right">Stok</th>
                        <th>Referensi</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($movements === []): ?>
                        <tr><td colspan="7" class="py-16 text-center text-sm text-muted">Belum ada pergerakan stok.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($movements as $movement): ?>
                        <tr>
                            <td class="whitespace-nowrap text-[11px] text-muted"><?= date_id($movement['created_at'], true) ?></td>
                            <td class="text-[13px] text-ivory/90"><?= e($movement['product_name']) ?></td>
                            <td><span class="badge <?= $typeBadge[$movement['type']] ?? 'badge-ivory' ?>"><?= $typeLabel[$movement['type']] ?? e($movement['type']) ?></span></td>
                            <td class="text-right text-xs tabular-nums <?= (int) $movement['qty'] >= 0 ? 'text-emerald-300' : 'text-red-300' ?>">
                                <?= (int) $movement['qty'] > 0 ? '+' : '' ?><?= (int) $movement['qty'] ?>
                            </td>
                            <td class="text-right text-[11px] tabular-nums text-muted"><?= (int) $movement['stock_before'] ?> → <span class="text-ivory"><?= (int) $movement['stock_after'] ?></span></td>
                            <td class="font-mono text-[10px] text-muted"><?= e($movement['reference'] ?: '—') ?></td>
                            <td class="text-[11px] text-muted"><?= e($movement['note'] ?: '—') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
