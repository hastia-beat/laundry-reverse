<?php
require_once __DIR__ . '/functions.php';
$currentUser = current_user();
$isAdminArea = $isAdminArea ?? false;
$flashMessages = get_flash();
clear_old_input();
$pageTitle = $pageTitle ?? 'LaundryKu';
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle) ?> — LaundryKu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#eff6ff', 100: '#dbeafe', 200: '#bfdbfe',
                            300: '#93c5fd', 400: '#60a5fa', 500: '#3b82f6',
                            600: '#2563eb', 700: '#1d4ed8', 800: '#1e3a8a',
                            900: '#172554'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'system-ui', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lucide-static@latest/font/lucide.css">
    <script src="<?= e(BASE_URL) ?>/assets/js/script.js" defer></script>
    <style>
        body { font-family: 'Inter', system-ui, sans-serif; }
        .fade-in { animation: fadeIn .4s ease both; }
        @keyframes fadeIn { from { opacity:0; transform:translateY(8px) } to { opacity:1; transform:none } }
        .card-hover { transition: transform .2s ease, box-shadow .2s ease; }
        .card-hover:hover { transform: translateY(-4px); box-shadow: 0 12px 28px -8px rgba(30,58,138,.18); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

<!-- Flash messages -->
<?php if (!empty($flashMessages)): ?>
<div id="flash-container" class="fixed top-4 right-4 z-[60] space-y-2 w-[90%] max-w-sm">
    <?php foreach ($flashMessages as $f): ?>
        <div class="fade-in rounded-lg shadow-lg px-4 py-3 text-sm border <?= $f['type']==='success' ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($f['type']==='error' ? 'bg-rose-50 border-rose-200 text-rose-800' : 'bg-blue-50 border-blue-200 text-blue-800') ?>">
            <?= e($f['message']) ?>
        </div>
    <?php endforeach; ?>
</div>
<script> setTimeout(()=>{ const el=document.getElementById('flash-container'); if(el) el.style.display='none'; }, 5000); </script>
<?php endif; ?>

<?php require __DIR__ . '/navbar.php'; ?>

<main class="flex-1">
