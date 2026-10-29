<?php
$paperLabel = ['A4' => 'A4', 'F4' => 'F4', 'A3' => 'A3', 'A5' => 'A5'];
?>
<section class="animate-fade-up">
    <div class="flex flex-wrap items-end justify-between gap-5">
        <div>
            <p class="eyebrow">Layanan</p>
            <h2 class="mt-3 display text-4xl sm:text-5xl">Jasa <span class="italic text-gold/90">Cetak</span></h2>
            <p class="mt-3 max-w-lg text-sm leading-relaxed text-muted">
                Atur tarif cetak per lembar dan harga finishing. Perubahan langsung berlaku di kasir.
            </p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" class="btn-ghost !py-2.5 text-xs" data-modal-open="finishing-modal" data-modal-reset="finishing-form" data-title="Finishing Baru">Finishing Baru</button>
            <button type="button" class="btn-primary !py-2.5 text-xs" data-modal-open="service-modal" data-modal-reset="service-form" data-title="Layanan Baru">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 5v14M5 12h14"/></svg>
                Layanan Baru
            </button>
        </div>
    </div>

    <div class="mt-7 grid gap-4 xl:grid-cols-[1.5fr_1fr]">
        <!-- Layanan -->
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <div>
                    <p class="eyebrow">Tarif Cetak</p>
                    <h3 class="mt-1.5 font-display text-xl">Layanan</h3>
                </div>
                <span class="badge badge-gold"><?= count($services) ?> item</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table-lux min-w-[540px]">
                    <thead>
                        <tr>
                            <th>Layanan</th>
                            <th>Spesifikasi</th>
                            <th class="text-right">Harga / Lembar</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($services === []): ?>
                            <tr><td colspan="4" class="py-14 text-center text-sm text-muted">Belum ada layanan cetak.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($services as $service): ?>
                            <?php
                            $payload = [
                                'id'             => (int) $service['id'],
                                'name'           => $service['name'],
                                'paper_size'     => $service['paper_size'],
                                'color_mode'     => $service['color_mode'],
                                'price_per_page' => (float) $service['price_per_page'],
                                'is_active'      => (int) $service['is_active'],
                            ];
                            ?>
                            <tr class="<?= !(int) $service['is_active'] ? 'opacity-50' : '' ?>">
                                <td>
                                    <p class="text-[13px] text-ivory/90"><?= e($service['name']) ?></p>
                                    <?php if (!(int) $service['is_active']): ?><span class="badge badge-ivory mt-1.5">Nonaktif</span><?php endif; ?>
                                </td>
                                <td class="text-xs text-muted">
                                    <?= e($service['paper_size']) ?> ·
                                    <?= $service['color_mode'] === 'color' ? 'Warna' : 'Hitam Putih' ?>
                                </td>
                                <td class="text-right text-xs tabular-nums text-ivory"><?= money($service['price_per_page']) ?></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" class="btn-icon" title="Edit layanan"
                                                data-modal-open="service-modal"
                                                data-form-fill="service-form"
                                                data-title="Edit Layanan"
                                                data-payload="<?= e(json_encode($payload, JSON_UNESCAPED_UNICODE)) ?>">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                        </button>
                                        <form method="post" action="<?= url('jasa/hapus') ?>" data-confirm="Nonaktifkan layanan ini?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $service['id'] ?>">
                                            <button type="submit" class="btn-icon hover:!border-red-400/40 hover:!text-red-300" title="Nonaktifkan">
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

        <!-- Finishing -->
        <div class="card overflow-hidden">
            <div class="flex items-center justify-between border-b border-line px-5 py-4">
                <div>
                    <p class="eyebrow">Tambahan</p>
                    <h3 class="mt-1.5 font-display text-xl">Finishing</h3>
                </div>
                <span class="badge badge-gold"><?= count($finishings) ?> item</span>
            </div>
            <div class="overflow-x-auto">
                <table class="table-lux min-w-[360px]">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th class="text-right">Harga</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($finishings === []): ?>
                            <tr><td colspan="3" class="py-14 text-center text-sm text-muted">Belum ada finishing.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($finishings as $finishing): ?>
                            <?php
                            $payload = [
                                'id'        => (int) $finishing['id'],
                                'name'      => $finishing['name'],
                                'price'     => (float) $finishing['price'],
                                'is_active' => (int) $finishing['is_active'],
                            ];
                            ?>
                            <tr class="<?= !(int) $finishing['is_active'] ? 'opacity-50' : '' ?>">
                                <td class="text-[13px] text-ivory/90"><?= e($finishing['name']) ?></td>
                                <td class="text-right text-xs tabular-nums text-ivory"><?= money($finishing['price']) ?></td>
                                <td>
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button type="button" class="btn-icon" title="Edit finishing"
                                                data-modal-open="finishing-modal"
                                                data-form-fill="finishing-form"
                                                data-title="Edit Finishing"
                                                data-payload="<?= e(json_encode($payload, JSON_UNESCAPED_UNICODE)) ?>">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>
                                        </button>
                                        <form method="post" action="<?= url('finishing/hapus') ?>" data-confirm="Nonaktifkan finishing ini?">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="id" value="<?= (int) $finishing['id'] ?>">
                                            <button type="submit" class="btn-icon hover:!border-red-400/40 hover:!text-red-300" title="Nonaktifkan">
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
    </div>

    <p class="mt-6 text-[11px] leading-relaxed text-muted/60">
        Catatan: layanan yang dinonaktifkan tidak akan muncul di kasir, tetapi riwayat transaksi lama tetap tersimpan.
    </p>
