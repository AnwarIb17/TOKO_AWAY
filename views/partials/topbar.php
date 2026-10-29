<header class="sticky top-0 z-30 border-b border-line bg-ink/85 backdrop-blur-xl">
    <div class="flex items-center justify-between gap-4 px-5 py-3.5 sm:px-8 lg:px-10">
        <div class="flex min-w-0 items-center gap-3">
            <button id="sidebar-toggle" type="button" class="btn-icon lg:hidden" aria-label="Buka menu">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round">
                    <path d="M4 7h16M4 12h16M4 17h16"/>
                </svg>
            </button>
            <div class="min-w-0">
                <p class="eyebrow hidden sm:block">Panel Internal</p>
                <h1 class="truncate font-display text-lg leading-tight text-ivory sm:text-xl"><?= e($title ?? 'Dasbor') ?></h1>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <span id="clock" class="hidden text-[11px] tabular-nums tracking-wide text-muted md:block"></span>
            <span class="hidden h-4 w-px bg-line md:block"></span>
            <a href="<?= url('kasir') ?>" class="btn-primary !px-3.5 !py-2 text-xs">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                <span class="hidden sm:inline">Transaksi Baru</span>
                <span class="sm:hidden">Kasir</span>
            </a>
        </div>
    </div>
</header>
