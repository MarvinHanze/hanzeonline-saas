<?php
declare(strict_types=1);

// Pricing / feature-gating config voor HanzeOS.
// Dit is GEEN echte betaalintegratie (geen Mollie/Stripe) — alleen de code-kant van
// "welk plan mag welke modules activeren en hoeveel gebruikers toevoegen".
// Owners kunnen via Beheer > Abonnement hun plan wijzigen (simulatie, geen betaalflow).

return [
    'freemium' => [
        'name' => 'Freemium',
        'price' => 0,
        'price_label' => 'Gratis',
        'max_users' => 2,
        'modules' => ['dashboard', 'beheer', 'facturatie'],
    ],
    'starter' => [
        'name' => 'Starter',
        'price' => 15,
        'price_label' => '€15 / maand',
        'max_users' => 5,
        'modules' => ['dashboard', 'beheer', 'facturatie', 'hr', 'contract'],
    ],
    'business' => [
        'name' => 'Business',
        'price' => 49,
        'price_label' => '€49 / maand',
        'max_users' => 25,
        'modules' => ['dashboard', 'beheer', 'facturatie', 'hr', 'contract', 'crm', 'projecten', 'voorraad'],
    ],
    'enterprise' => [
        'name' => 'Enterprise',
        'price' => 199,
        'price_label' => 'vanaf €199 / maand',
        'max_users' => null, // null = onbeperkt
        'modules' => '*', // '*' = alle modules uit de catalogus, incl. optionele uitbreidingen
    ],
];
