<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="<?= BASE ?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen — HanzeOnline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#f5f3ff', 100: '#ede9fe', 500: '#7c3aed', 600: '#6d28d9', 700: '#5b21b6' },
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-slate-100 antialiased">
<div class="min-h-screen flex">
    <!-- Side panel: enterprise / multi-tenant modules -->
    <div class="hidden lg:flex lg:w-[42%] xl:w-[38%] relative flex-col justify-between px-12 py-12 text-white overflow-hidden"
         style="background: linear-gradient(160deg, #2e1065 0%, #4c1d95 45%, #5b21b6 100%);">
        <div class="absolute inset-0 opacity-20 pointer-events-none"
             style="background-image: radial-gradient(circle at 20% 15%, rgba(255,255,255,.5) 0, transparent 2px), radial-gradient(circle at 70% 60%, rgba(255,255,255,.4) 0, transparent 2px), radial-gradient(circle at 40% 85%, rgba(255,255,255,.4) 0, transparent 2px); background-size: 140px 140px;"></div>

        <div class="relative">
            <div class="flex items-center gap-2.5 mb-14">
                <div class="w-9 h-9 rounded-lg bg-white/10 border border-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <path d="M9 12h6M12 9v6"/>
                    </svg>
                </div>
                <span class="text-lg font-semibold tracking-tight">HanzeOnline</span>
            </div>

            <h2 class="text-3xl font-bold leading-tight mb-3">Eén platform voor<br>elke afdeling</h2>
            <p class="text-violet-200 text-sm mb-10 max-w-xs">Multi-tenant SaaS-suite met modules die per bedrijf aan- en uitgezet kunnen worden.</p>

            <ul class="space-y-4">
                <li class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-violet-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M9 14l2 2 4-4"/><rect x="3" y="4" width="18" height="16" rx="2"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Facturatie</p>
                        <p class="text-xs text-violet-300">Facturen maken, versturen en bijhouden</p>
                    </div>
                </li>
                <li class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-violet-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3"/><path d="M2 20c0-3.3 3.1-6 7-6s7 2.7 7 6M16 8a3 3 0 100-6M22 20c0-2.8-2.2-5-5-5.5"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">HR Dashboard</p>
                        <p class="text-xs text-violet-300">Medewerkers, verlof en beoordelingen</p>
                    </div>
                </li>
                <li class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-white/10 border border-white/15 flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-violet-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M14 3v5h5"/><path d="M6 3h8l5 5v13a1 1 0 01-1 1H6a1 1 0 01-1-1V4a1 1 0 011-1z"/><path d="M9 13h6M9 17h6"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold">Contractbeheer</p>
                        <p class="text-xs text-violet-300">Contracten aanmaken en ondertekenen</p>
                    </div>
                </li>
            </ul>
        </div>

        <p class="relative text-xs text-violet-300">&copy; <?= date('Y') ?> HanzeOnline &mdash; Enterprise multi-tenant platform</p>
    </div>

    <!-- Form panel -->
    <div class="flex-1 flex items-center justify-center px-6 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-8 lg:hidden text-center">
                <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-brand-100 mb-3">
                    <svg class="w-7 h-7 text-brand-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <rect x="3" y="3" width="18" height="18" rx="3"/>
                        <path d="M9 12h6M12 9v6"/>
                    </svg>
                </div>
                <h1 class="text-xl font-bold text-slate-900">HanzeOnline</h1>
            </div>

            <div class="mb-7">
                <h2 class="text-2xl font-bold text-slate-900">Aanmelden</h2>
                <p class="text-sm text-slate-500 mt-1">Log in bij het werkruimte van uw organisatie</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <?php if (!empty($error)): ?>
                    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium bg-red-50 text-red-700 border border-red-200">
                        <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <form method="post" class="space-y-4"><?= \Core\Csrf::field() ?>
                    <div>
                        <label for="tenant" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1.5">Bedrijfsnaam</label>
                        <input type="text" name="tenant" id="tenant" required
                               class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-shadow"
                               placeholder="mijn-bedrijf" value="<?= htmlspecialchars($_POST['tenant'] ?? 'demo-bedrijf') ?>">
                    </div>
                    <div>
                        <label for="email" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1.5">E-mailadres</label>
                        <input type="email" name="email" id="email" required
                               class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-shadow"
                               placeholder="admin@bedrijf.nl" value="<?= htmlspecialchars($_POST['email'] ?? 'admin@demo.nl') ?>">
                    </div>
                    <div>
                        <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-slate-500 mb-1.5">Wachtwoord</label>
                        <input type="password" name="password" id="password" required value="demo123"
                               class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-shadow"
                               placeholder="••••••••">
                    </div>
                    <button type="submit"
                            class="w-full py-2.5 px-4 text-sm font-semibold text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors shadow-sm">
                        Inloggen
                    </button>
                </form>
            </div>

            <div class="mt-5 bg-violet-50 border border-violet-200 rounded-xl p-4 text-xs text-violet-800 space-y-1">
                <p class="font-semibold">Demo gegevens</p>
                <p>Bedrijf: <span class="font-mono font-semibold">demo-bedrijf</span></p>
                <p>E-mail: <span class="font-mono font-semibold">admin@demo.nl</span></p>
                <p>Wachtwoord: <span class="font-mono font-semibold">demo123</span></p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
