<?php
$path = current_path();
$user = Auth::user();
$nav = [
    ['label' => 'Dasbor',      'path' => '/dashboard',  'num' => '01', 'icon' => 'M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z'],
    ['label' => 'Kasir',       'path' => '/kasir',      'num' => '02', 'icon' => 'M3 4h2l2.4 11.2a1 1 0 0 0 1 .8h8.8a1 1 0 0 0 1-.8L20 7H6M9 20h.01M17 20h.01'],
    ['label' => 'Transaksi',   'path' => '/transaksi', 'num' => '03', 'icon' => 'M6 3h12v18l-3-2-3 2-3-2-3 2zM9 8h6M9 12h6M9 16h3'],
    ['label' => 'Produk',      'path' => '/produk',    'num' => '04', 'icon' => 'M21 8 12 3 3 8v8l9 5 9-5zM3 8l9 5 9-5M12 13v8'],
    ['label' => 'Jasa Cetak',  'path' => '/jasa',      'num' => '05', 'icon' => 'M6 9V3h12v6M6 18H4a1 1 0 0 1-1-1v-6a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v6a1 1 0 0 1-1 1h-2M6 14h12v7H6z'],
    ['label' => 'Keuangan',    'path' => '/keuangan',  'num' => '06', 'icon' => 'M3 7h18v12H3zM3 7l2-3h14l2 3M16 13h2'],
    ['label' => 'Shift Kasir', 'path' => '/shift',     'num' => '07', 'icon' => 'M12 8v5l3 2M21 12a9 9 0 1 1-9-9 9 9 0 0 1 9 9z'],
    ['label' => 'Laporan',     'path' => '/laporan',   'num' => '08', 'icon' => 'M4 20V10M10 20V4M16 20v-7M22 20H2'],
    ['label' => 'Pengaturan',  'path' => '/pengaturan','num' => '09', 'icon' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-2.9 1.2V21a2 2 0 1 1-4 0v-.1A1.7 1.7 0 0 0 7 19.4l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A1.7 1.7 0 0 0 3 13.7H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.6 7l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1A1.7 1.7 0 0 0 10 3.6V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 2.9 1.2l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0 1.2 2.9H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z'],
];

$isActive = function (string $itemPath) use ($path): bool {
    if ($itemPath === '/dashboard') {
        return $path === '/' || $path === '/dashboard';
    }
    return $path === $itemPath || str_starts_with($path, $itemPath . '/');
};
?>
<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-[248px] border-r border-line bg-charcoal/95 backdrop-blur-2xl">
    <div class="flex h-full flex-col">
        <!-- Brand -->
        <div class="px-6 pb-7 pt-7">
            <a href="<?= url('dashboard') ?>" class="group block">
                <div class="flex items-start justify-between">
                    <div>
                        <span class="block font-display text-[30px] font-normal leading-none tracking-[0.06em] text-ivory transition-colors duration-500 group-hover:text-gold">AWAY</span>
                        <span class="mt-2 block text-[9px] uppercase tracking-[0.42em] text-muted">Fotocopy</span>
                    </div>
                    <span class="mt-1 h-1.5 w-1.5 rounded-full bg-gold shadow-[0_0_12px_rgba(198,166,100,.8)]"></span>
                </div>
                <div class="mt-4 h-px w-full bg-gradient-to-r from-gold/50 via-line to-transparent"></div>
                <p class="mt-3 text-[10px] uppercase tracking-[0.22em] text-muted/70">Fotokopi &amp; Alat Tulis</p>
            </a>
        </div>

        <!-- Nav -->
        <nav class="flex-1 space-y-0.5 overflow-y-auto px-3 pb-4 no-scrollbar">
            <?php foreach ($nav as $item): ?>
                <?php $active = $isActive($item['path']); ?>
                <a href="<?= url(ltrim($item['path'], '/')) ?>" class="nav-item group <?= $active ? 'active' : '' ?>">
                    <span class="nav-num"><?= $item['num'] ?></span>
                    <svg class="h-[17px] w-[17px] shrink-0 <?= $active ? 'text-gold' : '' ?>" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">
                        <path d="<?= $item['icon'] ?>"/>
                    </svg>
                    <span class="tracking-wide"><?= e($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>

        <!-- Footer -->
        <div class="border-t border-line px-5 py-4">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-full border border-gold/30 bg-gold/10 font-display text-sm text-gold">
                    <?= e(mb_strtoupper(mb_substr($user['name'] ?? 'A', 0, 1))) ?>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-xs font-medium text-ivory"><?= e($user['name'] ?? 'Admin') ?></p>
                    <p class="truncate text-[10px] uppercase tracking-widest text-muted">Administrator</p>
                </div>
                <form method="post" action="<?= url('logout') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="text-muted transition-colors hover:text-gold" title="Keluar">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                        </svg>
                    </button>
                </form>
            </div>
            <p class="mt-4 text-center text-[9px] uppercase tracking-[0.3em] text-muted/40">Est. 2026</p>
        </div>
    </div>
</aside>
