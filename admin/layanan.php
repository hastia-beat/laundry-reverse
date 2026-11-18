<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();

// Toggle aktif/nonaktif
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Token tidak valid.'); redirect('admin/layanan.php'); }
    $action = $_POST['action'] ?? '';
    $sid = (int) ($_POST['id'] ?? 0);

    if ($action === 'toggle') {
        $stmt = $pdo->prepare("SELECT is_active FROM services WHERE id=?");
        $stmt->execute([$sid]); $cur = $stmt->fetchColumn();
        if ($cur !== false) {
            $pdo->prepare("UPDATE services SET is_active=? WHERE id=?")->execute([$cur?0:1, $sid]);
            flash('success','Status layanan diperbarui.');
        }
        redirect('admin/layanan.php');
    }
    if ($action === 'delete') {
        // Cek apakah dipakai di orders
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE service_id=?");
        $stmt->execute([$sid]); $count = (int) $stmt->fetchColumn();
        if ($count > 0) {
            flash('error','Layanan sudah digunakan dalam pesanan. Nonaktifkan saja, jangan dihapus.');
        } else {
            $pdo->prepare("DELETE FROM services WHERE id=?")->execute([$sid]);
            flash('success','Layanan dihapus.');
        }
        redirect('admin/layanan.php');
    }
    flash('error','Aksi tidak dikenali.');
    redirect('admin/layanan.php');
}

$services = $pdo->query("SELECT s.*, (SELECT COUNT(*) FROM orders o WHERE o.service_id=s.id) AS used_count FROM services s ORDER BY s.id")->fetchAll();

$pageTitle = 'Data Layanan';
$activeMenu = 'layanan';
$isAdminArea = true; $layoutType = 'admin';
$menuItems = require __DIR__ . '/_menu.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-2xl font-bold text-brand-900">Data Layanan</h1>
        <p class="text-slate-500 text-sm">Kelola katalog layanan laundry.</p>
    </div>
    <a href="<?= e(BASE_URL) ?>/admin/tambah-layanan.php" class="px-4 py-2 rounded-lg bg-brand-700 text-white text-sm font-medium hover:bg-brand-800">+ Tambah Layanan</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                <tr>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Deskripsi</th>
                    <th class="text-left px-4 py-3">Harga</th>
                    <th class="text-left px-4 py-3">Satuan</th>
                    <th class="text-left px-4 py-3">Status</th>
                    <th class="text-left px-4 py-3">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($services as $s): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium"><?= e($s['name']) ?></td>
                    <td class="px-4 py-3 text-xs text-slate-600 max-w-[260px]"><?= e($s['description']) ?></td>
                    <td class="px-4 py-3"><?= format_rupiah($s['price']) ?></td>
                    <td class="px-4 py-3"><?= e($s['unit']) ?></td>
                    <td class="px-4 py-3">
                        <?php if ($s['is_active']): ?>
                            <span class="px-2 py-1 rounded-full text-xs bg-emerald-100 text-emerald-700">Aktif</span>
                        <?php else: ?>
                            <span class="px-2 py-1 rounded-full text-xs bg-slate-200 text-slate-600">Nonaktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <a href="<?= e(BASE_URL) ?>/admin/edit-layanan.php?id=<?= (int)$s['id'] ?>" class="text-brand-700 hover:underline mr-3">Edit</a>
                        <form method="POST" class="inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="toggle">
                            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                            <button type="submit" class="text-amber-600 hover:underline mr-3"><?= $s['is_active']?'Nonaktifkan':'Aktifkan' ?></button>
                        </form>
                        <?php if ((int)$s['used_count'] === 0): ?>
                        <form method="POST" class="inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                            <button type="submit" data-confirm="Yakin menghapus layanan ini?" class="text-rose-600 hover:underline">Hapus</button>
                        </form>
                        <?php else: ?>
                            <span class="text-slate-300 text-xs" title="Sudah dipakai dalam pesanan">Hapus</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
require __DIR__ . '/../includes/sidebar_end.php';
require __DIR__ . '/../includes/footer.php';
