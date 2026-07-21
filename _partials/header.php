<?php
declare(strict_types=1);

use Core\Auth;
use Core\Tenant;

require_once __DIR__ . '/icons.php';

/**
 * Gedeelde app-shell (sidebar + topbar) voor alle NIEUWE modules
 * (dashboard, beheer, crm, projecten, voorraad, esg, incidenten, integraties).
 * Gebruikt de hz-* componentenbibliotheek (public/assets/css/js/components.*)
 * i.p.v. de per-view Tailwind-CDN-aanpak van de oudere modules (hr/facturatie/
 * contract) — die blijven ongewijzigd om regressie te voorkomen.
 *
 * Verwacht in $data: 'pageTitle' (string), 'activeModule' (string, module-key).
 */
$user = Auth::user();
$pageTitle = $pageTitle ?? 'HanzeOS';
$activeModule = $activeModule ?? '';
$tenantName = Tenant::name() ?: 'HanzeOS';
$brandColor = Tenant::brandColor();
$logoPath = Tenant::logoPath();
$navModules = Tenant::activeModules();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <base href="<?= BASE ?>/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> — <?= htmlspecialchars($tenantName) ?></title>
    <link rel="stylesheet" href="<?= BASE ?>/assets/css/components.css">
    <style>
        :root { --hz-primary: <?= htmlspecialchars($brandColor) ?>; --hz-primary-dark: <?= htmlspecialchars($brandColor) ?>; }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: var(--hz-bg); color: var(--hz-text); }
        .hz-shell { display: flex; min-height: 100vh; }
        .hz-shell__main { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .hz-shell__content { padding: 1.5rem; flex: 1; }
        .hz-shell__brand { display: flex; align-items: center; gap: .6rem; padding: 1rem; border-bottom: 1px solid var(--hz-border); }
        .hz-shell__brand img, .hz-shell__brand-fallback { width: 30px; height: 30px; border-radius: 8px; object-fit: cover; }
        .hz-shell__brand-fallback { background: var(--hz-primary); }
        .hz-shell__nav { padding: .75rem 0; flex: 1; overflow-y: auto; }
        .hz-shell__user { padding: .85rem 1rem; border-top: 1px solid var(--hz-border); font-size: .82rem; color: var(--hz-text-muted); }
        .hz-shell__user a { color: var(--hz-primary); text-decoration: none; }
        @media (max-width: 860px) {
            .hz-sidebar { position: fixed; z-index: 90; transform: translateX(-100%); transition: transform .2s ease; }
            .hz-sidebar.hz-is-open { transform: translateX(0); }
            .hz-shell__main { width: 100%; }
        }
    </style>
</head>
<body>
<div class="hz-shell">
    <aside class="hz-sidebar" id="appSidebar">
        <div class="hz-shell__brand">
            <?php if ($logoPath): ?>
                <img src="<?= BASE . '/' . htmlspecialchars($logoPath) ?>" alt="">
            <?php else: ?>
                <div class="hz-shell__brand-fallback"></div>
            <?php endif; ?>
            <strong class="hz-sidebar__label"><?= htmlspecialchars($tenantName) ?></strong>
        </div>
        <nav class="hz-shell__nav">
            <?php foreach ($navModules as $key => $m): ?>
                <a href="<?= BASE . '/' . $key ?>" class="hz-sidebar__item <?= $activeModule === $key ? 'hz-is-active' : '' ?>">
                    <?= hz_icon($m['icon'] ?? 'box') ?>
                    <span class="hz-sidebar__label"><?= htmlspecialchars($m['name']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="hz-shell__user">
            <div><?= htmlspecialchars($user['name']) ?></div>
            <div><?= htmlspecialchars(ucfirst($user['role'])) ?></div>
            <div style="margin-top:.4rem"><a href="<?= BASE ?>/logout">Uitloggen</a></div>
        </div>
    </aside>
    <div class="hz-shell__main">
        <header class="hz-navbar">
            <button class="hz-hamburger" data-hz-mobile-toggle="appSidebar" aria-label="Menu"><span></span><span></span><span></span></button>
            <strong><?= htmlspecialchars($pageTitle) ?></strong>
            <div class="hz-navbar__actions"></div>
        </header>
        <div class="hz-shell__content">
