<?php
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';
require_customer();

$uid = (int) $_SESSION['user_id'];
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Token tidak valid.'); redirect('customer/profil.php'); }
    $name  = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $errors = [];
    if ($name === '') $errors[] = 'Nama wajib diisi.';
    if (!preg_match('/^[0-9+\-\s]{8,20}$/', $phone)) $errors[] = 'Nomor HP tidak valid.';
    if ($errors) { foreach ($errors as $er) flash('error',$er); redirect('customer/profil.php'); }

    $stmt = $pdo->prepare("UPDATE users SET name=?, phone=? WHERE id=?");
    $stmt->execute([$name, $phone, $uid]);
    $_SESSION['user_name'] = $name;
    flash('success','Profil berhasil diperbarui.');
    redirect('customer/profil.php');
}

$stmt = $pdo->prepare("SELECT name, email, phone, created_at FROM users WHERE id=?");
$stmt->execute([$uid]);
$user = $stmt->fetch();

$pageTitle = 'Profil';
$activeMenu = 'profil';
$menuItems = require __DIR__ . '/_menu.php';
require __DIR__ . '/../includes/header.php';
require __DIR__ . '/../includes/sidebar.php';
?>
<h1 class="text-2xl font-bold text-brand-900 mb-6">Profil Saya</h1>

<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-brand-900 mb-4">Informasi Akun</h2>
        <dl class="text-sm space-y-3">
            <div class="flex justify-between"><dt class="text-slate-500">Email</dt><dd><?= e($user['email']) ?></dd></div>
            <div class="flex justify-between"><dt class="text-slate-500">Terdaftar</dt><dd><?= format_tanggal($user['created_at']) ?></dd></div>
        </dl>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h2 class="font-semibold text-brand-900 mb-4">Ubah Profil</h2>
        <form method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required value="<?= e($user['name']) ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nomor HP</label>
                <input type="text" name="phone" required value="<?= e($user['phone']) ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none">
            </div>
            <button type="submit" class="w-full py-2.5 rounded-lg bg-brand-700 text-white font-medium hover:bg-brand-800 transition">Simpan Perubahan</button>
        </form>
    </div>
</div>
<?php
require __DIR__ . '/../includes/sidebar_end.php';
require __DIR__ . '/../includes/footer.php';
