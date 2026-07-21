<?php
declare(strict_types=1);

return [
    'name' => 'HanzeOnline SaaS',
    'url' => 'https://demo.hanzeonline.nl/saas-platform',
    'timezone' => 'Europe/Amsterdam',
    'debug' => false,

    'company' => [
        'name' => 'HanzeOnline B.V.',
        'address' => 'Kampen, Nederland',
        'kvk' => '94348677',
        'btw_nr' => 'NL004763267B01',
    ],

    'pdf' => [
        'paper' => 'A4',
        'orientation' => 'portrait',
    ],

    'email' => [
        'host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
        'port' => (int) (getenv('SMTP_PORT') ?: 587),
        'username' => getenv('SMTP_USER') ?: '',
        'password' => getenv('SMTP_PASS') ?: '',
        'from' => getenv('SMTP_FROM') ?: 'noreply@hanzeonline.nl',
        'from_name' => getenv('SMTP_FROM_NAME') ?: 'HanzeOnline',
    ],
];
