<?php declare(strict_types=1);
use Core\Auth;
use Core\Tenant;
use Core\Database;

$user = Auth::user();
$modules = Tenant::activeModules();
$tenantName = Tenant::name();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="/saas-platform/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard — <?= htmlspecialchars($tenantName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                brand: { 50: '#eff6ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' }
            }}}
        }
    </script>
</head>
<body class="h-full bg-slate-50">
<div class="min-h-full">
    <!-- Nav -->
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center gap-8">
                    <a href="/" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="3"/>
                                <path d="M9 12h6M12 9v6"/>
                            </svg>
                        </div>
                        <span class="font-bold text-slate-900 hidden sm:block"><?= htmlspecialchars($tenantName) ?></span>
                    </a>
                    <div class="hidden md:flex items-center gap-1">
                        <a href="/" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-900 bg-slate-100">Dashboard</a>
                        <?php foreach ($modules as $key => $module): ?>
                            <a href="/<?= $key ?>" class="px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50">
                                <?= htmlspecialchars($module['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-medium text-slate-700"><?= htmlspecialchars($user['name']) ?></p>
                        <p class="text-xs text-slate-500"><?= htmlspecialchars($user['role']) ?></p>
                    </div>
                    <a href="/logout" class="text-sm text-slate-500 hover:text-slate-700">Uitloggen</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-6">Welkom terug, <?= htmlspecialchars($user['name']) ?></h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($modules as $key => $module): ?>
                <a href="/<?= $key ?>" class="bg-white rounded-xl border border-slate-200 p-6 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 rounded-xl bg-brand-50 flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <?php if ($key === 'facturatie'): ?>
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="1"/>
                                <path d="M9 14h.01M14 14h.01M9 17h5"/>
                            <?php elseif ($key === 'hr'): ?>
                                <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <path d="M23 21v-2a4 4 0 00-3-3.87"/>
                                <path d="M16 3.13a4 4 0 010 7.75"/>
                            <?php elseif ($key === 'contract'): ?>
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                <polyline points="14 2 14 8 20 8"/>
                                <line x1="16" y1="13" x2="8" y2="13"/>
                                <line x1="16" y1="17" x2="8" y2="17"/>
                            <?php endif; ?>
                        </svg>
                    </div>
                    <h3 class="font-semibold text-slate-900 mb-1"><?= htmlspecialchars($module['name']) ?></h3>
                    <p class="text-sm text-slate-500"><?= htmlspecialchars($module['description']) ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </main>
</div>
</body>
</html>
