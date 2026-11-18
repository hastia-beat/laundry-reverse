<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    if (is_admin()) redirect('admin/dashboard.php');
    else redirect('customer/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Token tidak valid.'); redirect('register.php'); }
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $errors = [];
    set_old_input(compact('name','email','phone'));

    if ($name === '') $errors[] = 'Nama lengkap wajib diisi.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
    if (!preg_match('/^[0-9+\-\s]{8,20}$/', $phone)) $errors[] = 'Nomor HP tidak valid.';
    if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';
    if ($password !== $confirm) $errors[] = 'Konfirmasi password tidak sesuai.';

    if (!$errors) {
        $stmt = db()->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) { $errors[] = 'Email sudah terdaftar.'; }
    }
    if ($errors) {
        foreach ($errors as $err) flash('error', $err);
        redirect('register.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = db()->prepare("INSERT INTO users (name,email,phone,password,role) VALUES (?,?,?,?, 'customer')");
    $stmt->execute([$name,$email,$phone,$hash]);
    flash('success','Pendaftaran berhasil. Silakan masuk.');
    redirect('login.php');
}

$pageTitle = 'Daftar';
require __DIR__ . '/includes/header.php';
?>
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-brand-50 to-white">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-100 p-8 fade-in">
        <div class="text-center mb-6">
            <span class="inline-flex w-12 h-12 rounded-xl bg-brand-700 text-white items-center justify-center font-bold text-xl mb-3">L</span>
            <h1 class="text-2xl font-bold text-brand-900">Daftar Akun Baru</h1>
            <p class="text-sm text-slate-500 mt-1">Buat akun untuk mulai memesan laundry.</p>
        </div>
        <form method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nama Lengkap</label>
                <input type="text" name="name" required value="<?= old_input('name') ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none" placeholder="Nama Anda">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required value="<?= old_input('email') ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none" placeholder="nama@email.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Nomor HP</label>
                <input type="text" name="phone" required value="<?= old_input('phone') ?>" class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none" placeholder="0812xxxxxxx">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none" placeholder="Minimal 6 karakter">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Konfirmasi Password</label>
                <input type="password" name="confirm" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none" placeholder="Ulangi password">
            </div>
            <button type="submit" class="w-full py-3 rounded-lg bg-brand-700 text-white font-semibold hover:bg-brand-800 transition">Daftar</button>
        </form>
        <p class="text-sm text-center text-slate-600 mt-5">Sudah punya akun? <a href="<?= e(BASE_URL) ?>/login.php" class="text-brand-700 font-semibold hover:underline">Masuk di sini</a></p>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
