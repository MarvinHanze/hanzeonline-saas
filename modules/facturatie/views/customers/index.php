<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klanten — Facturatie — HanzeOnline</title>
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
                <a href="/facturatie/klanten" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg bg-blue-50 text-blue-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Klanten
                </a>
                <a href="/facturatie/facturen" class="flex items-center gap-3 px-3 py-2 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100">
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
            <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between lg:px-8">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h1 class="text-lg font-semibold text-gray-900">Klanten</h1>
                <a href="/facturatie/klanten/nieuw" class="inline-flex items-center gap-2 bg-blue-500 hover:bg-blue-600 text-white text-sm font-medium px-4 py-2 rounded-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Nieuwe klant
                </a>
            </header>

            <main class="p-4 lg:p-8">
                <!-- Search -->
                <form method="GET" action="/facturatie/klanten" class="mb-6">
                    <div class="relative max-w-md">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Zoek op naam, email of BTW-nummer..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none">
                    </div>
                </form>

                <!-- Table -->
                <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-left">
                                <tr>
                                    <th class="px-6 py-3 font-medium text-gray-500">Naam</th>
                                    <th class="px-6 py-3 font-medium text-gray-500">Email</th>
                                    <th class="px-6 py-3 font-medium text-gray-500 hidden md:table-cell">Telefoon</th>
                                    <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">BTW-nr</th>
                                    <th class="px-6 py-3 font-medium text-gray-500 hidden lg:table-cell">Aanmaakdatum</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (empty($customers)): ?>
                                <tr><td colspan="6" class="px-6 py-8 text-center text-gray-400">Geen klanten gevonden</td></tr>
                                <?php else: ?>
                                <?php foreach ($customers as $customer): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4">
                                        <a href="/facturatie/klanten/<?= $customer['id'] ?>" class="font-medium text-blue-600 hover:text-blue-700"><?= htmlspecialchars($customer['name']) ?></a>
                                    </td>
                                    <td class="px-6 py-4 text-gray-700"><?= htmlspecialchars($customer['email'] ?? '—') ?></td>
                                    <td class="px-6 py-4 text-gray-700 hidden md:table-cell"><?= htmlspecialchars($customer['phone'] ?? '—') ?></td>
                                    <td class="px-6 py-4 text-gray-500 hidden lg:table-cell"><?= htmlspecialchars($customer['btw_nr'] ?? '—') ?></td>
                                    <td class="px-6 py-4 text-gray-500 hidden lg:table-cell"><?= date('d-m-Y', strtotime($customer['created_at'])) ?></td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="/facturatie/klanten/<?= $customer['id'] ?>" class="text-gray-400 hover:text-blue-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                        </a>
                                    </td>
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
