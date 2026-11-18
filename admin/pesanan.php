<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();
$search = trim($_GET['search'] ?? '');
$statusFilter = trim($_GET['status'] ?? '');

$sql = "SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON u.id=o.user_id WHERE 1=1";
$params = [];
if ($search !== '') {
    $sql .= " AND (o.order_number LIKE :s OR u.name LIKE :sn)";
    $params[':s'] = "%$search%"; $params[':sn'] = "%$search%";
}
if ($statusFilter !== '' && in_array($statusFilter, STATUS_PENGERJAAN, true)) {
    $sql .= " AND o.status = :st"; $params[':st'] = $statusFilter;
}
$sql .= " ORDER BY o.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$pageTitle = 'Data Pesanan';
$activeMenu = 'pesanan';
$isAdminArea = true; $layoutType = 'admin';
$menuItems = require __DIR__ . '/_menu.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-brand-900">Data Pesanan</h1>
    <p class="text-slate-500 text-sm">Kelola seluruh pesanan pelanggan.</p>
</div>

<form method="GET" class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4 mb-6 flex flex-wrap gap-3">
    <input type="text" name="search" value="<?= e($search) ?>" placeholder="Cari nomor pesanan / nama pelanggan..." class="flex-1 min-w-[200px] px-4 py-2 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm">
    <select name="status" class="px-4 py-2 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none text-sm">
        <option value="">Semua Status</option>
        <?php foreach (STATUS_PENGERJAAN as $st): ?>
            <option value="<?= e($st) ?>" <?= $statusFilter===$st?'selected':'' ?>><?= e($st) ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="px-4 py-2 rounded-lg bg-brand-700 text-white text-sm font-medium hover:bg-brand-800">Filter</button>
    <a href="<?= e(BASE_URL) ?>/admin/pesanan.php" class="px-4 py-2 rounded-lg border border-slate-300 text-slate-600 text-sm hover:bg-slate-100">Reset</a>
</form>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <?php if (!$orders): ?>
        <div class="px-5 py-16 text-center text-slate-500 text-sm">Tidak ada pesanan ditemukan.</div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                    <tr>
                        <th class="text-left px-4 py-3">No. Pesanan</th>
                        <th class="text-left px-4 py-3">Pelanggan</th>
                        <th class="text-left px-4 py-3">Tanggal</th>
                        <th class="text-left px-4 py-3">Layanan</th>
                        <th class="text-left px-4 py-3">Jumlah</th>
                        <th class="text-left px-4 py-3">Total</th>
                        <th class="text-left px-4 py-3">Status</th>
                        <th class="text-left px-4 py-3">Bayar</th>
                        <th class="text-left px-4 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($orders as $o): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-3 font-mono text-xs"><?= e($o['order_number']) ?></td>
                        <td class="px-4 py-3"><?= e($o['customer_name']) ?></td>
                        <td class="px-4 py-3 text-xs"><?= format_tanggal($o['created_at']) ?></td>
                        <td class="px-4 py-3"><?= e($o['service_name']) ?></td>
                        <td class="px-4 py-3"><?= e($o['final_quantity'] ?? $o['quantity']) ?></td>
                        <td class="px-4 py-3"><?= format_rupiah($o['final_total'] ?? $o['estimated_total']) ?></td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs <?= status_badge_class($o['status']) ?>"><?= e($o['status']) ?></span></td>
                        <td class="px-4 py-3"><span class="px-2 py-1 rounded-full text-xs <?= payment_badge_class($o['payment_status']) ?>"><?= e($o['payment_status']) ?></span></td>
                        <td class="px-4 py-3"><a href="<?= e(BASE_URL) ?>/admin/detail-pesanan.php?id=<?= (int)$o['id'] ?>" class="text-brand-700 hover:underline">Kelola</a></td>
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
