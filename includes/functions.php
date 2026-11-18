<?php
// ============================================
// Fungsi Pembantu Umum
// ============================================

require_once __DIR__ . '/../config/database.php';

// Base URL disesuaikan dengan folder project di htdocs
define('BASE_URL', '/laundryku');

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function format_rupiah($amount): string
{
    return 'Rp' . number_format((float) $amount, 0, ',', '.');
}

function format_tanggal(string $datetime): string
{
    $ts = strtotime($datetime);
    return date('d M Y, H:i', $ts);
}

function redirect(string $path): void
{
    header('Location: ' . $path);
    exit;
}

function flash(string $type, string $message): void
{
    if (!isset($_SESSION)) session_start();
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function get_flash(): array
{
    if (!isset($_SESSION)) session_start();
    $flashes = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $flashes;
}

function generate_order_number(PDO $pdo): string
{
    $prefix = 'INV' . date('ymd');
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE order_number LIKE ?");
    $stmt->execute([$prefix . '%']);
    $count = (int) $stmt->fetchColumn();
    return $prefix . str_pad($count + 1, 4, '0', STR_PAD_LEFT);
}

function csrf_token(): string
{
    if (!isset($_SESSION)) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(): bool
{
    if (!isset($_SESSION)) session_start();
    $token = $_POST['csrf_token'] ?? '';
    return !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function old_input(string $key, string $default = ''): string
{
    return e($_SESSION['old_input'][$key] ?? $default);
}

function set_old_input(array $data): void
{
    if (!isset($_SESSION)) session_start();
    $_SESSION['old_input'] = $data;
}

function clear_old_input(): void
{
    if (!isset($_SESSION)) session_start();
    unset($_SESSION['old_input']);
}

function is_logged_in(): bool
{
    if (!isset($_SESSION)) session_start();
    return isset($_SESSION['user_id']);
}

function is_admin(): bool
{
    if (!isset($_SESSION)) session_start();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function current_user(): ?array
{
    if (!is_logged_in()) return null;
    return [
        'id'    => $_SESSION['user_id'],
        'name'  => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'role'  => $_SESSION['role'] ?? 'customer',
    ];
}

function status_badge_class(string $status): string
{
    $map = [
        'Menunggu Konfirmasi' => 'bg-amber-100 text-amber-800',
        'Pesanan Diterima'    => 'bg-blue-100 text-blue-800',
        'Sedang Dicuci'       => 'bg-cyan-100 text-cyan-800',
        'Sedang Disetrika'    => 'bg-indigo-100 text-indigo-800',
        'Siap Diambil'        => 'bg-teal-100 text-teal-800',
        'Selesai'             => 'bg-emerald-100 text-emerald-800',
        'Dibatalkan'          => 'bg-rose-100 text-rose-800',
    ];
    return $map[$status] ?? 'bg-slate-100 text-slate-800';
}

function payment_badge_class(string $status): string
{
    $map = [
        'Belum Dibayar' => 'bg-rose-100 text-rose-800',
        'Sudah Dibayar' => 'bg-emerald-100 text-emerald-800',
    ];
    return $map[$status] ?? 'bg-slate-100 text-slate-800';
}

const STATUS_PENGERJAAN = [
    'Menunggu Konfirmasi',
    'Pesanan Diterima',
    'Sedang Dicuci',
    'Sedang Disetrika',
    'Siap Diambil',
    'Selesai',
    'Dibatalkan',
];

const STATUS_TIMELINE = [
    'Menunggu Konfirmasi',
    'Pesanan Diterima',
    'Sedang Dicuci',
    'Sedang Disetrika',
    'Siap Diambil',
    'Selesai',
];

const PAYMENT_STATUSES = ['Belum Dibayar', 'Sudah Dibayar'];
