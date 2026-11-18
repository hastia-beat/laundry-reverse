<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT o.*, u.name AS customer_name, u.email, u.phone FROM orders o JOIN users u ON u.id=o.user_id WHERE o.id=?");
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) { flash('error','Pesanan tidak ditemukan.'); redirect('admin/pesanan.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Token tidak valid.'); redirect('admin/detail-pesanan.php?id='.$id); }
    $action = $_POST['action'] ?? '';

    if ($action === 'update_status') {
        $newStatus = trim($_POST['status'] ?? '');
        if (!in_array($newStatus, STATUS_PENGERJAAN, true)) { flash('error','Status tidak valid.'); }
        else {
            $pdo->prepare("UPDATE orders SET status=? WHERE id=?")->execute([$newStatus, $id]);
            flash('success','Status pengerjaan diperbarui menjadi "'.$newStatus.'".');
        }
        redirect('admin/detail-pesanan.php?id='.$id);
    }
    if ($action === 'update_final') {
        $fq = (float) ($_POST['final_quantity'] ?? 0);
        $ft = (float) ($_POST['final_total'] ?? 0);
        if ($fq <= 0) { flash('error','Jumlah akhir harus lebih dari 0.'); }
        elseif ($ft < 0) { flash('error','Total akhir tidak boleh negatif.'); }
        else {
            $pdo->prepare("UPDATE orders SET final_quantity=?, final_total=? WHERE id=?")->execute([$fq, $ft, $id]);
            flash('success','Jumlah dan total akhir berhasil diperbarui.');
        }
        redirect('admin/detail-pesanan.php?id='.$id);
    }
    if ($action === 'update_payment') {
        $pay = trim($_POST['payment_status'] ?? '');
        if (!in_array($pay, PAYMENT_STATUSES, true)) { flash('error','Status pembayaran tidak valid.'); }
        else {
            $pdo->prepare("UPDATE orders SET payment_status=? WHERE id=?")->execute([$pay, $id]);
            flash('success','Status pembayaran diperbarui.');
        }
        redirect('admin/detail-pesanan.php?id='.$id);
    }
    flash('error','Aksi tidak dikenali.');
    redirect('admin/detail-pesanan.php?id='.$id);
}

$pageTitle = 'Detail Pesanan #'.$order['order_number'];
$activeMenu = 'pesanan';
$isAdminArea = true; $layoutType = 'admin';
$menuItems = require __DIR__ . '/_menu.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<a href="<?= e(BASE_URL) ?>/admin/pesanan.php" class="text-sm text-brand-700 hover:underline mb-4 inline-block">← Kembali ke Data Pesanan</a>

<div class="grid md:grid-cols-2 gap-6 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-brand-900 mb-4">Informasi Pesanan</h2>
        <dl class="text-sm space-y-2">
            <div class="flex justify-between"><dt class="text-slate-500">No. Pesanan</dt><dd class="font-mono"><?= e($order['order_number']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Tanggal</dt><dd><?= format_tanggal($order['created_at']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Pelanggan</dt><dd><?= e($order['customer_name']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd><?= e($order['email']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">No. HP</dt><dd><?= e($order['phone']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Layanan</dt><dd><?= e($order['service_name']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Harga Satuan</dt><dd><?= format_rupiah($order['unit_price']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Jumlah Pesan</dt><dd><?= e($order['quantity']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Estimasi Total</dt><dd><?= format_rupiah($order['estimated_total']) ?></dd></div>
            <?php if ($order['notes']): ?>
                <div><dt class="text-slate-500 mb-1">Catatan</dt><dd class="bg-slate-50 rounded p-2"><?= e($order['notes']) ?></dd></div>
            <?php endif; ?>
        </dl>
    </div>

    <div class="space-y-6">
        <!-- Update status pengerjaan -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="font-semibold text-brand-900 mb-3">Status Pengerjaan</h2>
            <div class="mb-3"><span class="px-2 py-1 rounded-full text-xs <?= status_badge_class($order['status']) ?>"><?= e($order['status']) ?></span></div>
            <form method="POST" class="flex gap-2">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_status">
                <select name="status" class="flex-1 px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    <?php foreach (STATUS_PENGERJAAN as $st): ?>
                        <option value="<?= e($st) ?>" <?= $order['status']===$st?'selected':'' ?>><?= e($st) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-700 text-white text-sm hover:bg-brand-800">Update</button>
            </form>
        </div>

        <!-- Update jumlah & total akhir -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="font-semibold text-brand-900 mb-3">Jumlah & Total Akhir</h2>
            <form method="POST" class="space-y-3">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_final">
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Jumlah / Berat Akhir</label>
                    <input type="number" step="0.1" min="0.1" name="final_quantity" value="<?= e($order['final_quantity'] ?? $order['quantity']) ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs text-slate-500 mb-1">Total Akhir (Rp)</label>
                    <input type="number" step="0.01" min="0" name="final_total" value="<?= e($order['final_total'] ?? $order['estimated_total']) ?>" class="w-full px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-brand-500 outline-none">
                </div>
                <button type="submit" class="w-full py-2 rounded-lg bg-brand-700 text-white text-sm hover:bg-brand-800">Simpan</button>
            </form>
        </div>

        <!-- Update status pembayaran -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <h2 class="font-semibold text-brand-900 mb-3">Status Pembayaran</h2>
            <div class="mb-3"><span class="px-2 py-1 rounded-full text-xs <?= payment_badge_class($order['payment_status']) ?>"><?= e($order['payment_status']) ?></span></div>
            <form method="POST" class="flex gap-2">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="update_payment">
                <select name="payment_status" class="flex-1 px-3 py-2 rounded-lg border border-slate-300 text-sm focus:border-brand-500 outline-none">
                    <?php foreach (PAYMENT_STATUSES as $p): ?>
                        <option value="<?= e($p) ?>" <?= $order['payment_status']===$p?'selected':'' ?>><?= e($p) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2 rounded-lg bg-brand-700 text-white text-sm hover:bg-brand-800">Update</button>
            </form>
        </div>
    </div>
</div>
<?php
require __DIR__ . '/../includes/sidebar_end.php';
require __DIR__ . '/../includes/footer.php';
