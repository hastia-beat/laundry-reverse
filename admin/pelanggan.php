<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_admin();

$pdo = db();
$customers = $pdo->query("SELECT id, name, email, phone, created_at FROM users WHERE role='customer' ORDER BY created_at DESC")->fetchAll();

$pageTitle = 'Data Pelanggan';
$activeMenu = 'pelanggan';
$isAdminArea = true; $layoutType = 'admin';
$menuItems = require __DIR__ . '/_menu.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<div class="mb-6">
    <h1 class="text-2xl font-bold text-brand-900">Data Pelanggan</h1>
    <p class="text-slate-500 text-sm">Daftar seluruh pelanggan terdaftar.</p>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                <tr>
                    <th class="text-left px-4 py-3">Nama</th>
                    <th class="text-left px-4 py-3">Email</th>
                    <th class="text-left px-4 py-3">No. HP</th>
                    <th class="text-left px-4 py-3">Terdaftar</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (!$customers): ?>
                    <tr><td colspan="4" class="px-4 py-12 text-center text-slate-500">Belum ada pelanggan.</td></tr>
                <?php else: foreach ($customers as $c): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium"><?= e($c['name']) ?></td>
                    <td class="px-4 py-3"><?= e($c['email']) ?></td>
                    <td class="px-4 py-3"><?= e($c['phone']) ?></td>
                    <td class="px-4 py-3 text-xs"><?= format_tanggal($c['created_at']) ?></td>
                </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php
require __DIR__ . '/../includes/sidebar_end.php';
require __DIR__ . '/../includes/footer.php';
