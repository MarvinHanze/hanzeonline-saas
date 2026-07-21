<?php
declare(strict_types=1);

/**
 * Kleine tabbladnavigatie tussen de Beheer-subpagina's. Geen apart route-key
 * (beheer heeft één sidebar-item), dus dit is de secundaire navigatie binnen
 * de module zelf. Gedeeld via require (niet via View::partial, dat is
 * gereserveerd voor _partials/) zodat elke beheer-view hem kan tonen.
 */
if (!function_exists('beheerNav')) {
    function beheerNav(string $active): void
    {
        $items = [
            'modules' => ['Modules', '/beheer/modules'],
            'branding' => ['Branding', '/beheer/branding'],
            'team' => ['Team', '/beheer/team'],
            'api_tokens' => ['API-tokens', '/beheer/api-tokens'],
            'security' => ['Beveiliging', '/beheer/beveiliging'],
            'subscription' => ['Abonnement', '/beheer/abonnement'],
        ];
        echo '<nav style="display:flex;gap:.25rem;flex-wrap:wrap;margin-bottom:1.5rem;border-bottom:1px solid var(--hz-border);padding-bottom:.5rem;">';
        foreach ($items as $key => [$label, $path]) {
            $isActive = $key === $active;
            $style = $isActive
                ? 'background:var(--hz-bg);color:var(--hz-primary);font-weight:600;'
                : 'color:var(--hz-text-muted);';
            echo '<a href="' . BASE . $path . '" style="padding:.45rem .85rem;border-radius:var(--hz-radius);text-decoration:none;font-size:.85rem;' . $style . '">'
                . htmlspecialchars($label) . '</a>';
        }
        echo '</nav>';
    }
}
