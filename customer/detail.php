<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_customer();

$uid = (int) $_SESSION['user_id'];
$pdo = db();
$id = (int) ($_GET['id'] ?? 0);

$stmt = $pdo->prepare("SELECT o.*, u.name AS customer_name, u.email, u.phone FROM orders o JOIN users u ON u.id=o.user_id WHERE o.id=? AND o.user_id=?");
$stmt->execute([$id, $uid]);
$order = $stmt->fetch();

if (!$order) {
    flash('error','Pesanan tidak ditemukan.');
    redirect('customer/riwayat.php');
}

$pageTitle = 'Detail Pesanan';
$activeMenu = 'riwayat';
$menuItems = require __DIR__ . '/_menu.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<a href="<?= e(BASE_URL) ?>/customer/riwayat.php" class="text-sm text-brand-700 hover:underline mb-4 inline-block">← Kembali ke Riwayat</a>

<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-brand-900 mb-4">Informasi Pesanan</h2>
        <dl class="text-sm space-y-3">
            <div class="flex justify-between"><dt class="text-slate-500">Nomor Pesanan</dt><dd class="font-mono"><?= e($order['order_number']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Tanggal</dt><dd><?= format_tanggal($order['created_at']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Nama Pelanggan</dt><dd><?= e($order['customer_name']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Layanan</dt><dd><?= e($order['service_name']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Harga Satuan</dt><dd><?= format_rupiah($order['unit_price']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Jumlah Cucian</dt><dd><?= e($order['quantity']) ?> → <?= $order['final_quantity']!==null ? e($order['final_quantity']).' (final)' : 'menunggu' ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Estimasi Total</dt><dd><?= format_rupiah($order['estimated_total']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Total Akhir</dt><dd class="font-semibold text-brand-700"><?= $order['final_total']!==null ? format_rupiah($order['final_total']) : 'menunggu' ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Status Pembayaran</dt><dd><span class="px-2 py-1 rounded-full text-xs <?= payment_badge_class($order['payment_status']) ?>"><?= e($order['payment_status']) ?></span></dd></div>
            <?php if ($order['notes']): ?>
                <div><dt class="text-slate-500 mb-1">Catatan</dt><dd class="bg-slate-50 rounded-lg p-3 text-slate-700"><?= e($order['notes']) ?></dd></div>
            <?php endif; ?>
        </dl>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-brand-900 mb-4">Status Pengerjaan</h2>
        <?php
        $isCancelled = $order['status'] === 'Dibatalkan';
        $currentIdx = array_search($order['status'], STATUS_TIMELINE);
        ?>
        <?php if ($isCancelled): ?>
            <div class="rounded-lg bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 text-sm">Pesanan ini telah dibatalkan.</div>
        <?php else: ?>
            <ol class="relative border-l-2 border-brand-100 ml-3 space-y-6">
                <?php foreach (STATUS_TIMELINE as $i => $step):
                    $done = $currentIdx !== false && $i <= $currentIdx;
                    $active = $i === $currentIdx;
                ?>
                    <li class="ml-6">
                        <span class="absolute -left-[11px] w-5 h-5 rounded-full border-2 <?= $done?'bg-brand-700 border-brand-700':'bg-white border-slate-300' ?> flex items-center justify-center">
                            <?php if ($done): ?><svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg><?php endif; ?>
                        </span>
                        <div class="<?= $active?'text-brand-800 font-semibold':'text-slate-600' ?> text-sm"><?= e($step) ?></div>
                    </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>
</div>
<?php
require __DIR__ . '/../includes/sidebar_end.php';
require __DIR__ . '/../includes/footer.php';
