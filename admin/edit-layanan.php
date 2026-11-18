<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();
$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM services WHERE id=?");
$stmt->execute([$id]);
$service = $stmt->fetch();
if (!$service) { flash('error','Layanan tidak ditemukan.'); redirect('admin/layanan.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Token tidak valid.'); redirect('admin/edit-layanan.php?id='.$id); }
    $name = trim($_POST['name'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $price = (float) ($_POST['price'] ?? 0);
    $unit = trim($_POST['unit'] ?? '');
    $errors = [];
    if ($name === '') $errors[] = 'Nama wajib diisi.';
    if ($desc === '') $errors[] = 'Deskripsi wajib diisi.';
    if ($price <= 0) $errors[] = 'Harga harus lebih dari 0.';
    if ($unit === '') $errors[] = 'Satuan wajib diisi.';
    if ($errors) { foreach ($errors as $e) flash('error',$e); redirect('admin/edit-layanan.php?id='.$id); }

    $pdo->prepare("UPDATE services SET name=?, description=?, price=?, unit=? WHERE id=?")->execute([$name,$desc,$price,$unit,$id]);
    flash('success','Layanan diperbarui.');
    redirect('admin/layanan.php');
}

$pageTitle = 'Edit Layanan';
$activeMenu = 'layanan';
$isAdminArea = true; $layoutType = 'admin';
$menuItems = require __DIR__ . '/_menu.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<a href="<?= e(BASE_URL) ?>/admin/layanan.php" class="text-sm text-brand-700 hover:underline mb-4 inline-block">← Kembali ke Data Layanan</a>
<h1 class="text-2xl font-bold text-brand-900 mb-6">Edit Layanan</h1>

<div class="max-w-xl bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
    <form method="POST" class="space-y-4">
        <?= csrf_field() ?>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Nama Layanan</label>
            <input type="text" name="name" required value="<?= e($service['name']) ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi</label>
            <textarea name="description" rows="3" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none"><?= e($service['description']) ?></textarea>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Harga (Rp)</label>
                <input type="number" step="0.01" min="0" name="price" required value="<?= e($service['price']) ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Satuan</label>
                <input type="text" name="unit" required value="<?= e($service['unit']) ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
        </div>
        <button type="submit" class="w-full py-2.5 rounded-lg bg-brand-700 text-white font-medium hover:bg-brand-800">Simpan Perubahan</button>
    </form>
</div>
<?php
require __DIR__ . '/../includes/sidebar_end.php';
require __DIR__ . '/../includes/footer.php';
