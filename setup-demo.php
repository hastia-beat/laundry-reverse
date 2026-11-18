<?php
// Skrip satu kali: jalankan di browser http://localhost/laundryku/setup-demo.php
// untuk membuat akun demo (customer & admin) dengan password yang valid.
// HAPUS file ini setelah selesai.

require_once __DIR__ . '/config/database.php';

$hash = password_hash('password123', PASSWORD_DEFAULT);

$pdo = db();

// Hapus dulu jika sudah ada
$pdo->exec("DELETE FROM users WHERE email IN ('pelanggan@demo.com','admin@demo.com')");

$stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?,?,?,?,?)");
$stmt->execute(['Pelanggan Demo', 'pelanggan@demo.com', '081234567890', $hash, 'customer']);
$stmt->execute(['Admin Demo', 'admin@demo.com', '081234567891', $hash, 'admin']);

echo "<h2>Akun demo berhasil dibuat.</h2>";
echo "<ul>
  <li>Customer — pelanggan@demo.com / password123</li>
  <li>Admin — admin@demo.com / password123</li>
</ul>";
echo "<p><strong>Hapus file setup-demo.php sekarang.</strong></p>";
echo "<p><a href='index.php'>Buka halaman utama →</a></p>";
