<section class="animate-fade-up">
    <div class="max-w-2xl">
        <p class="eyebrow">Konfigurasi</p>
        <h2 class="mt-3 display text-4xl sm:text-5xl">Pengaturan <span class="italic text-gold/90">Toko</span></h2>
        <p class="mt-3 text-sm leading-relaxed text-muted">
            Identitas toko, format struk, dan keamanan akun administrator.
        </p>
    </div>

    <div class="mt-7 grid gap-4 xl:grid-cols-[1.4fr_1fr]">
        <div class="space-y-4">
            <!-- Profil toko -->
            <div class="card p-5 sm:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="eyebrow">Identitas</p>
                        <h3 class="mt-1.5 font-display text-xl">Profil toko</h3>
                    </div>
                    <span class="badge badge-gold">Tampil di struk</span>
                </div>

                <form method="post" action="<?= url('pengaturan/simpan') ?>" class="mt-5 space-y-4">
                    <?= csrf_field() ?>
                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label" for="store_name">Nama toko</label>
                            <input id="store_name" name="store_name" class="input" value="<?= e($settings['store_name'] ?? APP_NAME) ?>" required>
                        </div>
                        <div>
                            <label class="label" for="store_tagline">Tagline</label>
                            <input id="store_tagline" name="store_tagline" class="input" value="<?= e($settings['store_tagline'] ?? '') ?>">
                        </div>
                    </div>

                    <div>
                        <label class="label" for="store_address">Alamat</label>
                        <textarea id="store_address" name="store_address" rows="2" class="input resize-none"><?= e($settings['store_address'] ?? '') ?></textarea>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label" for="store_phone">Telepon</label>
                            <input id="store_phone" name="store_phone" class="input" value="<?= e($settings['store_phone'] ?? '') ?>">
                        </div>
                        <div>
                            <label class="label" for="invoice_prefix">Prefix invoice</label>
                            <input id="invoice_prefix" name="invoice_prefix" class="input" value="<?= e($settings['invoice_prefix'] ?? 'AF') ?>" maxlength="6">
                        </div>
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label" for="receipt_width">Lebar struk</label>
                            <select id="receipt_width" name="receipt_width" class="input">
                                <option value="58" <?= ($settings['receipt_width'] ?? '58') === '58' ? 'selected' : '' ?>>58 mm (thermal kecil)</option>
                                <option value="80" <?= ($settings['receipt_width'] ?? '') === '80' ? 'selected' : '' ?>>80 mm (thermal standar)</option>
                            </select>
                        </div>
                        <div>
                            <label class="label" for="tax_percent">Pajak (%)</label>
                            <input id="tax_percent" name="tax_percent" type="number" min="0" max="100" step="0.1" class="input tnum" value="<?= e($settings['tax_percent'] ?? '0') ?>">
                        </div>
                    </div>

                    <div>
                        <label class="label" for="receipt_footer">Footer struk</label>
                        <textarea id="receipt_footer" name="receipt_footer" rows="3" class="input resize-none"><?= e($settings['receipt_footer'] ?? '') ?></textarea>
                    </div>

                    <div class="flex justify-end pt-1">
                        <button type="submit" class="btn-primary">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="space-y-4">
            <!-- Akun -->
            <div class="card p-5 sm:p-6">
                <p class="eyebrow">Akun</p>
                <h3 class="mt-1.5 font-display text-xl">Profil administrator</h3>

                <form method="post" action="<?= url('pengaturan/profil') ?>" class="mt-5 space-y-4">
                    <?= csrf_field() ?>
                    <div>
                        <label class="label" for="user_name">Nama</label>
                        <input id="user_name" name="name" class="input" value="<?= e($user['name'] ?? '') ?>" required>
                    </div>
                    <div>
                        <label class="label" for="user_username">Username</label>
                        <input id="user_username" name="username" class="input" value="<?= e($user['username'] ?? '') ?>" required>
                    </div>
                    <button type="submit" class="btn-ghost w-full !py-2.5 text-xs">Simpan Profil</button>
                </form>
            </div>

            <!-- Password -->
            <div class="card p-5 sm:p-6">
                <p class="eyebrow">Keamanan</p>
                <h3 class="mt-1.5 font-display text-xl">Ganti password</h3>

                <form method="post" action="<?= url('pengaturan/password') ?>" class="mt-5 space-y-3.5">
                    <?= csrf_field() ?>
                    <div>
                        <label class="label" for="current_password">Password saat ini</label>
                        <input id="current_password" name="current_password" type="password" class="input" autocomplete="current-password" required>
                    </div>
                    <div>
                        <label class="label" for="new_password">Password baru</label>
                        <input id="new_password" name="new_password" type="password" class="input" autocomplete="new-password" minlength="6" required>
                    </div>
                    <div>
                        <label class="label" for="confirm_password">Konfirmasi password baru</label>
                        <input id="confirm_password" name="confirm_password" type="password" class="input" autocomplete="new-password" minlength="6" required>
                    </div>
                    <button type="submit" class="btn-primary w-full !py-2.5 text-xs">Perbarui Password</button>
                </form>
            </div>

            <div class="rounded-lg border border-line px-5 py-4">
                <p class="text-[10px] uppercase tracking-[0.2em] text-muted">Informasi</p>
                <p class="mt-2 text-[11px] leading-relaxed text-muted/70">
                    Perubahan lebar struk berlaku untuk cetakan berikutnya. Pastikan printer thermal sudah diatur ke ukuran yang sama.
                </p>
            </div>
        </div>
    </div>
</section>
