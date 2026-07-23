<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="<?= BASE ?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($invoice['number']) ?> — Facturatie — HanzeOnline</title>
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
                <a href="<?= BASE ?>/facturatie/klanten" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Klanten
                </a>
                <a href="<?= BASE ?>/facturatie/facturen" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg bg-blue-50 text-blue-700">
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

        <div id="sidebar-overlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

        <div class="flex-1 lg:ml-64">
            <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between lg:px-8">
                <div class="flex items-center gap-4">
                    <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <h1 class="text-lg font-semibold text-gray-900">Factuur <?= htmlspecialchars($invoice['number']) ?></h1>
                    <?php
                    $badgeColors = [
                        'concept' => 'bg-gray-100 text-gray-700',
                        'verstuurd' => 'bg-blue-100 text-blue-700',
                        'betaald' => 'bg-green-100 text-green-700',
                        'achterstallig' => 'bg-red-100 text-red-700',
                        'geannuleerd' => 'bg-yellow-100 text-yellow-700',
                    ];
                    $color = $badgeColors[$invoice['status']] ?? 'bg-gray-100 text-gray-700';
                    ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?= $color ?>"><?= ucfirst(htmlspecialchars($invoice['status'])) ?></span>
                </div>
                <div class="flex gap-2">
                    <a href="<?= BASE ?>/facturatie/facturen/<?= $invoice['id'] ?>/pdf" class="inline-flex items-center gap-1.5 text-sm font-medium text-gray-600 border border-gray-300 px-4 py-2 rounded-lg hover:bg-gray-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        PDF
                    </a>
                </div>
            </header>

            <main class="p-4 lg:p-8">
                <div class="max-w-4xl">
                    <!-- Invoice header -->
                    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-6">
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900"><?= htmlspecialchars($invoice['number']) ?></h2>
                                <p class="text-sm text-gray-500 mt-1">Aangemaakt: <?= date('d-m-Y', strtotime($invoice['created_at'])) ?></p>
                                <p class="text-sm text-gray-500">Vervaldatum: <?= $invoice['due_date'] ? date('d-m-Y', strtotime($invoice['due_date'])) : '—' ?></p>
                                <?php if ($invoice['paid_at']): ?>
                                <p class="text-sm text-green-600 mt-1">Betaald op: <?= date('d-m-Y', strtotime($invoice['paid_at'])) ?></p>
                                <?php endif; ?>
                            </div>
                            <!-- Status change -->
                            <div class="flex items-center gap-2">
                                <form method="POST" action="<?= BASE ?>/facturatie/facturen/<?= $invoice['id'] ?>/status" class="flex gap-2"><?= \Core\Csrf::field() ?>
                                    <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                                        <option value="concept" <?= $invoice['status'] === 'concept' ? 'selected' : '' ?>>Concept</option>
                                        <option value="verstuurd" <?= $invoice['status'] === 'verstuurd' ? 'selected' : '' ?>>Verstuurd</option>
                                        <option value="betaald" <?= $invoice['status'] === 'betaald' ? 'selected' : '' ?>>Betaald</option>
                                        <option value="achterstallig" <?= $invoice['status'] === 'achterstallig' ? 'selected' : '' ?>>Achterstallig</option>
                                        <option value="geannuleerd" <?= $invoice['status'] === 'geannuleerd' ? 'selected' : '' ?>>Geannuleerd</option>
                                    </select>
                                    <button type="submit" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium rounded-lg">Wijzigen</button>
                                </form>
                            </div>
                        </div>

                        <!-- Customer info -->
                        <div class="border-t border-gray-200 pt-4">
                            <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Klantgegevens</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                <div>
                                    <span class="text-gray-500">Naam:</span>
                                    <span class="text-gray-900 ml-1"><?= htmlspecialchars($invoice['customer_name']) ?></span>
                                </div>
                                <div>
                                    <span class="text-gray-500">Email:</span>
                                    <span class="text-gray-900 ml-1"><?= htmlspecialchars($invoice['customer_email'] ?? '—') ?></span>
                                </div>
                                <?php if (!empty($invoice['customer_address'])): ?>
                                <div>
                                    <span class="text-gray-500">Adres:</span>
                                    <span class="text-gray-900 ml-1"><?= htmlspecialchars($invoice['customer_address']) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($invoice['customer_postal']) || !empty($invoice['customer_city'])): ?>
                                <div>
                                    <span class="text-gray-500">Plaats:</span>
                                    <span class="text-gray-900 ml-1"><?= htmlspecialchars(($invoice['customer_postal'] ?? '') . ' ' . ($invoice['customer_city'] ?? '')) ?></span>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($invoice['customer_btw_nr'])): ?>
                                <div>
                                    <span class="text-gray-500">BTW-nr:</span>
                                    <span class="text-gray-900 ml-1"><?= htmlspecialchars($invoice['customer_btw_nr']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Line items -->
                    <div class="bg-white rounded-xl border border-gray-200 mb-6">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="bg-gray-50 text-left">
                                    <tr>
                                        <th class="px-6 py-3 font-medium text-gray-500">Omschrijving</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Aantal</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Prijs</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-right">BTW</th>
                                        <th class="px-6 py-3 font-medium text-gray-500 text-right">Totaal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td class="px-6 py-4 text-gray-900"><?= htmlspecialchars($item['description']) ?></td>
                                        <td class="px-6 py-4 text-gray-700 text-right"><?= number_format((float)$item['quantity'], 2, ',', '.') ?></td>
                                        <td class="px-6 py-4 text-gray-700 text-right">€ <?= number_format((float)$item['unit_price'], 2, ',', '.') ?></td>
                                        <td class="px-6 py-4 text-gray-700 text-right"><?= number_format((float)$item['btw_rate'], 0) ?>%</td>
                                        <td class="px-6 py-4 text-gray-900 text-right font-medium">€ <?= number_format((float)$item['total'], 2, ',', '.') ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Totals -->
                        <div class="border-t border-gray-200 px-6 py-4">
                            <div class="flex justify-end">
                                <div class="w-64">
                                    <div class="flex justify-between text-sm py-1">
                                        <span class="text-gray-500">Subtotaal</span>
                                        <span class="font-medium text-gray-900">€ <?= number_format((float)$invoice['subtotal'], 2, ',', '.') ?></span>
                                    </div>
                                    <div class="flex justify-between text-sm py-1">
                                        <span class="text-gray-500">BTW (<?= number_format((float)$invoice['btw_rate'], 0) ?>%)</span>
                                        <span class="font-medium text-gray-900">€ <?= number_format((float)$invoice['btw_amount'], 2, ',', '.') ?></span>
                                    </div>
                                    <div class="flex justify-between text-base py-2 border-t border-gray-200">
                                        <span class="font-semibold text-gray-900">Totaal</span>
                                        <span class="font-bold text-gray-900 text-lg">€ <?= number_format((float)$invoice['total'], 2, ',', '.') ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($invoice['notes'])): ?>
                    <div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
                        <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Notities</h3>
                        <p class="text-sm text-gray-700"><?= nl2br(htmlspecialchars($invoice['notes'])) ?></p>
                    </div>
                    <?php endif; ?>

                    <!-- Actions -->
                    <div class="flex flex-wrap gap-3">
                        <a href="<?= BASE ?>/facturatie/facturen/<?= $invoice['id'] ?>/pdf" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            PDF downloaden
                        </a>
                        <form method="POST" action="<?= BASE ?>/facturatie/facturen/<?= $invoice['id'] ?>/herinnering" class="inline"><?= \Core\Csrf::field() ?>
                            <button type="submit" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 text-sm font-medium px-5 py-2.5 rounded-lg hover:bg-gray-50" onclick="return confirm('Weet je zeker dat je een herinnering wilt versturen?')">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                Herinnering versturen
                            </button>
                        </form>
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
