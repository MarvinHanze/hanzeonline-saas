<?php declare(strict_types=1);
use Core\Auth;
use Core\Tenant;

$user = Auth::user();
$tenantName = Tenant::name();
$statusLabels = [
    'concept' => ['Concept', 'bg-slate-100 text-slate-700'],
    'actief' => ['Actief', 'bg-green-50 text-green-700'],
    'verlopen' => ['Verlopen', 'bg-red-50 text-red-700'],
    'vernieuwd' => ['Vernieuwd', 'bg-blue-50 text-blue-700'],
    'geannuleerd' => ['Geannuleerd', 'bg-slate-100 text-slate-500'],
];
$st = $statusLabels[$contract['status']] ?? ['Onbekend', 'bg-slate-100 text-slate-500'];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="<?= BASE ?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($contract['title']) ?> — <?= htmlspecialchars($tenantName) ?></title>
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
        <span class="ml-3 font-bold text-slate-900 truncate"><?= htmlspecialchars($contract['title']) ?></span>
    </div>

    <!-- Main -->
    <main class="flex-1 lg:ml-64 pt-16 lg:pt-0">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900"><?= htmlspecialchars($contract['title']) ?></h1>
                    <p class="text-sm text-slate-500 mt-1">
                        <?= htmlspecialchars($contract['customer_name'] ?? $contract['employee_name'] ?? 'Geen partij toegewezen') ?>
                        · <?= $contract['start_date'] ? date('d-m-Y', strtotime($contract['start_date'])) : '-' ?>
                        — <?= $contract['end_date'] ? date('d-m-Y', strtotime($contract['end_date'])) : '-' ?>
                    </p>
                </div>
                <span class="inline-flex px-3 py-1 rounded-full text-sm font-medium <?= $st[1] ?> w-fit"><?= $st[0] ?></span>
            </div>

            <!-- Contract content -->
            <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
                <h2 class="text-lg font-semibold text-slate-900 mb-4">Contractinhoud</h2>
                <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed">
                    <?= $renderedContent ?>
                </div>
            </div>

            <!-- Signature section -->
            <?php if ($hasSignature): ?>
                <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Handtekening</h2>
                    <?php if ($signaturePath && file_exists($signaturePath)): ?>
                        <img src="<?= BASE ?>/contract/contracts/<?= (int) $contract['id'] ?>/handtekening" alt="Handtekening" class="max-w-xs border border-slate-200 rounded-lg">
                    <?php else: ?>
                        <p class="text-sm text-green-600 font-medium">Handtekening ontvangen op <?= date('d-m-Y H:i', strtotime($contract['signed_at'])) ?></p>
                    <?php endif; ?>
                    <p class="text-sm text-slate-500 mt-2">Ondertekend door: <?= htmlspecialchars($contract['signed_by'] ?? '-') ?></p>
                </div>
            <?php else: ?>
                <!-- Signature canvas -->
                <div class="bg-white rounded-xl border border-slate-200 p-6 mb-6">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Handtekening plaatsen</h2>
                    <form method="POST" action="<?= BASE ?>/contract/contracts/<?= $contract['id'] ?>/ondertekenen"><?= \Core\Csrf::field() ?>
                        <canvas id="signatureCanvas" width="500" height="200" class="border border-slate-300 rounded-lg bg-white cursor-crosshair w-full max-w-lg"></canvas>
                        <input type="hidden" name="signature_data" id="signatureData">
                        <div class="flex items-center gap-3 mt-4">
                            <button type="submit" id="signBtn" class="px-5 py-2.5 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600 transition-colors">
                                Ondertekenen
                            </button>
                            <button type="button" onclick="clearCanvas()" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors">
                                Wis handtekening
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Actions -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="<?= BASE ?>/contract/contracts/<?= $contract['id'] ?>/pdf" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors inline-flex items-center gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    PDF downloaden
                </a>

                <!-- Status change -->
                <div class="relative inline-block">
                    <button onclick="document.getElementById('statusForm').classList.toggle('hidden')" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 rounded-lg text-sm font-medium hover:bg-slate-50 transition-colors inline-flex items-center gap-2">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/></svg>
                        Status wijzigen
                    </button>
                    <form id="statusForm" method="POST" action="<?= BASE ?>/contract/contracts/<?= $contract['id'] ?>/status" class="hidden absolute top-full left-0 mt-2 bg-white border border-slate-200 rounded-lg shadow-lg p-3 z-10"><?= \Core\Csrf::field() ?>
                        <select name="status" class="border border-slate-300 rounded-lg px-3 py-2 text-sm mb-2 w-full">
                            <option value="concept" <?= $contract['status'] === 'concept' ? 'selected' : '' ?>>Concept</option>
                            <option value="actief" <?= $contract['status'] === 'actief' ? 'selected' : '' ?>>Actief</option>
                            <option value="verlopen" <?= $contract['status'] === 'verlopen' ? 'selected' : '' ?>>Verlopen</option>
                            <option value="vernieuwd" <?= $contract['status'] === 'vernieuwd' ? 'selected' : '' ?>>Vernieuwd</option>
                            <option value="geannuleerd" <?= $contract['status'] === 'geannuleerd' ? 'selected' : '' ?>>Geannuleerd</option>
                        </select>
                        <button type="submit" class="w-full px-4 py-2 bg-brand-500 text-white rounded-lg text-sm font-medium hover:bg-brand-600">Opslaan</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<?php if (!$hasSignature): ?>
<script>
const canvas = document.getElementById('signatureCanvas');
const ctx = canvas.getContext('2d');
let drawing = false;

function getPos(e) {
    const rect = canvas.getBoundingClientRect();
    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
    return { x: clientX - rect.left, y: clientY - rect.top };
}

canvas.addEventListener('mousedown', (e) => { drawing = true; ctx.beginPath(); ctx.moveTo(getPos(e).x, getPos(e).y); });
canvas.addEventListener('mousemove', (e) => { if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
canvas.addEventListener('mouseup', () => { drawing = false; document.getElementById('signatureData').value = canvas.toDataURL(); });
canvas.addEventListener('mouseleave', () => { drawing = false; });
canvas.addEventListener('touchstart', (e) => { e.preventDefault(); drawing = true; ctx.beginPath(); ctx.moveTo(getPos(e).x, getPos(e).y); });
canvas.addEventListener('touchmove', (e) => { e.preventDefault(); if (!drawing) return; const p = getPos(e); ctx.lineTo(p.x, p.y); ctx.stroke(); });
canvas.addEventListener('touchend', () => { drawing = false; document.getElementById('signatureData').value = canvas.toDataURL(); });

ctx.lineWidth = 2;
ctx.strokeStyle = '#1e293b';
ctx.lineCap = 'round';

function clearCanvas() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById('signatureData').value = '';
}
</script>
<?php endif; ?>
</body>
</html>
