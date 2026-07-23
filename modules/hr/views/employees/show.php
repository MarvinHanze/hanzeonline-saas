<?php declare(strict_types=1);
use Core\Auth;
$user = Auth::user();
$currentPage = 'employees';
$brandColor = '#059669';
$leaveStatusColors = ['ingediend' => 'bg-yellow-100 text-yellow-800', 'goedgekeurd' => 'bg-emerald-100 text-emerald-800', 'afgewezen' => 'bg-red-100 text-red-800'];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="<?= BASE ?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($employee['name']) ?> — HR Dashboard</title>
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
            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:<?= htmlspecialchars($brandColor) ?>">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
            </div>
            <span class="font-bold text-slate-900">HR Dashboard</span>
        </div>
        <nav class="p-4 space-y-1">
            <a href="<?= BASE ?>/hr" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Overzicht</a>
            <a href="<?= BASE ?>/hr/medewerkers" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium bg-emerald-50 text-emerald-700">Medewerkers</a>
            <a href="<?= BASE ?>/hr/verlof" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Verlof</a>
            <a href="<?= BASE ?>/hr/organogram" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Organogram</a>
            <a href="<?= BASE ?>/hr/beoordelingen" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Beoordelingen</a>
        </nav>
    </aside>

    <div class="flex-1 lg:ml-64">
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center">
                <div class="lg:hidden w-10"></div>
                <a href="<?= BASE ?>/hr/medewerkers" class="text-slate-400 hover:text-slate-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M15 19l-7-7 7-7"/></svg>
                </a>
                <h1 class="text-lg font-semibold text-slate-900"><?= htmlspecialchars($employee['name']) ?></h1>
            </div>
            <a href="<?= BASE ?>/hr/medewerkers/<?= $employee['id'] ?>/bewerk" class="px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Bewerken</a>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
            <!-- Profile card -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 mb-8">
                <div class="flex flex-col sm:flex-row gap-6">
                    <div class="w-20 h-20 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-emerald-700"><?= strtoupper(substr($employee['name'],0,1)) ?></span>
                    </div>
                    <div class="flex-1">
                        <h2 class="text-xl font-bold text-slate-900"><?= htmlspecialchars($employee['name']) ?></h2>
                        <p class="text-slate-500"><?= htmlspecialchars($employee['position'] ?? 'Geen functie') ?></p>
                        <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2 text-sm">
                            <p class="text-slate-600"><span class="text-slate-400">Email:</span> <?= htmlspecialchars($employee['email'] ?? '—') ?></p>
                            <p class="text-slate-600"><span class="text-slate-400">Telefoon:</span> <?= htmlspecialchars($employee['phone'] ?? '—') ?></p>
                            <p class="text-slate-600"><span class="text-slate-400">Afdeling:</span> <?= htmlspecialchars($employee['department_name'] ?? '—') ?></p>
                            <p class="text-slate-600"><span class="text-slate-400">Salaris:</span> € <?= number_format((float)($employee['salary'] ?? 0), 2, ',', '.') ?></p>
                            <p class="text-slate-600"><span class="text-slate-400">Startdatum:</span> <?= htmlspecialchars($employee['start_date'] ?? '—') ?></p>
                            <p class="text-slate-600"><span class="text-slate-400">Contract einde:</span> <?= htmlspecialchars($employee['contract_end'] ?? '—') ?></p>
                            <p class="text-slate-600"><span class="text-slate-400">Verlofsaldo:</span> <?= $employee['leave_balance_days'] ?? 25 ?> dagen</p>
                            <p class="text-slate-600">
                                <span class="text-slate-400">Status:</span>
                                <?php
                                $sc = $employee['status'] === 'actief' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600';
                                ?>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium <?= $sc ?>"><?= ucfirst($employee['status']) ?></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="border-b border-slate-200 mb-6">
                <nav class="flex gap-6" id="tabs">
                    <button onclick="showTab('overview')" class="tab-btn pb-3 text-sm font-medium border-b-2 border-emerald-500 text-emerald-600" data-tab="overview">Overzicht</button>
                    <button onclick="showTab('leave')" class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700" data-tab="leave">Verlof</button>
                    <button onclick="showTab('reviews')" class="tab-btn pb-3 text-sm font-medium border-b-2 border-transparent text-slate-500 hover:text-slate-700" data-tab="reviews">Beoordelingen</button>
                </nav>
            </div>

            <!-- Tab: Overview -->
            <div id="tab-overview" class="tab-content">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">Verlofaanvragen</p>
                        <p class="text-2xl font-bold text-slate-900 mt-1"><?= count($leaveHistory) ?></p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">Beoordelingen</p>
                        <p class="text-2xl font-bold text-slate-900 mt-1"><?= count($reviews) ?></p>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-5">
                        <p class="text-sm text-slate-500">Verlofsaldo</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1"><?= $employee['leave_balance_days'] ?? 25 ?> dagen</p>
                    </div>
                </div>
            </div>

            <!-- Tab: Leave -->
            <div id="tab-leave" class="tab-content hidden">
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 text-left">
                            <tr>
                                <th class="px-5 py-3 font-medium text-slate-600">Type</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Van</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Tot</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Dagen</th>
                                <th class="px-5 py-3 font-medium text-slate-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($leaveHistory)): ?>
                                <tr><td colspan="5" class="px-5 py-8 text-center text-slate-400">Geen verlofgeschiedenis</td></tr>
                            <?php else: foreach ($leaveHistory as $l): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3 capitalize"><?= htmlspecialchars($l['type']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($l['start_date']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($l['end_date']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= $l['days'] ?></td>
                                    <td class="px-5 py-3">
                                        <?php $c = $leaveStatusColors[$l['status']] ?? 'bg-slate-100 text-slate-800'; ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $c ?>"><?= ucfirst($l['status']) ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tab: Reviews -->
            <div id="tab-reviews" class="tab-content hidden">
                <div class="space-y-4">
                    <?php if (empty($reviews)): ?>
                        <p class="text-center text-slate-400 py-8">Geen beoordelingen gevonden</p>
                    <?php else: foreach ($reviews as $r): ?>
                        <div class="bg-white rounded-xl border border-slate-200 p-5">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-slate-900"><?= htmlspecialchars($r['period'] ?? 'Onbekende periode') ?></p>
                                    <p class="text-sm text-slate-500">Beoordelaar: <?= htmlspecialchars($r['reviewer_name'] ?? '—') ?></p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <?php if ($r['score']): ?>
                                        <span class="text-lg font-bold <?= $r['score'] >= 7 ? 'text-emerald-600' : ($r['score'] >= 5 ? 'text-amber-600' : 'text-red-600') ?>"><?= $r['score'] ?>/10</span>
                                    <?php endif; ?>
                                    <?php $rc = $r['status'] === 'afgerond' ? 'bg-emerald-100 text-emerald-800' : 'bg-yellow-100 text-yellow-800'; ?>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $rc ?>"><?= ucfirst($r['status']) ?></span>
                                </div>
                            </div>
                            <?php if ($r['strengths']): ?>
                                <p class="mt-3 text-sm text-slate-600"><span class="font-medium">Sterke punten:</span> <?= htmlspecialchars($r['strengths']) ?></p>
                            <?php endif; ?>
                            <?php if ($r['improvements']): ?>
                                <p class="mt-1 text-sm text-slate-600"><span class="font-medium">Verbeterpunten:</span> <?= htmlspecialchars($r['improvements']) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
function showTab(name) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('tab-' + name).classList.remove('hidden');
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-emerald-500','text-emerald-600');
        btn.classList.add('border-transparent','text-slate-500');
    });
    document.querySelector('[data-tab="' + name + '"]').classList.add('border-emerald-500','text-emerald-600');
    document.querySelector('[data-tab="' + name + '"]').classList.remove('border-transparent','text-slate-500');
}
</script>
</body>
</html>
