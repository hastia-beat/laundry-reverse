</main>

<footer class="bg-brand-900 text-slate-300 mt-12">
    <div id="kontak" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
        <div class="md:col-span-2">
            <div class="flex items-center gap-2 mb-3">
                <span class="w-9 h-9 rounded-xl bg-white/10 flex items-center justify-center text-white font-bold text-lg">L</span>
                <span class="font-bold text-lg text-white">LaundryKu</span>
            </div>
            <p class="text-sm leading-relaxed max-w-md">Laundry Praktis, Bersih, dan Terpercaya. Platform laundry online dengan pemesanan mudah, harga transparan, dan pelacakan status cucian secara real-time.</p>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-3 text-sm">Navigasi</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="<?= e(BASE_URL) ?>/" class="hover:text-white transition">Beranda</a></li>
                <li><a href="<?= e(BASE_URL) ?>/layanan.php" class="hover:text-white transition">Layanan</a></li>
                <li><a href="<?= e(BASE_URL) ?>/#tentang" class="hover:text-white transition">Tentang Kami</a></li>
            </ul>
        </div>
        <div>
            <h4 class="text-white font-semibold mb-3 text-sm">Kontak Outlet</h4>
            <ul class="space-y-2 text-sm">
                <li>Jl. Bersih Sejahtera No. 17, Bandung</li>
                <li>(022) 123-4567</li>
                <li>halo@laundryku.id</li>
                <li>Senin–Sabtu, 08.00–20.00</li>
            </ul>
        </div>
    </div>
    <div class="border-t border-white/10 py-4 text-center text-xs text-slate-400">
        &copy; <?= date('Y') ?> LaundryKu. Dibuat untuk tugas Pemrograman Web.
    </div>
</footer>

</body>
</html>