</section>

<!-- ================= MODAL LAYANAN ================= -->
<div id="service-modal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-panel">
        <div class="flex items-start justify-between">
            <div>
                <p class="eyebrow">Tarif Cetak</p>
                <h3 class="mt-2 font-display text-3xl" data-modal-title>Layanan Baru</h3>
            </div>
            <button type="button" class="btn-icon" data-modal-close>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="service-form" method="post" action="<?= url('jasa/simpan') ?>" class="mt-6 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="0">

            <div>
                <label class="label" for="sv-name">Nama layanan</label>
                <input id="sv-name" name="name" class="input" required placeholder="Contoh: Cetak A4 Warna">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="label" for="sv-paper">Ukuran kertas</label>
                    <select id="sv-paper" name="paper_size" class="input">
                        <?php foreach (['A4', 'F4', 'A3', 'A5'] as $paper): ?>
                            <option value="<?= $paper ?>"><?= $paper ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="label" for="sv-mode">Mode warna</label>
                    <select id="sv-mode" name="color_mode" class="input">
                        <option value="bw">Hitam Putih</option>
                        <option value="color">Warna</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="label" for="sv-price">Harga per lembar</label>
                <input id="sv-price" name="price_per_page" type="number" min="0" step="1" class="input tnum" value="500" required>
            </div>

            <label class="flex cursor-pointer items-center gap-2.5 pt-1 text-xs text-muted">
                <input type="checkbox" name="is_active" value="1" checked class="h-3.5 w-3.5 rounded border-line bg-ink accent-[#C6A664]">
                Aktif dan tampil di kasir
            </label>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-ghost" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary">Simpan Layanan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL FINISHING ================= -->
<div id="finishing-modal" class="modal">
    <div class="modal-backdrop"></div>
    <div class="modal-panel !max-w-md">
        <div class="flex items-start justify-between">
            <div>
                <p class="eyebrow">Tambahan</p>
                <h3 class="mt-2 font-display text-3xl" data-modal-title>Finishing Baru</h3>
            </div>
            <button type="button" class="btn-icon" data-modal-close>
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
            </button>
        </div>

        <form id="finishing-form" method="post" action="<?= url('finishing/simpan') ?>" class="mt-6 space-y-4">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="0">

            <div>
                <label class="label" for="fn-name">Nama finishing</label>
                <input id="fn-name" name="name" class="input" required placeholder="Contoh: Laminasi A4">
            </div>
            <div>
                <label class="label" for="fn-price">Harga</label>
                <input id="fn-price" name="price" type="number" min="0" step="1" class="input tnum" value="5000" required>
            </div>

            <label class="flex cursor-pointer items-center gap-2.5 pt-1 text-xs text-muted">
                <input type="checkbox" name="is_active" value="1" checked class="h-3.5 w-3.5 rounded border-line bg-ink accent-[#C6A664]">
                Aktif dan tampil di kasir
            </label>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" class="btn-ghost" data-modal-close>Batal</button>
                <button type="submit" class="btn-primary">Simpan Finishing</button>
            </div>
        </form>
    </div>
</div>
