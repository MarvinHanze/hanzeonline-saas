<?php
declare(strict_types=1);

namespace Core;

/**
 * Lichte CSRF-bescherming voor de sessie-gebonden webrouter (niet voor
 * api/v1 — dat is stateless Bearer-token-auth zonder cookies, dus CSRF is
 * daar niet van toepassing).
 *
 * Eén token per sessie (niet per-formulier), zodat meerdere open
 * tabbladen/formulieren elkaars token niet laten verlopen. Wordt
 * gecontroleerd door Core\Router::dispatch() vóór elke niet-GET request.
 */
class Csrf
{
    private const SESSION_KEY = 'csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[self::SESSION_KEY];
    }

    /** Hidden input, direct te plaatsen in een <form method="post">. */
    public static function field(): string
    {
        return '<input type="hidden" name="_csrf" value="' . htmlspecialchars(self::token(), ENT_QUOTES) . '">';
    }

    public static function verify(?string $token): bool
    {
        if (empty($_SESSION[self::SESSION_KEY]) || empty($token)) {
            return false;
        }
        return hash_equals((string) $_SESSION[self::SESSION_KEY], (string) $token);
    }
}
