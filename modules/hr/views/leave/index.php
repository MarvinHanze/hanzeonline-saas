<?php declare(strict_types=1);
use Core\Auth;
$user = Auth::user();
$currentPage = 'leave';
$brandColor = '#059669';
$leaveStatusColors = ['ingediend' => 'bg-yellow-100 text-yellow-800', 'goedgekeurd' => 'bg-emerald-100 text-emerald-800', 'afgewezen' => 'bg-red-100 text-red-800'];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="/saas-platform/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verlof — HR Dashboard</title>
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
            <a href="/hr/verlof" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium bg-emerald-50 text-emerald-700">Verlof</a>
            <a href="/hr/organogram" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Organogram</a>
            <a href="/hr/beoordelingen" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">Beoordelingen</a>
        </nav>
    </aside>

    <div class="flex-1 lg:ml-64">
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="lg:hidden w-10"></div>
            <h1 class="text-lg font-semibold text-slate-900">Verlof</h1>
            <button onclick="document.getElementById('modal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Nieuwe aanvraag
            </button>
        </header>

        <main class="p-4 sm:p-6 lg:p-8">
            <!-- Status filter -->
            <div class="flex gap-2 mb-6 overflow-x-auto">
                <a href="/hr/verlof" class="px-4 py-2 rounded-lg text-sm font-medium <?= $status === '' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">Alle</a>
                <a href="/hr/verlof?status=ingediend" class="px-4 py-2 rounded-lg text-sm font-medium <?= $status === 'ingediend' ? 'bg-yellow-500 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">Ingediend</a>
                <a href="/hr/verlof?status=goedgekeurd" class="px-4 py-2 rounded-lg text-sm font-medium <?= $status === 'goedgekeurd' ? 'bg-emerald-600 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">Goedgekeurd</a>
                <a href="/hr/verlof?status=afgewezen" class="px-4 py-2 rounded-lg text-sm font-medium <?= $status === 'afgewezen' ? 'bg-red-500 text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' ?>">Afgewezen</a>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
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
                                <?php if ($isAdmin): ?>
                                    <th class="px-5 py-3 font-medium text-slate-600 text-right">Acties</th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <?php if (empty($requests)): ?>
                                <tr><td colspan="<?= $isAdmin ? 7 : 6 ?>" class="px-5 py-8 text-center text-slate-400">Geen verlofaanvragen gevonden</td></tr>
                            <?php else: foreach ($requests as $req): ?>
                                <tr class="hover:bg-slate-50">
                                    <td class="px-5 py-3 font-medium text-slate-900"><?= htmlspecialchars($req['employee_name']) ?></td>
                                    <td class="px-5 py-3 text-slate-600 capitalize"><?= htmlspecialchars($req['type']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($req['start_date']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= htmlspecialchars($req['end_date']) ?></td>
                                    <td class="px-5 py-3 text-slate-600"><?= $req['days'] ?></td>
                                    <td class="px-5 py-3">
                                        <?php $c = $leaveStatusColors[$req['status']] ?? ''; ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $c ?>"><?= ucfirst($req['status']) ?></span>
                                    </td>
                                    <?php if ($isAdmin): ?>
                                        <td class="px-5 py-3 text-right">
                                            <?php if ($req['status'] === 'ingediend'): ?>
                                                <div class="flex gap-2 justify-end">
                                                    <form method="POST" action="/hr/verlof/<?= $req['id'] ?>/goedkeuren">
                                                        <button type="submit" class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-medium rounded-lg hover:bg-emerald-200">Goedkeuren</button>
                                                    </form>
                                                    <form method="POST" action="/hr/verlof/<?= $req['id'] ?>/afwijzen" class="flex gap-1">
                                                        <input type="text" name="reason" placeholder="Reden..." class="px-2 py-1 border border-slate-300 rounded text-xs w-32">
                                                        <button type="submit" class="px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-lg hover:bg-red-200">Afwijzen</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Modal: new leave request -->
<div id="modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-lg mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold text-slate-900">Nieuwe verlofaanvraag</h2>
            <button onclick="document.getElementById('modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="/hr/verlof" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Medewerker *</label>
                <select name="employee_id" required class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="">Selecteer medewerker</option>
                    <?php foreach ($employees as $e): ?>
                        <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?> (<?= $e['leave_balance_days'] ?> dagen saldo)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Type *</label>
                <select name="type" required class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                    <option value="vakantie">Vakantie</option>
                    <option value="ziek">Ziek</option>
                    <option value="persoonlijk">Persoonlijk</option>
                    <option value="ouderschapsverlof">Ouderschapsverlof</option>
                </select>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Van *</label>
                    <input type="date" name="start_date" required class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tot *</label>
                    <input type="date" name="end_date" required class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Dagen *</label>
                    <input type="number" name="days" min="1" required class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Notities</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="px-5 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700">Indienen</button>
                <button type="button" onclick="document.getElementById('modal').classList.add('hidden')" class="px-5 py-2 bg-slate-100 text-slate-700 text-sm font-medium rounded-lg hover:bg-slate-200">Annuleren</button>
            </div>
        </form>
    </div>
</div>
</body>
</html>
