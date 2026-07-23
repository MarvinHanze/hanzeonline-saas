<?php declare(strict_types=1);
use Core\Auth;
use Core\Tenant;

$user = Auth::user();
$tenantName = Tenant::name();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="<?= BASE ?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuw Contract — <?= htmlspecialchars($tenantName) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                brand: { 50: '#fffbeb', 100: '#fef3c7', 200: '#fde68a', 300: '#fcd34d', 400: '#fbbf24', 500: '#f59e0b', 600: '#d97706', 700: '#b45309' }
            }}}
        }
    </script>
</head>
<body class="h-full bg-slate-50">
<div class="min-h-full flex">
    <!-- Sidebar -->
    <aside id="sidebar" class="hidden lg:flex lg:flex-col w-64 bg-white border-r border-slate-200 fixed inset-y-0 left-0 z-30">
        <div class="flex items-center gap-2 px-6 h-16 border-b border-slate-200">
            <a href="<?= BASE ?>/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 12h6M12 9v6"/></svg>
                </div>
                <span class="font-bold text-slate-900"><?= htmlspecialchars($tenantName) ?></span>
            </a>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1">
            <a href="<?= BASE ?>/contract" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Dashboard
            </a>
            <a href="<?= BASE ?>/contract/contracts" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium bg-brand-50 text-brand-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Contracten
            </a>
            <a href="<?= BASE ?>/contract/sjablonen" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Sjablonen
            </a>
        </nav>
        <div class="px-3 py-4 border-t border-slate-200">
            <a href="<?= BASE ?>/contract/contracts" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M15 18l-6-6 6-6"/></svg>
                Terug naar contracten
            </a>
        </div>
    </aside>

    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white border-b border-slate-200 px-4 h-16 flex items-center">
        <button onclick="document.getElementById('sidebar').classList.toggle('hidden')" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <span class="ml-3 font-bold text-slate-900">Nieuw Contract</span>
    </div>

    <!-- Main -->
    <main class="flex-1 lg:ml-64 pt-16 lg:pt-0">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold text-slate-900 mb-6">Nieuw contract</h1>

            <form method="POST" action="<?= BASE ?>/contract/contracts" class="space-y-6"><?= \Core\Csrf::field() ?>
                <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
                    <!-- Template -->
                    <div>
                        <label for="template_id" class="block text-sm font-medium text-slate-700 mb-1">Sjabloon</label>
                        <select name="template_id" id="template_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                            <option value="0">— Geen sjabloon —</option>
                            <?php foreach ($templates as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-slate-700 mb-1">Titel *</label>
                        <input type="text" name="title" id="title" required
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                               placeholder="Bv. Arbeidsovereenkomst">
                    </div>

                    <!-- Customer / Employee -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-slate-700 mb-1">Klant</label>
                            <select name="customer_id" id="customer_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                                <option value="0">— Selecteer klant —</option>
                                <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <label for="employee_id" class="block text-sm font-medium text-slate-700 mb-1">Medewerker</label>
                            <select name="employee_id" id="employee_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                                <option value="0">— Selecteer medewerker —</option>
                                <?php foreach ($employees as $e): ?>
                                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Dates -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-slate-700 mb-1">Startdatum</label>
                            <input type="date" name="start_date" id="start_date"
                                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-slate-700 mb-1">Einddatum</label>
                            <input type="date" name="end_date" id="end_date"
                                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-700 mb-1">Notities</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                                  placeholder="Optionele notities over dit contract..."></textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600 transition-colors">
                        Contract aanmaken
                    </button>
                    <a href="<?= BASE ?>/contract/contracts" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
                        Annuleren
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
