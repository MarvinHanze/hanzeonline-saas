<?php
declare(strict_types=1);

// Modulecatalogus voor HanzeOS.
//
// LET OP — architectuurwijziging: 'enabled' hieronder is nu alleen nog een
// GLOBALE kill-switch (ops-niveau, bv. een module tijdelijk helemaal uitzetten
// voor alle tenants tijdens onderhoud). Of een module voor een SPECIFIEKE tenant
// actief is, wordt sinds de SaaS-uitbreiding bepaald door de `tenant_modules`-tabel
// (zie core/Tenant.php: hasModule()/activeModules()), niet meer door dit bestand.
//
// 'core' => true betekent: altijd geladen/zichtbaar voor elke ingelogde gebruiker,
// niet aan/uit te zetten via de module-store en niet plan-afhankelijk.
// 'category' groepeert de kaarten in de "App Store"-achtige module-store.

return [
    'dashboard' => [
        'enabled' => true,
        'core' => true,
        'name' => 'Dashboard',
        'icon' => 'home',
        'description' => 'Realtime KPI\'s: omzet, facturen, projecten en klantengroei',
        'category' => 'Kern',
    ],
    'beheer' => [
        'enabled' => true,
        'core' => true,
        'name' => 'Beheer',
        'icon' => 'settings',
        'description' => 'Modules, branding, team, API-tokens, beveiliging en abonnement',
        'category' => 'Kern',
    ],
    'facturatie' => [
        'enabled' => true,
        'core' => false,
        'name' => 'Facturatie',
        'icon' => 'receipt',
        'description' => 'Facturen maken, versturen en bijhouden',
        'category' => 'Financieel',
    ],
    'hr' => [
        'enabled' => true,
        'core' => false,
        'name' => 'HR Dashboard',
        'icon' => 'users',
        'description' => 'Medewerkers, verlof, declaraties en beoordelingen',
        'category' => 'Personeel',
    ],
    'contract' => [
        'enabled' => true,
        'core' => false,
        'name' => 'Contractbeheer',
        'icon' => 'document',
        'description' => 'Contracten aanmaken, ondertekenen en beheren',
        'category' => 'Financieel',
    ],
    'crm' => [
        'enabled' => true,
        'core' => false,
        'name' => 'CRM',
        'icon' => 'target',
        'description' => 'Leads, offertes en verkooporders opvolgen',
        'category' => 'Verkoop',
    ],
    'projecten' => [
        'enabled' => true,
        'core' => false,
        'name' => 'Projecten',
        'icon' => 'kanban',
        'description' => 'Projectmanagement, taakverdeling en urenregistratie',
        'category' => 'Operatie',
    ],
    'voorraad' => [
        'enabled' => true,
        'core' => false,
        'name' => 'Voorraad',
        'icon' => 'box',
        'description' => 'Magazijnbeheer, inkooporders en materieelbeheer',
        'category' => 'Operatie',
    ],
    'esg' => [
        'enabled' => true,
        'core' => false,
        'placeholder' => true,
        'name' => 'ESG-rapportages',
        'icon' => 'leaf',
        'description' => 'Duurzaamheids- en ESG-rapportages (binnenkort beschikbaar)',
        'category' => 'Uitbreidingen',
    ],
    'incidenten' => [
        'enabled' => true,
        'core' => false,
        'placeholder' => true,
        'name' => 'Incidentenbeheer',
        'icon' => 'alert',
        'description' => 'Meldingen, incidenten en opvolging (binnenkort beschikbaar)',
        'category' => 'Uitbreidingen',
    ],
    'integraties' => [
        'enabled' => true,
        'core' => false,
        'placeholder' => true,
        'name' => 'Externe koppelingen',
        'icon' => 'plug',
        'description' => 'WooCommerce, Shopify, Exact en MailChimp (binnenkort beschikbaar)',
        'category' => 'Uitbreidingen',
    ],
];
