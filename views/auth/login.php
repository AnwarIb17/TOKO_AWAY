<?php $flashes = flashes(); ?>
<div class="relative z-10 grid min-h-screen lg:grid-cols-[1.15fr_1fr]">
    <!-- Artistic panel -->
    <section class="relative hidden overflow-hidden border-r border-line lg:flex lg:flex-col lg:justify-between">
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-24 top-1/4 h-[520px] w-[520px] rounded-full bg-gold/[0.07] blur-[110px]"></div>
            <div class="absolute bottom-0 right-0 h-[420px] w-[420px] rounded-full bg-[#6b5a35]/10 blur-[100px]"></div>
            <div class="absolute inset-0 opacity-[0.16]" style="background-image: repeating-linear-gradient(115deg, transparent 0 38px, rgba(198,166,100,.18) 38px 39px);"></div>
        </div>

        <div class="relative px-14 pt-14">
            <div class="flex items-center gap-4">
                <span class="h-px w-12 bg-gold/60"></span>
                <span class="text-[10px] uppercase tracking-[0.42em] text-muted">Est. 2026</span>
            </div>
        </div>

        <div class="relative px-14">
            <p class="eyebrow mb-6">Fotokopi &amp; Alat Tulis</p>
            <h1 class="display text-[clamp(4rem,9vw,8.5rem)]">
                AWAY<br>
                <span class="italic text-gold/90">Fotocopy</span>
            </h1>
            <p class="mt-8 max-w-md text-sm leading-relaxed text-muted">
                Ruang kerja internal untuk mengelola transaksi, stok, dan keuangan toko
                dengan tenang, cepat, dan rapi.
            </p>
        </div>

        <div class="relative flex items-end justify-between px-14 pb-12">
            <p class="max-w-[240px] font-display text-lg italic leading-snug text-ivory/50">
                “Detail kecil yang dikerjakan dengan rapi.”
            </p>
            <span class="text-[10px] uppercase tracking-[0.32em] text-muted/50">Internal Panel</span>
        </div>
    </section>

    <!-- Form panel -->
    <section class="relative flex items-center justify-center px-6 py-14">
        <div class="w-full max-w-[380px] animate-fade-up">
            <div class="mb-10 lg:hidden">
                <span class="font-display text-3xl tracking-[0.08em]">AWAY</span>
                <span class="mt-1 block text-[9px] uppercase tracking-[0.42em] text-muted">Fotocopy</span>
            </div>

            <p class="eyebrow">Selamat datang</p>
            <h2 class="mt-3 font-display text-4xl leading-tight">Masuk ke panel</h2>
            <p class="mt-3 text-sm leading-relaxed text-muted">
                Gunakan kredensial administrator untuk melanjutkan.
            </p>

            <?php foreach ($flashes as $flash): ?>
                <div class="mt-6 flex items-start gap-3 rounded-md border px-4 py-3 text-sm <?= $flash['type'] === 'error' ? 'border-red-400/25 bg-red-500/10 text-red-200' : 'border-gold/25 bg-gold/10 text-gold' ?>">
                    <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-current"></span>
                    <span class="leading-relaxed"><?= e($flash['message']) ?></span>
                </div>
            <?php endforeach; ?>

            <form method="post" action="<?= url('login') ?>" class="mt-8 space-y-5">
                <?= csrf_field() ?>
                <div>
                    <label class="label" for="username">Username</label>
                    <input id="username" name="username" type="text" class="input-lg" placeholder="admin" autocomplete="username" autofocus required>
                </div>
                <div>
                    <label class="label" for="password">Password</label>
                    <input id="password" name="password" type="password" class="input-lg" placeholder="••••••••" autocomplete="current-password" required>
                </div>

                <button type="submit" class="btn-primary mt-2 w-full !py-3.5">
                    Masuk
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round">
                        <path d="M5 12h14M13 6l6 6-6 6"/>
                    </svg>
                </button>
            </form>

            <div class="mt-8 flex items-center gap-4">
                <span class="h-px flex-1 bg-line"></span>
                <span class="text-[9px] uppercase tracking-[0.3em] text-muted/60">Akses Internal</span>
                <span class="h-px flex-1 bg-line"></span>
            </div>
            <p class="mt-6 text-center text-[11px] leading-relaxed text-muted/60">
                Akses terbatas untuk administrator toko.<br>
                Hubungi pemilik jika Anda memerlukan akses.
            </p>
        </div>
    </section>
</div>
