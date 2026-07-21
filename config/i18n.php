<?php
declare(strict_types=1);

// Minimale i18n-structuur: "voorbereid maar NL-only".
//
// We vertalen NIET de hele applicatie (alle bestaande + nieuwe views zijn hardcoded
// Nederlands, zoals de rest van HanzeOS) — dat zou een groot deel van deze fase
// opslokken zonder functionele meerwaarde voor de huidige doelgroep (NL MKB).
// In plaats daarvan tonen we hier de STRUCTUUR die een volledige vertaling zou
// gebruiken: een key => tekst-array per locale, met core/I18n.php als kleine
// helper (I18n::t($key)) die op de tenant-locale terugvalt op 'nl'.
//
// Alleen 'nl' is volledig ingevuld. 'en' bestaat als skelet zodat een toekomstige
// vertaalronde alleen deze array hoeft aan te vullen — geen codewijzigingen elders.

return [
    'nl' => [
        'nav.dashboard' => 'Dashboard',
        'nav.modules' => 'Modules & instellingen',
        'nav.logout' => 'Uitloggen',
        'onboarding.title' => 'Welkom bij HanzeOS',
        'onboarding.subtitle' => 'In een paar stappen ben je klaar om te starten',
        'upgrade.title' => 'Upgrade vereist',
        'upgrade.body' => 'Deze module zit niet in je huidige plan.',
    ],
    'en' => [
        // Skelet — nog niet vertaald. Zelfde keys als 'nl', wordt gebruikt zodra
        // een tenant locale 'en' kiest EN de vertalingen zijn aangevuld.
    ],
];
