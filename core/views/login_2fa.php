<?php declare(strict_types=1); ?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <base href="<?= BASE ?>/">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificatiecode — HanzeOnline</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: {
                brand: { 50: '#eff6ff', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' },
            }}}
        }
    </script>
</head>
<body class="h-full bg-slate-50 antialiased flex items-center justify-center px-4">
<div class="w-full max-w-sm">
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand-50 mb-4">
            <svg class="w-9 h-9 text-brand-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <rect x="5" y="11" width="14" height="10" rx="2"/>
                <path d="M8 11V7a4 4 0 018 0v4"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900">Tweestapsverificatie</h1>
        <p class="text-sm text-slate-500 mt-1">Vul de 6-cijferige code uit je authenticator-app in</p>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
        <?php if (!empty($error)): ?>
            <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium bg-red-50 text-red-700 border border-red-200">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-4"><?= \Core\Csrf::field() ?>
            <div>
                <label for="code" class="block text-sm font-medium text-slate-700 mb-1">Verificatiecode</label>
                <input type="text" name="code" id="code" required autofocus inputmode="numeric" pattern="[0-9]*" maxlength="6"
                       class="w-full px-3 py-2.5 text-sm border border-slate-300 rounded-lg text-center tracking-[0.5em] font-mono focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition-shadow"
                       placeholder="000000">
            </div>
            <button type="submit"
                    class="w-full py-2.5 px-4 text-sm font-semibold text-white bg-brand-500 rounded-lg hover:bg-brand-600 transition-colors shadow-sm">
                Verifiëren
            </button>
        </form>
    </div>

    <p class="mt-6 text-center text-xs text-slate-400">
        <a href="<?= BASE ?>/login" class="text-brand-500 hover:underline">Terug naar inloggen</a>
    </p>
</div>
</body>
</html>
