<?php
// Sidebar layout untuk area customer & admin.
// Variabel yang diperlukan: $pageTitle, $activeMenu, $menuItems (array of [label, href, icon])
$user = current_user();
$navItems = $menuItems ?? [];
$layoutType = $layoutType ?? 'customer'; // customer | admin
?>
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-6">
    <aside class="lg:sticky lg:top-20 self-start">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-4">
            <div class="px-2 py-3 mb-2 border-b border-slate-100">
                <div class="text-xs text-slate-500">Masuk sebagai</div>
                <div class="font-semibold text-brand-900 text-sm truncate"><?= e($user['name']) ?></div>
                <span class="inline-block mt-1 text-[10px] px-2 py-0.5 rounded-full <?= $layoutType==='admin'?'bg-brand-100 text-brand-700':'bg-emerald-100 text-emerald-700' ?> uppercase tracking-wide"><?= e($user['role']) ?></span>
            </div>
            <nav class="space-y-1 text-sm">
                <?php foreach ($navItems as $item): ?>
                    <a href="<?= e(BASE_URL) ?>/<?= e($item['href']) ?>" class="flex items-center gap-3 px-3 py-2.5 rounded-lg <?= $activeMenu===$item['key'] ? 'bg-brand-700 text-white font-medium' : 'text-slate-600 hover:bg-slate-100' ?>">
                        <span class="w-5 h-5"><?= $item['icon'] ?? '' ?></span>
                        <span><?= e($item['label']) ?></span>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>
    </aside>
    <div class="min-w-0">
