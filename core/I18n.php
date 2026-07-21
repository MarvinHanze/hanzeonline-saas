<?php
declare(strict_types=1);

namespace Core;

/**
 * Minimale i18n-helper. Zie config/i18n.php voor de motivatie waarom dit
 * NL-only is met een "voorbereid" skelet voor 'en'.
 */
class I18n
{
    private static ?array $strings = null;

    private static function all(): array
    {
        if (self::$strings === null) {
            self::$strings = require __DIR__ . '/../config/i18n.php';
        }
        return self::$strings;
    }

    public static function t(string $key, ?string $locale = null): string
    {
        $locale = $locale ?? Tenant::locale();
        $all = self::all();
        return $all[$locale][$key] ?? $all['nl'][$key] ?? $key;
    }
}
