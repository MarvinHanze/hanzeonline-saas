<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="<?= BASE ?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facturatie Dashboard — HanzeOnline</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-gray-200 transform -translate-x-full lg:translate-x-0 transition-transform" id="sidebar">
            <div class="flex items-center gap-2 px-6 py-5 border-b border-gray-200">
                <div class="w-8 h-8 bg-blue-500 rounded-lg flex items-center justify-center">
                    <span class="text-white font-bold text-sm">H</span>
                </div>
                <span class="font-bold text-gray-900">HanzeOnline</span>
            </div>
            <nav class="px-3 py-4 space-y-1">
                <p class="px-3 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Facturatie</p>
                <a href="<?= BASE ?>/facturatie" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg bg-blue-50 text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-4 0a1 1 0 01-1-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 01-1 1"/></svg>
                    Dashboard
                </a>
                <a href="<?= BASE ?>/facturatie/klanten" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Klanten
                </a>
                <a href="<?= BASE ?>/facturatie/facturen" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Facturen
                </a>
                <div class="border-t border-gray-200 my-3"></div>
                <a href="<?= BASE ?>/" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                    Terug naar dashboard
                </a>
            </nav>
        </aside>

        <!-- Mobile overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <!-- Main -->
        <div class="flex-1 lg:ml-64">
            <!-- Top bar -->
            <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between lg:px-8">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-900">Facturatie Dashboard</h1>
                <div class="text-sm text-gray-500"><?= date('d-m-Y') ?></div>
            </header>

            <!-- Content -->
            <main class="p-4 lg:p-8">
                <!-- Stats -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Totaal klanten</p>
                                <p class="text-2xl font-bold text-gray-900"><?= $totalCustomers ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Openstaande facturen</p>
                                <p class="text-2xl font-bold text-gray-900">€ <?= number_format((float)$openAmount, 2, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Betaald deze maand</p>
                                <p class="text-2xl font-bold text-gray-900">€ <?= number_format((float)$paidThisMonth, 2, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Totaal omzet</p>
                                <p class="text-2xl font-bold text-gray-900">€ <?= number_format((float)$totalRevenue, 2, ',', '.') ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent invoices -->
                <div class="bg-white rounded-xl border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <h2 class="text-lg font-semibold text-gray-900">Recente facturen</h2>
                        <a href="<?= BASE ?>/facturatie/facturen" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Bekijk alles</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr>
                                    <th class="px-6 py-3 font-medium text-gray-500">Nummer</th>
                                    <th class="px-6 py-3 font-medium text-gray-500">Klant</th>
                                    <th class="px-6 py-3 font-medium text-gray-500">Bedrag</th>
                                    <th class="px-6 py-3 font-medium text-gray-500">Status</th>
                                    <th class="px-6 py-3 font-medium text-gray-500">Datum</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (empty($recentInvoices)): ?>
                                <tr><td colspan="5" class="px-6 py-8 text-center text-gray-400">Nog geen facturen aangemaakt</td></tr>
                                <?php else: ?>
                                <?php foreach ($recentInvoices as $inv): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <a href="<?= BASE ?>/facturatie/facturen/<?= $inv['id'] ?>" class="font-medium text-blue-600 hover:text-blue-700"><?= htmlspecialchars($inv['number']) ?></a>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($inv['customer_name']) ?></td>
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
                                    <td class="px-6 py-4 text-gray-500"><?= date('d-m-Y', strtotime($inv['created_at'])) ?></td>
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
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
    </script>
</body>
</html>
