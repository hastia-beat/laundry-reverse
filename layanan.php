<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$pageTitle = 'Layanan';
$services = db()->query("SELECT * FROM services WHERE is_active = 1 ORDER BY id")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-brand-900">Katalog Layanan Laundry</h1>
        <p class="text-slate-600 mt-2">Pilih layanan yang sesuai dengan kebutuhan Anda.</p>
    </div>

    <?php if (!$services): ?>
        <div class="text-center py-16 text-slate-500">Belum ada layanan tersedia saat ini.</div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($services as $s): ?>
        <div class="card-hover bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col">
            <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 9m-6 0a3 3 0 006 0"/></svg>
            </div>
            <h3 class="font-semibold text-lg text-brand-900"><?= e($s['name']) ?></h3>
            <p class="text-sm text-slate-600 mt-2 mb-4 flex-1"><?= e($s['description']) ?></p>
            <div class="flex items-end justify-between border-t border-slate-100 pt-4">
                <div>
                    <div class="text-xl font-bold text-brand-700"><?= format_rupiah($s['price']) ?></div>
                    <div class="text-xs text-slate-500">per <?= e($s['unit']) ?></div>
                </div>
                <?php if (is_logged_in() && !is_admin()): ?>
                    <a href="<?= e(BASE_URL) ?>/customer/pesan.php?service=<?= (int)$s['id'] ?>" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-medium hover:bg-brand-700 transition">Pesan Sekarang</a>
                <?php elseif (is_logged_in() && is_admin()): ?>
                    <span class="text-xs text-slate-400">Login admin</span>
                <?php else: ?>
                    <a href="<?= e(BASE_URL) ?>/login.php" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-medium hover:bg-brand-700 transition">Pesan Sekarang</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
