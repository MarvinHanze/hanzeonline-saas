<?php
declare(strict_types=1);

namespace Core;

/**
 * Centrale foutafhandeling voor productie. Dit hand-rolled framework had hier
 * nog geen globale handler voor: onverwachte exceptions/fatale fouten konden
 * ruwe PHP-stacktraces (met bestandspaden, queries, etc.) naar de browser
 * sturen. Nu: nooit details naar de client tenzij config/app.php 'debug' => true
 * staat, altijd server-side loggen via error_log() (Apache/Docker-logs).
 */
class ErrorHandler
{
    public static function register(bool $debug = false): void
    {
        ini_set('display_errors', '0');
        ini_set('display_startup_errors', '0');
        ini_set('log_errors', '1');
        error_reporting(E_ALL);

        set_exception_handler(function (\Throwable $e) use ($debug): void {
            error_log(
                'Uncaught exception: ' . $e->getMessage()
                . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n" . $e->getTraceAsString()
            );
            self::render($debug ? $e->getMessage() : null);
        });

        set_error_handler(function (int $severity, string $message, string $file = '', int $line = 0): bool {
            if (!(error_reporting() & $severity)) {
                return false;
            }
            throw new \ErrorException($message, 0, $severity, $file, $line);
        });

        register_shutdown_function(function () use ($debug): void {
            $error = error_get_last();
            $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
            if ($error !== null && in_array($error['type'], $fatalTypes, true)) {
                error_log('Fatal error: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']);
                if (!headers_sent()) {
                    self::render($debug ? $error['message'] : null);
                }
            }
        });
    }

    private static function render(?string $debugMessage): void
    {
        if (!headers_sent()) {
            http_response_code(500);
        }
        echo '<!DOCTYPE html><html lang="nl"><head><meta charset="UTF-8"><title>Er ging iets mis</title></head>'
            . '<body style="font-family:sans-serif;padding:3rem;text-align:center;color:#334155">'
            . '<h1>Er ging iets mis</h1><p>Er is een onverwachte fout opgetreden. Probeer het later opnieuw.</p>'
            . ($debugMessage !== null
                ? '<pre style="text-align:left;max-width:800px;margin:2rem auto;background:#f1f5f9;padding:1rem;'
                    . 'border-radius:8px;overflow:auto">' . htmlspecialchars($debugMessage) . '</pre>'
                : '')
            . '</body></html>';
    }
}
