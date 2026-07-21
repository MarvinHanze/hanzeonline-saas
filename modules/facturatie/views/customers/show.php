<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="<?= BASE ?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($customer['name']) ?> — Klant — HanzeOnline</title>
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
                <a href="<?= BASE ?>/facturatie" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                    Dashboard
                </a>
                <a href="<?= BASE ?>/facturatie/klanten" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg bg-blue-50 text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Klanten
                </a>
                <a href="<?= BASE ?>/facturatie/facturen" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Facturen
                </a>
                <div class="border-t border-gray-200 my-3"></div>
                <a href="<?= BASE ?>/"> class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    Terug naar dashboard
                </a>
            </nav>
        </aside>

        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <div class="flex-1 lg:ml-64">
            <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between lg:px-8">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($customer['name']) ?></h1>
                </div>
                <div class="flex gap-2">
                    <a href="<?= BASE ?>/facturatie/klanten/<?= $customer['id'] ?>/bewerk" class="text-sm text-gray-600 hover:text-gray-900 border border-gray-300 px-4 py-2 rounded-lg font-medium">Bewerken</a>
                </div>
            </header>

            <main class="p-4 lg:p-8">
                <!-- Customer info -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 mb-8">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-4">Klantgegevens</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Naam</p>
                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($customer['name']) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Email</p>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($customer['email'] ?? '—') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Telefoon</p>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($customer['phone'] ?? '—') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Adres</p>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($customer['address'] ?? '—') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Postcode / Plaats</p>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars(($customer['postal'] ?? '') . ' ' . ($customer['city'] ?? '')) ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Land</p>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($customer['country'] ?? '—') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">BTW-nummer</p>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($customer['btw_nr'] ?? '—') ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">KVK-nummer</p>
                            <p class="text-sm text-gray-900"><?= htmlspecialchars($customer['kvk_nr'] ?? '—') ?></p>
                        </div>
                        <?php if (!empty($customer['notes'])): ?>
                        <div class="md:col-span-3">
                            <p class="text-xs text-gray-400 mb-1">Notities</p>
                            <p class="text-sm text-gray-900"><?= nl2br(htmlspecialchars($customer['notes'])) ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Invoices -->
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider">Facturen</h2>
                        <a href="<?= BASE ?>/facturatie/facturen/nieuw" class="text-sm text-blue-600 hover:text-blue-700 font-medium">+ Nieuwe factuur</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr>
                                    <th class="px-6 py-3 font-medium text-gray-500">Nummer</th>
                                    <th class="px-6 py-3 font-medium text-gray-500">Bedrag</th>
                                    <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                                    <th class="px-6 py-3 font-medium text-gray-500">Vervaldatum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (empty($invoices)): ?>
                                <tr><td colspan="4" class="px-6 py-8 text-center text-gray-400">Nog geen facturen voor deze klant</td></tr>
                                <?php else: ?>
                                <?php foreach ($invoices as $inv): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <a href="<?= BASE ?>/facturatie/facturen/<?= $inv['id'] ?>" class="font-medium text-blue-600 hover:text-blue-700"><?= htmlspecialchars($inv['number']) ?></a>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">€ <?= number_format((float)$inv['total'], 2, ',', '.') ?></td>
                                    <td class="px-6 py-4">
                                        <?php
                                        $badgeColors = [
                                            'concept' => 'bg-gray-100 text-gray-700',
                                            'verstuurd' => 'bg-blue-100 text-blue-700',
                                            'betaald' => 'bg-green-100 text-green-700',
                                            'achterstallig' => 'bg-red-100 text-red-700',
                                            'geannuleerd' => 'bg-yellow-100 text-yellow-700',
                                        ];
                                        $color = $badgeColors[$inv['status']] ?? 'bg-gray-100 text-gray-700';
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color ?>"><?= ucfirst(htmlspecialchars($inv['status'])) ?></span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-500"><?= $inv['due_date'] ? date('d-m-Y', strtotime($inv['due_date'])) : '—' ?></td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('-translate-x-full');
        document.getElementById('sidebar-overlay').classList.toggle('hidden');
    }
    </script>
</body>
</html>
