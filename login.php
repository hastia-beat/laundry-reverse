<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';

if (is_logged_in()) {
    if (is_admin()) redirect('admin/dashboard.php');
    else redirect('customer/dashboard.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) { flash('error','Token tidak valid.'); redirect('login.php'); }
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email === '' || $password === '') { flash('error','Email dan password wajib diisi.'); redirect('login.php'); }
    if (attempt_login($email, $password)) {
        flash('success','Selamat datang kembali!');
        if (is_admin()) redirect('admin/dashboard.php');
        redirect('customer/dashboard.php');
    }
    flash('error','Email atau password salah.'); redirect('login.php');
}

$pageTitle = 'Login';
require __DIR__ . '/includes/header.php';
?>
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12 bg-gradient-to-br from-brand-50 to-white">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl border border-slate-100 p-8 fade-in">
        <div class="text-center mb-6">
            <span class="inline-flex w-12 h-12 rounded-xl bg-brand-700 text-white items-center justify-center font-bold text-xl mb-3">L</span>
            <h1 class="text-2xl font-bold text-brand-900">Masuk ke LaundryKu</h1>
            <p class="text-sm text-slate-500 mt-1">Silakan masuk untuk memesan laundry.</p>
        </div>
        <form method="POST" class="space-y-4">
            <?= csrf_field() ?>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none" placeholder="nama@email.com">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-lg border border-slate-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-100 outline-none" placeholder="••••••••">
            </div>
            <button type="submit" class="w-full py-3 rounded-lg bg-brand-700 text-white font-semibold hover:bg-brand-800 transition">Masuk</button>
        </form>
        <p class="text-sm text-center text-slate-600 mt-5">Belum punya akun? <a href="<?= e(BASE_URL) ?>/register.php" class="text-brand-700 font-semibold hover:underline">Daftar di sini</a></p>
    </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
