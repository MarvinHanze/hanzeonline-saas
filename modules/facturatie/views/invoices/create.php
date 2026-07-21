<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe factuur — Facturatie — HanzeOnline</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform">
            <div class="flex items-center gap-2 px-6 py-5 border-b border-gray-200">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">H</span>
                </div>
                <span class="font-bold text-gray-900">HanzeOnline</span>
            </div>
            <nav class="px-3 py-4 space-y-1">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Facturatie</p>
                <a href="/facturatie" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                    Dashboard
                </a>
                <a href="/facturatie/klanten" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Klanten
                </a>
                <a href="/facturatie/facturen" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg bg-blue-50 text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Facturen
                </a>
                <div class="border-t border-gray-200 my-3"></div>
                <a href="/dashboard" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    Terug naar dashboard
                </a>
            </nav>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <div class="flex-1 lg:ml-64">
            <header class="bg-white border-b border-gray-200 px-4 py-3 lg:px-8">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-900">Nieuwe factuur</h1>
            </header>

            <main class="p-4 lg:p-8">
                <form method="POST" action="/facturatie/facturen" class="max-w-4xl">
                    <!-- Header info -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Klant *</label>
                                <select id="customer_id" name="customer_id" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white">
                                    <option value="">Selecteer een klant...</option>
                                    <?php foreach ($customers as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-1">Vervaldatum</label>
                                <input type="date" id="due_date" name="due_date" value="<?= date('Y-m-d', strtotime('+30 days')) ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Line items -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Regels</h2>
                            <button type="button" onclick="addRow()" class="text-sm text-blue-600 hover:text-blue-700 font-medium">+ Regel toevoegen</button>
                        </div>

                        <div id="items" class="space-y-3">
                            <div class="item-row grid grid-cols-12 gap-3 items-end">
                                <div class="col-span-12 md:col-span-5">
                                    <label class="block text-xs text-gray-400 mb-1">Omschrijving</label>
                                    <input type="text" name="item_description[]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" placeholder="Omschrijving">
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <label class="block text-xs text-gray-400 mb-1">Aantal</label>
                                    <input type="number" name="item_quantity[]" value="1" min="0" step="0.01" class="item-qty w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" oninput="recalc()">
                                </div>
                                <div class="col-span-4 md:col-span-2">
                                    <label class="block text-xs text-gray-400 mb-1">Prijs (€)</label>
                                    <input type="number" name="item_price[]" value="0.00" min="0" step="0.01" class="item-price w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none" oninput="recalc()">
                                </div>
                                <div class="col-span-3 md:col-span-2">
                                    <label class="block text-xs text-gray-400 mb-1">BTW %</label>
                                    <select name="item_btw[]" class="item-btw w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none bg-white" onchange="recalc()">
                                        <option value="21">21%</option>
                                        <option value="9">9%</option>
                                        <option value="0">0%</option>
                                    </select>
                                </div>
                                <div class="col-span-1">
                                    <button type="button" onclick="removeRow(this)" class="p-2 text-gray-400 hover:text-red-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Totals -->
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <div class="flex justify-end space-y-1">
                                <div class="w-64">
                                    <div class="flex justify-between text-sm py-1">
                                        <span class="text-gray-500">Subtotaal</span>
                                        <span id="subtotal" class="font-medium text-gray-900">€ 0,00</span>
                                    </div>
                                    <div class="flex justify-between text-sm py-1">
                                        <span class="text-gray-500">BTW</span>
                                        <span id="btw" class="font-medium text-gray-900">€ 0,00</span>
                                    </div>
                                    <div class="flex justify-between text-base py-2 border-t border-gray-200">
                                        <span class="font-semibold text-gray-900">Totaal</span>
                                        <span id="total" class="font-bold text-gray-900">€ 0,00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Notities</label>
                        <textarea id="notes" name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none resize-y" placeholder="Optionele notities voor op de factuur..."></textarea>
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium px-6 py-2.5 rounded-lg">Factuur opslaan</button>
                        <a href="/facturatie/facturen" class="bg-white border border-gray-300 text-gray-700 text-sm font-medium px-6 py-2.5 rounded-lg hover:bg-gray-50">Annuleren</a>
                    </div>
                </form>
            </main>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.toggle('hidden');
    }

    function addRow() {
        const template = document.querySelector('.item-row');
        const clone = template.cloneNode(true);
        clone.querySelectorAll('input, select').forEach(el => {
            if (el.type === 'number') el.value = el.classList.contains('item-qty') ? '1' : '0.00';
            else if (el.tagName === 'SELECT') el.value = '21';
            else el.value = '';
        });
        document.getElementById('items').appendChild(clone);
    }

    function removeRow(btn) {
        const rows = document.querySelectorAll('.item-row');
        if (rows.length > 1) btn.closest('.item-row').remove();
        recalc();
    }

    function recalc() {
        let subtotal = 0;
        let btw = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            const qty = parseFloat(row.querySelector('.item-qty').value) || 0;
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const rate = parseFloat(row.querySelector('.item-btw').value) || 0;
            const line = qty * price;
            subtotal += line;
            btw += line * (rate / 100);
        });
        const total = subtotal + btw;
        document.getElementById('subtotal').textContent = '€ ' + subtotal.toLocaleString('nl-NL', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('btw').textContent = '€ ' + btw.toLocaleString('nl-NL', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        document.getElementById('total').textContent = '€ ' + total.toLocaleString('nl-NL', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }
    </script>
</body>
</html>
