<?php

if (!function_exists('sested_cookie_domain')) {
    function sested_cookie_domain(): string
    {
        // Usa domínio explícito se definido no .env
        if (function_exists('env')) {
            $envDomain = env('SESSION_COOKIE_DOMAIN', '');
            if ($envDomain !== '') {
                return $envDomain;
            }
        }
        // Sem domínio explícito, deixa o navegador decidir (host atual)
        return '';
    }
}

if (!function_exists('sested_session_start')) {
    function sested_session_start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $cookieParams = session_get_cookie_params();
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || (($_SERVER['SERVER_PORT'] ?? 0) == 443);
        $domain = sested_cookie_domain();

        if (!headers_sent()) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => $domain !== '' ? $domain : '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
        } else {
            $prevUseCookies = ini_get('session.use_cookies');
            $prevOnlyCookies = ini_get('session.use_only_cookies');
            if ($prevUseCookies !== false) {
                @ini_set('session.use_cookies', '0');
            }
            if ($prevOnlyCookies !== false) {
                @ini_set('session.use_only_cookies', '0');
            }
        }

        @session_start();

        // Garante cookie da sessão na resposta (evita falhas em hospedagem)
        if (!headers_sent()) {
            $name = session_name();
            if ($name && session_id() !== '') {
                @setcookie($name, session_id(), [
                    'expires' => 0,
                    'path' => '/',
                    'domain' => $domain !== '' ? $domain : '',
                    'secure' => $isSecure,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }
        }

        if (isset($prevUseCookies) && $prevUseCookies !== false) {
            @ini_set('session.use_cookies', (string) $prevUseCookies);
        }
        if (isset($prevOnlyCookies) && $prevOnlyCookies !== false) {
            @ini_set('session.use_only_cookies', (string) $prevOnlyCookies);
        }
    }
}
