<?php declare(strict_types=1);
use Core\Auth;
$user = Auth::user();
$currentPage = 'dashboard';
$brandColor = '#059669';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="<?= BASE ?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                brand: { 50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',400:'#34d399',500:'#059669',600:'#047857',700:'#065f46' }
            }}}
        }
    </script>
</head>
<body class="h-full bg-slate-50">
<div class="min-h-full flex">
    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="p-2 bg-white rounded-lg shadow border border-slate-200">
            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 z-40 transform -translate-x-full lg:translate-x-0 transition-transform">
        <div class="flex items-center gap-2 px-6 h-16 border-b border-slate-200">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:<?= htmlspecialchars($brandColor) ?>">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <span class="font-bold text-slate-900">HR Dashboard</span>
        </div>
        <nav class="p-4 space-y-1">
            <a href="<?= BASE ?>/hr" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium <?= $currentPage === 'dashboard' ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                Overzicht
            </a>
            <a href="<?= BASE ?>/hr/medewerkers" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium <?= $currentPage === 'employees' ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                Medewerkers
            </a>
            <a href="<?= BASE ?>/hr/verlof" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium <?= $currentPage === 'leave' ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                Verlof
            </a>
            <a href="<?= BASE ?>/hr/organogram" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium <?= $currentPage === 'departments' ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                Organogram
            </a>
            <a href="<?= BASE ?>/hr/beoordelingen" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium <?= $currentPage === 'reviews' ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50' ?>">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                Beoordelingen
            </a>
        </nav>
    </aside>

    <!-- Main content -->
    <div class="flex-1 lg:ml-64">
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="lg:hidden w-10"></div>
            <h1 class="text-lg font-semibold text-slate-900">HR Overzicht</h1>
            <a href="<?= BASE ?>/" class="text-sm text-slate-500 hover:text-slate-700">Hoofdmenu</a>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500">Totaal medewerkers</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1"><?= $totalEmployees ?></p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500">Actief verlof</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1"><?= $activeLeave ?></p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500">Openstaande beoordelingen</p>
                    <p class="text-3xl font-bold text-amber-600 mt-1"><?= $pendingReviews ?></p>
                </div>
                <div class="bg-white rounded-xl border border-slate-200 p-5">
                    <p class="text-sm text-slate-500">Afdelingen</p>
                    <p class="text-3xl font-bold text-slate-900 mt-1"><?= $departments ?></p>
                </div>
            </div>

            <!-- Recent leave requests -->
            <div class="bg-white rounded-xl border border-slate-200">
                <div class="px-5 py-4 border-b border-slate-200">
                    <h2 class="font-semibold text-slate-900">Recente verlofaanvragen</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left">
                            <tr>
                                <th class="px-5 py-3 font-medium text-slate-600">Medewerker</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Type</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Van</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Tot</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Dagen</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($recentLeave)): ?>
                                <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">Geen verlofaanvragen gevonden</td></tr>
                            <?php else: foreach ($recentLeave as $req): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3 font-medium text-slate-900"><?= htmlspecialchars($req['employee_name']) ?></td>
                                    <td class="px-5 py-3 text-slate-600 capitalize"><?= htmlspecialchars($req['type']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($req['start_date']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($req['end_date']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= $req['days'] ?></td>
                                    <td class="px-5 py-3">
                                        <?php
                                        $colors = ['ingediend' => 'bg-yellow-100 text-yellow-800', 'goedgekeurd' => 'bg-emerald-100 text-emerald-800', 'afgewezen' => 'bg-red-100 text-red-800'];
                                        $c = $colors[$req['status']] ?? 'bg-slate-100 text-slate-800';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $c ?>"><?= ucfirst($req['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>
</body>
</html>
