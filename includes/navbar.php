<?php
$currentUser = current_user();
$isAdminArea = $isAdminArea ?? false;
?>
<nav class="sticky top-0 z-50 bg-white/90 backdrop-blur border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <a href="<?= e(BASE_URL) ?>/" class="flex items-center gap-2 group">
                <span class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-600 to-brand-800 flex items-center justify-center text-white font-bold text-lg shadow">L</span>
                <span class="font-bold text-lg text-brand-800 group-hover:text-brand-600 transition">LaundryKu</span>
            </a>

            <!-- Links (public) -->
            <?php if (!$isAdminArea): ?>
            <div class="hidden md:flex items-center gap-6 text-sm font-medium text-slate-600">
                <a href="<?= e(BASE_URL) ?>/" class="hover:text-brand-700 transition">Beranda</a>
                <a href="<?= e(BASE_URL) ?>/layanan.php" class="hover:text-brand-700 transition">Layanan</a>
                <a href="<?= e(BASE_URL) ?>/#tentang" class="hover:text-brand-700 transition">Tentang Kami</a>
                <a href="<?= e(BASE_URL) ?>/#kontak" class="hover:text-brand-700 transition">Kontak</a>
            </div>
            <?php endif; ?>

            <!-- Auth buttons -->
            <div class="hidden md:flex items-center gap-3">
                <?php if ($currentUser): ?>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <a href="<?= e(BASE_URL) ?>/admin/dashboard.php" class="px-4 py-2 rounded-lg bg-brand-700 text-white text-sm font-medium hover:bg-brand-800 transition">Dashboard Admin</a>
                    <?php else: ?>
                        <a href="<?= e(BASE_URL) ?>/customer/dashboard.php" class="px-4 py-2 rounded-lg bg-brand-700 text-white text-sm font-medium hover:bg-brand-800 transition">Dashboard</a>
                    <?php endif; ?>
                    <a href="<?= e(BASE_URL) ?>/logout.php" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 text-sm font-medium hover:bg-slate-100 transition">Logout</a>
                <?php else: ?>
                    <a href="<?= e(BASE_URL) ?>/login.php" class="px-4 py-2 rounded-lg text-brand-700 text-sm font-medium hover:bg-brand-50 transition">Login</a>
                    <a href="<?= e(BASE_URL) ?>/register.php" class="px-4 py-2 rounded-lg bg-brand-700 text-white text-sm font-medium hover:bg-brand-800 transition shadow-sm">Daftar</a>
                <?php endif; ?>
            </div>

            <!-- Mobile toggle -->
            <button id="mobile-menu-btn" class="md:hidden p-2 rounded-lg hover:bg-slate-100" aria-label="Menu">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
        </div>
    </div>

    <!-- Mobile menu -->
    <div id="mobile-menu" class="hidden md:hidden border-t border-slate-200 bg-white">
        <div class="px-4 py-3 space-y-2 text-sm">
            <?php if (!$isAdminArea): ?>
                <a href="<?= e(BASE_URL) ?>/" class="block py-2 px-3 rounded hover:bg-slate-100">Beranda</a>
                <a href="<?= e(BASE_URL) ?>/layanan.php" class="block py-2 px-3 rounded hover:bg-slate-100">Layanan</a>
                <a href="<?= e(BASE_URL) ?>/#tentang" class="block py-2 px-3 rounded hover:bg-slate-100">Tentang Kami</a>
                <a href="<?= e(BASE_URL) ?>/#kontak" class="block py-2 px-3 rounded hover:bg-slate-100">Kontak</a>
            <?php endif; ?>
            <div class="pt-2 border-t border-slate-200">
                <?php if ($currentUser): ?>
                    <?php if ($currentUser['role'] === 'admin'): ?>
                        <a href="<?= e(BASE_URL) ?>/admin/dashboard.php" class="block py-2 px-3 rounded bg-brand-700 text-white">Dashboard Admin</a>
                    <?php else: ?>
                        <a href="<?= e(BASE_URL) ?>/customer/dashboard.php" class="block py-2 px-3 rounded bg-brand-700 text-white">Dashboard</a>
                    <?php endif; ?>
                    <a href="<?= e(BASE_URL) ?>/logout.php" class="block py-2 px-3 rounded hover:bg-slate-100">Logout</a>
                <?php else: ?>
                    <a href="<?= e(BASE_URL) ?>/login.php" class="block py-2 px-3 rounded hover:bg-slate-100">Login</a>
                    <a href="<?= e(BASE_URL) ?>/register.php" class="block py-2 px-3 rounded bg-brand-700 text-white">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>
