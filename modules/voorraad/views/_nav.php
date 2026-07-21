<?php
declare(strict_types=1);

if (!function_exists('voorraadNav')) {
    function voorraadNav(string $active): void
    {
        $items = [
            'dashboard' => ['Overzicht', '/voorraad'],
            'products' => ['Producten', '/voorraad/producten'],
            'warehouses' => ['Magazijnen', '/voorraad/magazijnen'],
            'purchase_orders' => ['Inkooporders', '/voorraad/inkooporders'],
            'equipment' => ['Materieel', '/voorraad/materieel'],
        ];
        echo '<nav style="display:flex;gap:.25rem;flex-wrap:wrap;margin-bottom:1.5rem;border-bottom:1px solid var(--hz-border);padding-bottom:.5rem;">';
        foreach ($items as $key => [$label, $path]) {
            $style = $key === $active
                ? 'background:var(--hz-bg);color:var(--hz-primary);font-weight:600;'
                : 'color:var(--hz-text-muted);';
            echo '<a href="' . BASE . $path . '" style="padding:.45rem .85rem;border-radius:var(--hz-radius);text-decoration:none;font-size:.85rem;' . $style . '">'
                . htmlspecialchars($label) . '</a>';
        }
        echo '</nav>';
    }
}
