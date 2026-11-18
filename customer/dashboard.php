<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_customer();

$uid = (int) $_SESSION['user_id'];
$pdo = db();

$totalOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE user_id=$uid")->fetchColumn();
$processing  = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE user_id=$uid AND status NOT IN ('Selesai','Dibatalkan')")->fetchColumn();
$selesai     = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE user_id=$uid AND status='Selesai'")->fetchColumn();
$recent = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC LIMIT 5");
$recent->execute([$uid]);
$recentOrders = $recent->fetchAll();

$pageTitle = 'Dashboard Pelanggan';
$activeMenu = 'dashboard';
$menuItems = [
    ['key'=>'dashboard','label'=>'Dashboard','href'=>'customer/dashboard.php','icon'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0h6"/></svg>'],
    ['key'=>'pesan','label'=>'Buat Pesanan','href'=>'customer/pesan.php','icon'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>'],
    ['key'=>'riwayat','label'=>'Riwayat Pesanan','href'=>'customer/riwayat.php','icon'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>'],
    ['key'=>'profil','label'=>'Profil','href'=>'customer/profil.php','icon'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'],
    ['key'=>'logout','label'=>'Logout','href'=>'logout.php','icon'=>'<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>'],
];
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-brand-900">Halo, <?= e($_SESSION['user_name']) ?>! 👋</h1>
    <p class="text-slate-500 text-sm">Selamat datang di dashboard LaundryKu Anda.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-500">Total Pesanan</div>
        <div class="text-3xl font-bold text-brand-800 mt-1"><?= $totalOrders ?></div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-500">Sedang Diproses</div>
        <div class="text-3xl font-bold text-amber-600 mt-1"><?= $processing ?></div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-500">Pesanan Selesai</div>
        <div class="text-3xl font-bold text-emerald-600 mt-1"><?= $selesai ?></div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="font-semibold text-brand-900">Pesanan Terbaru</h2>
        <a href="<?= e(BASE_URL) ?>/customer/riwayat.php" class="text-sm text-brand-700 hover:underline">Lihat semua →</a>
    </div>
    <?php if (!$recentOrders): ?>
        <div class="px-5 py-12 text-center text-slate-500 text-sm">
            Anda belum memiliki pesanan.<br>
            <a href="<?= e(BASE_URL) ?>/customer/pesan.php" class="inline-block mt-3 px-4 py-2 rounded-lg bg-brand-700 text-white text-sm font-medium hover:bg-brand-800 transition">Buat Pesanan Pertama</a>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="text-left px-5 py-3">No. Pesanan</th>
                        <th class="text-left px-5 py-3">Layanan</th>
                        <th class="text-left px-5 py-3">Total</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-left px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($recentOrders as $o): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-mono text-xs"><?= e($o['order_number']) ?></td>
                        <td class="px-5 py-3"><?= e($o['service_name']) ?></td>
                        <td class="px-5 py-3"><?= format_rupiah($o['final_total'] ?? $o['estimated_total']) ?></td>
                        <td class="px-5 py-3"><span class="px-2 py-1 rounded-full text-xs <?= status_badge_class($o['status']) ?>"><?= e($o['status']) ?></span></td>
                        <td class="px-5 py-3"><a href="<?= e(BASE_URL) ?>/customer/detail.php?id=<?= (int)$o['id'] ?>" class="text-brand-700 hover:underline">Detail</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php
require __DIR__ . '/../includes/sidebar_end.php';
require __DIR__ . '/../includes/footer.php';
