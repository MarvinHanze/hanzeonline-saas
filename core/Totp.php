<?php
declare(strict_types=1);

namespace Core;

/**
 * Compacte eigen TOTP-implementatie (RFC 6238 / HOTP RFC 4226), zonder
 * Composer-dependency. Gebruikt voor echte 2FA op owner/admin-accounts.
 *
 * Vereenvoudiging: er wordt GEEN QR-afbeelding gegenereerd (dat vereist een
 * QR-library of de GD-extensie, wat we hier niet kunnen garanderen zonder
 * `composer install`). In plaats daarvan tonen we het secret + de
 * otpauth://-URI als kopieerbare tekst, wat elke authenticator-app ook
 * accepteert via "handmatig toevoegen".
 */
class Totp
{
    private const DIGITS = 6;
    private const PERIOD = 30;
    private const ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    public static function generateSecret(int $bytes = 20): string
    {
        return self::base32Encode(random_bytes($bytes));
    }

    public static function provisioningUri(string $secret, string $accountEmail, string $issuer = 'HanzeOS'): string
    {
        $label = rawurlencode($issuer) . ':' . rawurlencode($accountEmail);
        $query = http_build_query([
            'secret' => $secret,
            'issuer' => $issuer,
            'algorithm' => 'SHA1',
            'digits' => self::DIGITS,
            'period' => self::PERIOD,
        ]);
        return "otpauth://totp/{$label}?{$query}";
    }

    public static function currentCode(string $secret, ?int $timestamp = null): string
    {
        $timestamp = $timestamp ?? time();
        return self::hotp($secret, intdiv($timestamp, self::PERIOD));
    }

    /**
     * Verifieert een 6-cijferige code met een tolerantie van $window stappen
     * (30s per stap) om klok-drift tussen server en telefoon op te vangen.
     */
    public static function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = preg_replace('/\s+/', '', (string) $code);
        if ($code === '' || !ctype_digit($code) || strlen($code) !== self::DIGITS) {
            return false;
        }
        $counter = intdiv(time(), self::PERIOD);
        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::hotp($secret, $counter + $i), $code)) {
                return true;
            }
        }
        return false;
    }

    private static function hotp(string $secret, int $counter): string
    {
        $key = self::base32Decode($secret);
        $binCounter = pack('N*', 0, $counter);
        $hash = hash_hmac('sha1', $binCounter, $key, true);
        $offset = ord($hash[19]) & 0x0F;
        $truncated = ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF);
        $code = (string) ($truncated % (10 ** self::DIGITS));
        return str_pad($code, self::DIGITS, '0', STR_PAD_LEFT);
    }

    private static function base32Encode(string $data): string
    {
        $binaryString = '';
        foreach (str_split($data) as $char) {
            $binaryString .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
        }
        $output = '';
        foreach (str_split($binaryString, 5) as $chunk) {
            if (strlen($chunk) < 5) {
                $chunk = str_pad($chunk, 5, '0', STR_PAD_RIGHT);
            }
            $output .= self::ALPHABET[bindec($chunk)];
        }
        return $output;
    }

    private static function base32Decode(string $data): string
    {
        $data = strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $data));
        $binaryString = '';
        foreach (str_split($data) as $char) {
            $pos = strpos(self::ALPHABET, $char);
            if ($pos === false) {
                continue;
            }
            $binaryString .= str_pad(decbin($pos), 5, '0', STR_PAD_LEFT);
        }
        $bytes = '';
        foreach (str_split($binaryString, 8) as $byte) {
            if (strlen($byte) < 8) {
                break;
            }
            $bytes .= chr(bindec($byte));
        }
        return $bytes;
    }

    /** Groepeert een secret in blokken van 4 tekens, prettiger om over te typen. */
    public static function formatSecretForDisplay(string $secret): string
    {
        return trim(chunk_split($secret, 4, ' '));
    }
}
