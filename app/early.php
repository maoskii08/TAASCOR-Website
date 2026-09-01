<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', '1');
    header_remove('X-Powered-By');

    set_exception_handler(static function (Throwable $exception): void {
        error_log('TAASCOR uncaught application exception: ' . $exception->getMessage());
        if (!headers_sent()) {
            http_response_code(503);
            header_remove('X-Powered-By');
            header('Content-Type: text/html; charset=UTF-8');
            header('Cache-Control: no-store');
            header('Retry-After: 300');
            header('X-Content-Type-Options: nosniff');
            header('X-Frame-Options: DENY');
            header('Referrer-Policy: no-referrer');
            header("Content-Security-Policy: default-src 'none'; frame-ancestors 'none'; base-uri 'none'; form-action 'none'");
        }
        echo '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Service temporarily unavailable | TAASCOR</title><body><main><h1>Service temporarily unavailable</h1><p>This secure TAASCOR service is not ready to accept requests. Please try again later.</p><p><a href="/">Return to the TAASCOR website</a></p></main></body></html>';
    });
}
