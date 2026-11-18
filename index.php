<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth.php';
$services = db()->query("SELECT * FROM services WHERE is_active = 1 ORDER BY id LIMIT 6")->fetchAll();
$pageTitle = 'Beranda';
require __DIR__ . '/includes/header.php';
?>
<!-- Hero -->
<section class="relative overflow-hidden bg-gradient-to-br from-brand-50 via-white to-brand-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 grid md:grid-cols-2 gap-10 items-center">
        <div class="fade-in">
            <span class="inline-block px-3 py-1 rounded-full bg-brand-100 text-brand-700 text-xs font-semibold mb-4">Laundry Online Terpercaya</span>
            <h1 class="text-3xl md:text-5xl font-bold text-brand-900 leading-tight mb-4">Pakaian Bersih Tanpa Ribet!</h1>
            <p class="text-slate-600 text-lg leading-relaxed mb-6">Nikmati layanan laundry praktis dengan proses pemesanan mudah, harga transparan, dan pelacakan status cucian secara online.</p>
            <div class="flex flex-wrap gap-3">
                <a href="<?= e(BASE_URL) ?>/customer/pesan.php" class="px-6 py-3 rounded-xl bg-brand-700 text-white font-semibold hover:bg-brand-800 transition shadow-lg shadow-brand-200">Pesan Sekarang</a>
                <a href="<?= e(BASE_URL) ?>/layanan.php" class="px-6 py-3 rounded-xl border border-brand-200 text-brand-700 font-semibold hover:bg-brand-50 transition">Lihat Layanan</a>
            </div>
        </div>
        <div class="fade-in">
            <img src="https://images.pexels.com/photos/4652348/pexels-photo-4652348.jpeg?auto=compress&cs=tinysrgb&w=900" alt="Laundry" class="rounded-2xl shadow-2xl w-full object-cover h-[360px]">
        </div>
    </div>
</section>

<!-- Layanan Unggulan -->
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="text-center mb-10">
        <h2 class="text-2xl md:text-3xl font-bold text-brand-900">Layanan Unggulan Kami</h2>
        <p class="text-slate-600 mt-2">Pilih layanan laundry sesuai kebutuhan Anda.</p>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($services as $s): ?>
        <div class="card-hover bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
            <div class="w-12 h-12 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 9m-6 0a3 3 0 006 0"/></svg>
            </div>
            <h3 class="font-semibold text-lg text-brand-900 mb-1"><?= e($s['name']) ?></h3>
            <p class="text-sm text-slate-600 mb-4 min-h-[40px]"><?= e($s['description']) ?></p>
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-xl font-bold text-brand-700"><?= format_rupiah($s['price']) ?></div>
                    <div class="text-xs text-slate-500">per <?= e($s['unit']) ?></div>
                </div>
                <a href="<?= is_logged_in() ? e(BASE_URL).'/customer/pesan.php?service='.(int)$s['id'] : e(BASE_URL).'/login.php' ?>" class="px-4 py-2 rounded-lg bg-brand-600 text-white text-sm font-medium hover:bg-brand-700 transition">Pesan</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Cara Kerja -->
<section class="bg-brand-50/60 py-16">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl md:text-3xl font-bold text-brand-900 text-center mb-10">Cara Kerja</h2>
        <div class="grid md:grid-cols-3 gap-6">
            <?php
            $steps = [
                ['1','Pilih Layanan','Tentukan jenis layanan laundry yang sesuai kebutuhan Anda.'],
                ['2','Buat Pesanan','Pesan melalui website dengan estimasi berat dan biaya transparan.'],
                ['3','Antar & Pantau','Antar cucian ke outlet dan pantau status pengerjaan online.'],
            ];
            foreach ($steps as [$n,$t,$d]): ?>
            <div class="bg-white rounded-2xl p-6 shadow-sm text-center">
                <div class="w-12 h-12 mx-auto rounded-full bg-brand-700 text-white font-bold flex items-center justify-center mb-3"><?= $n ?></div>
                <h3 class="font-semibold text-brand-900 mb-1"><?= e($t) ?></h3>
                <p class="text-sm text-slate-600"><?= e($d) ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Keunggulan + Tentang -->
<section id="tentang" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
    <div class="grid md:grid-cols-2 gap-10 items-center">
        <img src="https://images.pexels.com/photos/5255095/pexels-photo-5255095.jpeg?auto=compress&cs=tinysrgb&w=800" alt="Tentang" class="rounded-2xl shadow-lg w-full h-[320px] object-cover">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-brand-900 mb-4">Mengapa LaundryKu?</h2>
            <ul class="space-y-4">
                <?php foreach (['Pemesanan mudah dan cepat','Harga transparan tanpa biaya tersembunyi','Status pengerjaan terpantau secara online','Layanan sesuai kebutuhan'] as $item): ?>
                <li class="flex items-start gap-3">
                    <span class="mt-1 w-6 h-6 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                    </span>
                    <span class="text-slate-700"><?= e($item) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
