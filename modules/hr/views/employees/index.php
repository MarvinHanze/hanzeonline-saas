<?php declare(strict_types=1);
use Core\Auth;
$user = Auth::user();
$currentPage = 'employees';
$brandColor = '#059669';
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="/saas-platform/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medewerkers — HR Dashboard</title>
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
            <a href="/hr" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                Overzicht
            </a>
            <a href="/hr/medewerkers" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium bg-emerald-50 text-emerald-700">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                Medewerkers
            </a>
            <a href="/hr/verlof" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                Verlof
            </a>
            <a href="/hr/organogram" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
                Organogram
            </a>
            <a href="/hr/beoordelingen" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.562.562 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                Beoordelingen
            </a>
        </nav>
    </aside>

    <div class="flex-1 lg:ml-64">
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="lg:hidden w-10"></div>
            <h1 class="text-lg font-semibold text-slate-900">Medewerkers</h1>
            <a href="/hr/medewerkers/nieuw" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nieuwe medewerker
            </a>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
            <!-- Filters -->
            <form method="GET" class="flex flex-col sm:flex-row gap-3 mb-6">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Zoek op naam of email..." class="flex-1 px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                <select name="department" class="px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">Alle afdelingen</option>
                    <?php foreach ($departments as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $departmentId == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="px-4 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200">Zoeken</button>
            </form>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left">
                            <tr>
                                <th class="px-5 py-3 font-medium text-slate-600">Naam</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Afdeling</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Functie</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Status</th>
                                <th class="px-5 py-3 font-medium text-slate-600">In dienst sinds</th>
                                <th class="px-5 py-3 font-medium text-slate-600"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($employees)): ?>
                                <tr><td colspan="6" class="px-5 py-8 text-center text-slate-400">Geen medewerkers gevonden</td></tr>
                            <?php else: foreach ($employees as $emp): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <a href="/hr/medewerkers/<?= $emp['id'] ?>" class="font-medium text-slate-900 hover:text-emerald-600"><?= htmlspecialchars($emp['name']) ?></a>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($emp['department_name'] ?? '—') ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($emp['position'] ?? '—') ?></td>
                                    <td class="px-5 py-3">
                                        <?php
                                        $sc = $emp['status'] === 'actief' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $sc ?>"><?= ucfirst($emp['status']) ?></span>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($emp['start_date'] ?? '—') ?></td>
                                    <td class="px-5 py-3 text-right">
                                        <a href="/hr/medewerkers/<?= $emp['id'] ?>/bewerk" class="text-emerald-600 hover:text-emerald-800 text-sm font-medium">Bewerken</a>
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
