<?php declare(strict_types=1);
use Core\Auth;
$user = Auth::user();
$currentPage = 'reviews';
$brandColor = '#059669';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beoordelingen — HR Dashboard</title>
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
    <div class="lg:hidden fixed top-4 left-4 z-50">
        <button onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')" class="p-2 bg-white rounded-lg shadow border border-slate-200">
            <svg class="w-6 h-6 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-white border-r border-slate-200 z-40 transform -translate-x-full lg:translate-x-0 transition-transform">
        <div class="flex items-center gap-2 px-6 h-16 border-b border-slate-200">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:<?= $brandColor ?>">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <span class="font-bold text-slate-900">HR Dashboard</span>
        </div>
        <nav class="p-4 space-y-1">
            <a href="/hr" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Overzicht</a>
            <a href="/hr/medewerkers" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Medewerkers</a>
            <a href="/hr/verlof" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Verlof</a>
            <a href="/hr/organogram" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Organogram</a>
            <a href="/hr/beoordelingen" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium bg-emerald-50 text-emerald-700">Beoordelingen</a>
        </nav>
    </aside>

    <div class="flex-1 lg:ml-64">
        <header class="bg-white border-b border-slate-200 h-16 flex items-center px-4 sm:px-6 lg:px-8">
            <div class="lg:hidden w-10"></div>
            <h1 class="text-lg font-semibold text-slate-900">Beoordelingen</h1>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left">
                            <tr>
                                <th class="px-5 py-3 font-medium text-slate-600">Medewerker</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Beoordelaar</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Periode</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Score</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($reviews)): ?>
                                <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Geen beoordelingen gevonden</td></tr>
                            <?php else: foreach ($reviews as $r): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3 font-medium text-slate-900"><?= htmlspecialchars($r['employee_name']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($r['reviewer_name'] ?? '—') ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($r['period'] ?? '—') ?></td>
                                    <td class="px-5 py-3">
                                        <?php if ($r['score']): ?>
                                            <span class="font-bold <?= $r['score'] >= 7 ? 'text-emerald-600' : ($r['score'] >= 5 ? 'text-amber-600' : 'text-red-600') ?>"><?= $r['score'] ?>/10</span>
                                        <?php else: ?>
                                            <span class="text-slate-400">—</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-3">
                                        <?php $rc = $r['status'] === 'afgerond' ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800'; ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $rc ?>"><?= ucfirst($r['status']) ?></span>
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
