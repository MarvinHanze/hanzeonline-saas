<?php declare(strict_types=1);
use Core\Auth;
use Core\Tenant;

$user = Auth::user();
$tenantName = Tenant::name();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="/saas-platform/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuw Sjabloon — <?= htmlspecialchars($tenantName) ?></title>
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
            <a href="/" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-lg bg-brand-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="3"/><path d="M9 12h6M12 9v6"/></svg>
                </div>
                <span class="font-bold text-slate-900"><?= htmlspecialchars($tenantName) ?></span>
            </a>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1">
            <a href="/contract" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                Dashboard
            </a>
            <a href="/contract/contracts" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Contracten
            </a>
            <a href="/contract/sjablonen" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium bg-brand-50 text-brand-700">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                Sjablonen
            </a>
        </nav>
        <div class="px-3 py-4 border-t border-slate-200">
            <a href="/contract/sjablonen" class="flex items-center gap-3 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 hover:bg-slate-50">
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M15 18l-6-6 6-6"/></svg>
                Terug naar sjablonen
            </a>
        </div>
    </aside>

    <!-- Mobile menu button -->
    <div class="lg:hidden fixed top-0 left-0 right-0 z-40 bg-white border-b border-slate-200 px-4 h-16 flex items-center">
        <button onclick="document.getElementById('sidebar').classList.toggle('hidden')" class="p-2 rounded-lg text-slate-600 hover:bg-slate-100">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <span class="ml-3 font-bold text-slate-900">Nieuw Sjabloon</span>
    </div>

    <!-- Main -->
    <main class="flex-1 lg:ml-64 pt-16 lg:pt-0">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-2xl font-bold text-slate-900 mb-6">Nieuw sjabloon</h1>

            <form method="POST" action="/contract/sjablonen" class="space-y-6">
                <div class="bg-white rounded-xl border border-slate-200 p-6 space-y-5">
                    <!-- Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">Naam *</label>
                        <input type="text" name="name" id="name" required
                               class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                               placeholder="Bv. Arbeidsovereenkomst standaard">
                    </div>

                    <!-- HTML content -->
                    <div>
                        <label for="content_html" class="block text-sm font-medium text-slate-700 mb-1">HTML Inhoud *</label>
                        <textarea name="content_html" id="content_html" rows="16" required
                                  class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm font-mono focus:ring-2 focus:ring-brand-500 focus:border-brand-500"
                                  placeholder="<h2>Arbeidsovereenkomst</h2>&#10;<p>Between {{klant_naam}} en {{bedrijf_naam}}</p>"></textarea>
                    </div>

                    <!-- Variables help -->
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-amber-800 mb-2">Beschikbare variabelen</h3>
                        <p class="text-sm text-amber-700 mb-2">Gebruik deze placeholders in je sjabloon, ze worden automatisch vervangen:</p>
                        <div class="grid grid-cols-2 gap-2 text-sm text-amber-800">
                            <code class="bg-amber-100 px-2 py-1 rounded">{{klant_naam}}</code>
                            <code class="bg-amber-100 px-2 py-1 rounded">{{medewerker_naam}}</code>
                            <code class="bg-amber-100 px-2 py-1 rounded">{{datum}}</code>
                            <code class="bg-amber-100 px-2 py-1 rounded">{{start_datum}}</code>
                            <code class="bg-amber-100 px-2 py-1 rounded">{{eind_datum}}</code>
                            <code class="bg-amber-100 px-2 py-1 rounded">{{bedrijf_naam}}</code>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600 transition-colors">
                        Sjabloon opslaan
                    </button>
                    <a href="/contract/sjablonen" class="px-5 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
                        Annuleren
                    </a>
                </div>
            </form>
        </div>
    </main>
</div>
</body>
</html>
