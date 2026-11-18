<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_customer();

$pdo = db();
$services = $pdo->query("SELECT id, name, price, unit FROM services WHERE is_active = 1 ORDER BY id")->fetchAll();
$preselectId = isset($_GET['service']) ? (int) $_GET['service'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Token tidak valid.'); redirect('customer/pesan.php'); }
    $serviceId = (int) ($_POST['service_id'] ?? 0);
    $quantity  = (float) ($_POST['quantity'] ?? 0);
    $notes     = trim($_POST['notes'] ?? '');

    $errors = [];
    $service = null;
    foreach ($services as $s) { if ((int)$s['id'] === $serviceId) { $service = $s; break; } }
    if (!$service) $errors[] = 'Layanan tidak valid atau tidak aktif.';
    if ($quantity <= 0) $errors[] = 'Jumlah cucian harus lebih dari 0.';

    if ($errors) {
        foreach ($errors as $err) flash('error', $err);
        redirect('customer/pesan.php' . ($preselectId ? '?service='.$preselectId : ''));
    }

    // Hitung ulang di server (validasi)
    $unitPrice = (float) $service['price'];
    $estimated = $unitPrice * $quantity;

    $orderNum = generate_order_number($pdo);
    $stmt = $pdo->prepare("INSERT INTO orders (order_number, user_id, service_id, service_name, unit_price, quantity, estimated_total, notes, status, payment_status) VALUES (?,?,?,?,?,?,?,?, 'Menunggu Konfirmasi', 'Belum Dibayar')");
    $stmt->execute([
        $orderNum, (int) $_SESSION['user_id'], $serviceId,
        $service['name'], $unitPrice, $quantity, $estimated, $notes ?: null
    ]);
    $newId = (int) $pdo->lastInsertId();
    flash('success', "Pesanan #$orderNum berhasil dibuat. Silakan antar cucian ke outlet kami.");
    redirect('customer/detail.php?id=' . $newId);
}

$pageTitle = 'Buat Pesanan';
$activeMenu = 'pesan';
$menuItems = require __DIR__ . '/_menu.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold text-brand-900 mb-1">Buat Pesanan Laundry</h1>
    <p class="text-slate-500 text-sm mb-6">Pilih layanan dan masukkan estimasi berat/jumlah cucian. Total biaya dihitung otomatis.</p>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <form method="POST" class="space-y-5">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Pilih Layanan</label>
                <select id="service-select" name="service_id" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
                    <option value="" data-price="0" data-unit="">-- Pilih layanan --</option>
                    <?php foreach ($services as $s): ?>
                        <option value="<?= (int)$s['id'] ?>" data-price="<?= (float)$s['price'] ?>" data-unit="<?= e($s['unit']) ?>" <?= $preselectId===(int)$s['id']?'selected':'' ?>>
                            <?= e($s['name']) ?> — <?= format_rupiah($s['price']) ?>/<?= e($s['unit']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (!$services): ?><p class="text-xs text-rose-600 mt-1">Tidak ada layanan aktif saat ini.</p><?php endif; ?>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah / Estimasi Berat <span class="text-slate-400">(satuan: <span id="unit-label">-</span>)</span></label>
                <input id="quantity" type="number" name="quantity" step="0.1" min="0.1" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none" placeholder="Contoh: 3.5">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Catatan Tambahan <span class="text-slate-400">(opsional)</span></label>
                <textarea name="notes" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none" placeholder="Misal: pisahkan pakaian putih, jangan dikeringkan, dll."></textarea>
            </div>
            <div class="rounded-xl bg-brand-50 border border-brand-100 p-4 flex items-center justify-between">
                <div>
                    <div class="text-xs text-slate-500">Estimasi Total Biaya</div>
                    <div id="estimate-total" class="text-2xl font-bold text-brand-800">Rp0</div>
                </div>
                <div class="text-xs text-slate-400 max-w-[180px] text-right">Harga akhir akan dikonfirmasi admin setelah cucian ditimbang.</div>
            </div>
            <button type="submit" <?= !$services?'disabled':'' ?> class="w-full py-3 rounded-lg bg-brand-700 text-white font-semibold hover:bg-brand-800 transition disabled:opacity-50">Kirim Pesanan</button>
        </form>
    </div>
</div>
<?php
require __DIR__ . '/../includes/sidebar_end.php';
require __DIR__ . '/../includes/footer.php';
