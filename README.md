# LaundryKu — Platform Layanan Laundry Berbasis Web

Website laundry berbasis **PHP Native + MySQL + Tailwind CSS (CDN)**, dirancang untuk dijalankan di **XAMPP**. Dibuat untuk tugas mata kuliah Pemrograman Web.

**Tagline:** _Laundry Praktis, Bersih, dan Terpercaya._

---

## Teknologi

- HTML5, Tailwind CSS (CDN), Vanilla JavaScript
- PHP Native (tanpa framework)
- MySQL / MariaDB via phpMyAdmin
- XAMPP (Apache + MySQL)

---

## Struktur Folder

```
laundryku/
├── index.php                # Landing page
├── layanan.php              # Katalog layanan
├── login.php / register.php / logout.php
├── config/
│   └── database.php         # Konfigurasi PDO MySQL
├── includes/
│   ├── header.php / footer.php / navbar.php
│   ├── auth.php             # Penjaga akses & login
│   ├── functions.php        # Helper umum
│   └── sidebar.php          # Layout sidebar customer/admin
├── customer/
│   ├── dashboard.php / pesan.php / riwayat.php
│   ├── detail.php / profil.php
│   └── _menu.php
├── admin/
│   ├── dashboard.php / pesanan.php / detail-pesanan.php
│   ├── layanan.php / tambah-layanan.php / edit-layanan.php
│   ├── pelanggan.php
│   └── _menu.php
├── assets/
│   ├── js/script.js
│   └── images/
└── database/
    └── laundryku.sql        # Import ke phpMyAdmin
```

---

## Cara Menjalankan di XAMPP

1. **Instal dan jalankan XAMPP**, lalu aktifkan **Apache** dan **MySQL**.
2. **Salin folder `laundryku/`** ke dalam direktori `htdocs` (mis. `C:\xampp\htdocs\laundryku`).
3. Buka **phpMyAdmin** di `http://localhost/phpmyadmin`.
4. Buat database baru bernama `laundryku`, atau langsung **Import** file `database/laundryku.sql` (file ini otomatis membuat database).
5. Sesuaikan konfigurasi database pada `config/database.php` bila user/password MySQL Anda berbeda dari default XAMPP:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'laundryku');
   define('DB_USER', 'root');     // default XAMPP
   define('DB_PASS', '');         // default XAMPP kosong
   ```
6. Buka website melalui `http://localhost/laundryku`.

> Catatan: konstanta `BASE_URL` di `includes/functions.php` diatur ke `/laundryku`. Jika folder project Anda berbeda namanya, ubah nilai ini.

---

## Akun Demo

File SQL sudah menyertakan dua akun demo. **Namun hash password di file SQL hanya placeholder** — agar login demo berfungsi, buat ulang password setelah import melalui phpMyAdmin (lihat langkah di bawah) **atau** daftar akun baru lewat halaman registrasi.

| Role     | Email                | Password      |
|----------|----------------------|---------------|
| Customer | pelanggan@demo.com   | _lihat catatan_ |
| Admin    | admin@demo.com       | _lihat catatan_ |

### Membuat / Mengatur Akun Admin

Role admin **tidak** bisa dipilih lewat form registrasi. Untuk membuat admin:

1. Daftarkan akun seperti biasa melalui halaman **Daftar** (akun otomatis menjadi `customer`).
2. Buka **phpMyAdmin** → database `laundryku` → tabel `users`.
3. Cari baris pengguna yang ingin dijadikan admin, lalu ubah kolom `role` dari `customer` menjadi `admin`.
4. Simpan. Pengguna tersebut kini dapat login sebagai admin dan mengakses `http://localhost/laundryku/admin/dashboard.php`.

### Membuat Akun Demo yang Siap Pakai

File SQL berisi data layanan, namun hash password akun demo bersifat placeholder agar aman. Untuk membuat akun demo yang langsung bisa dipakai:

1. Setelah import database, buka di browser: `http://localhost/laundryku/setup-demo.php`
2. Skrip akan membuat dua akun demo:
   - **Customer** — `pelanggan@demo.com` / `password123`
   - **Admin** — `admin@demo.com` / `password123`
3. **Hapus file `setup-demo.php`** setelah selesai demi keamanan.

---

## Fitur Utama

### Pelanggan
- Registrasi, login, logout
- Melihat katalog layanan & harga
- Membuat pesanan dengan estimasi biaya otomatis (JavaScript) dan validasi ulang di server (PHP)
- Melihat riwayat pesanan dengan filter status & pencarian nomor pesanan
- Melihat detail pesanan dengan **timeline status pengerjaan**
- Mengubah profil (nama & nomor HP)

### Admin
- Dashboard statistik (total pesanan, status, pendapatan)
- Manajemen pesanan: ubah status pengerjaan, jumlah/berat akhir, total akhir, status pembayaran
- Manajemen layanan (CRUD): tambah, edit, hapus (hanya jika belum dipakai pesanan), aktif/nonaktif
- Data pelanggan: daftar semua pelanggan terdaftar

### Alur Status Pengerjaan
1. Menunggu Konfirmasi
2. Pesanan Diterima
3. Sedang Dicuci
4. Sedang Disetrika
5. Siap Diambil
6. Selesai

Serta status **Dibatalkan** untuk pesanan yang dibatalkan admin.

---

## Keamanan yang Diterapkan

- **PDO + prepared statements** untuk semua query (tidak ada concatenasi input pengguna).
- `password_hash()` / `password_verify()` untuk autentikasi.
- `session_regenerate_id()` setelah login berhasil.
- Penjagaan role per halaman: `require_customer()` / `require_admin()`.
- Pelanggan hanya bisa melihat & mengelola pesanan miliknya (filtering via `user_id`).
- `htmlspecialchars()` pada semua output data pengguna (anti-XSS).
- **Token CSRF** pada semua form yang mengubah data.
- Metode **POST** untuk semua aksi simpan/edit/hapus.
- Tidak ada pilihan role admin pada form registrasi publik.

---

## Batasan Fitur (sesuai ketentuan tugas)

Tidak ada: payment gateway, pembayaran online, WhatsApp API, GPS tracking, sistem kurir, live chat, AI chatbot, membership/loyalty, kupon kompleks. Pembayaran bersifat manual — admin mengubah status pembayaran setelah pelanggan membayar di outlet.

---

## Catatan

- Satu pesanan = satu jenis layanan (sesuai ketentuan penyederhanaan database).
- Nama layanan & harga saat transaksi disimpan sebagai **snapshot** di tabel `orders`, sehingga perubahan harga layanan di masa depan tidak mengubah riwayat transaksi lama.
- Seluruh tampilan menggunakan bahasa Indonesia.
