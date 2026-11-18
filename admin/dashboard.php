<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();
$totalOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$menunggu    = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Menunggu Konfirmasi'")->fetchColumn();
$diproses    = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('Pesanan Diterima','Sedang Dicuci','Sedang Disetrika')")->fetchColumn();
$siapAmbil   = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status='Siap Diambil'")->fetchColumn();
$pendapatan  = (float) $pdo->query("SELECT COALESCE(SUM(final_total),0) FROM orders WHERE status='Selesai' AND payment_status='Sudah Dibayar'")->fetchColumn();

$recent = $pdo->query("SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC LIMIT 6")->fetchAll();

$pageTitle = 'Dashboard Admin';
$activeMenu = 'dashboard';
$isAdminArea = true;
$layoutType = 'admin';
$menuItems = require __DIR__ . '/_menu.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-brand-900">Dashboard Admin</h1>
    <p class="text-slate-500 text-sm">Ringkasan operasional LaundryKu.</p>
</div>

<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-500">Total Pesanan</div>
        <div class="text-2xl font-bold text-brand-800 mt-1"><?= $totalOrders ?></div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-500">Menunggu Konfirmasi</div>
        <div class="text-2xl font-bold text-amber-600 mt-1"><?= $menunggu ?></div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-500">Sedang Diproses</div>
        <div class="text-2xl font-bold text-blue-600 mt-1"><?= $diproses ?></div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-500">Siap Diambil</div>
        <div class="text-2xl font-bold text-teal-600 mt-1"><?= $siapAmbil ?></div>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-5">
        <div class="text-xs text-slate-500">Pendapatan</div>
        <div class="text-lg font-bold text-emerald-600 mt-1"><?= format_rupiah($pendapatan) ?></div>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h2 class="font-semibold text-brand-900">Pesanan Terbaru</h2>
        <a href="<?= e(BASE_URL) ?>/admin/pesanan.php" class="text-sm text-brand-700 hover:underline">Kelola semua →</a>
    </div>
    <?php if (!$recent): ?>
        <div class="px-5 py-12 text-center text-slate-500 text-sm">Belum ada pesanan masuk.</div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="text-left px-5 py-3">No. Pesanan</th>
                        <th class="text-left px-5 py-3">Pelanggan</th>
                        <th class="text-left px-5 py-3">Layanan</th>
                        <th class="text-left px-5 py-3">Status</th>
                        <th class="text-left px-5 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($recent as $o): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-mono text-xs"><?= e($o['order_number']) ?></td>
                        <td class="px-5 py-3"><?= e($o['customer_name']) ?></td>
                        <td class="px-5 py-3"><?= e($o['service_name']) ?></td>
                        <td class="px-5 py-3"><span class="px-2 py-1 rounded-full text-xs <?= status_badge_class($o['status']) ?>"><?= e($o['status']) ?></span></td>
                        <td class="px-5 py-3"><a href="<?= e(BASE_URL) ?>/admin/detail-pesanan.php?id=<?= (int)$o['id'] ?>" class="text-brand-700 hover:underline">Kelola</a></td>
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
