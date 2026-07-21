<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen — HanzeOnline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: { 50: '#eff6ff', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-slate-50 antialiased flex items-center justify-center px-4">
<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand-50 mb-4">
            <svg class="w-9 h-9 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="3" y="3" width="18" height="18" rx="3"/>
                <path d="M9 12h6M12 9v6"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">HanzeOnline</h1>
        <p class="text-sm text-slate-500 mt-1">SaaS Platform</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <?php if (!empty($error)): ?>
            <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium bg-red-50 text-red-700 border border-red-200">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4">
            <div>
                <label for="tenant" class="block text-sm font-medium text-slate-700 mb-1">Bedrijfsnaam</label>
                <input type="text" name="tenant" id="tenant" required
                       class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-shadow"
                       placeholder="mijn-bedrijf" value="<?= htmlspecialchars($_POST['tenant'] ?? '') ?>">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-slate-700 mb-1">E-mailadres</label>
                <input type="email" name="email" id="email" required
                       class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-shadow"
                       placeholder="admin@bedrijf.nl" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Wachtwoord</label>
                <input type="password" name="password" id="password" required
                       class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-shadow"
                       placeholder="••••••••">
            </div>
            <button type="submit"
                    class="w-full py-2.5 px-4 text-sm font-semibold text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors shadow-sm">
                Inloggen
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-xs text-slate-400">
        Nog geen account? <a href="/register" class="text-brand-500 hover:underline">Registreren</a>
    </p>
</div>
</body>
</html>
