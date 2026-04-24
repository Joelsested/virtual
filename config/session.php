<?php

if (!function_exists('sested_cookie_domain')) {
    function sested_cookie_domain()
    {
        // Usa dominio explicito se definido no .env
        if (function_exists('env')) {
            $envDomain = env('SESSION_COOKIE_DOMAIN', '');
            if ($envDomain !== '') {
                return $envDomain;
            }
        }
        // Sem dominio explicito, deixa o navegador decidir (host atual)
        return '';
    }
}

if (!function_exists('sested_session_start')) {
    function sested_session_start()
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $serverPort = isset($_SERVER['SERVER_PORT']) ? (int) $_SERVER['SERVER_PORT'] : 0;
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($serverPort === 443);
        $domain = sested_cookie_domain();

        if (!headers_sent()) {
            if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
                session_set_cookie_params(array(
                    'lifetime' => 0,
                    'path' => '/',
                    'domain' => $domain !== '' ? $domain : '',
                    'secure' => $isSecure,
                    'httponly' => true,
                    'samesite' => 'Lax',
                ));
            } else {
                session_set_cookie_params(0, '/; samesite=Lax', $domain !== '' ? $domain : '', $isSecure, true);
            }
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

        // Garante cookie da sessao na resposta (evita falhas em hospedagem)
        if (!headers_sent()) {
            $name = session_name();
            if ($name && session_id() !== '') {
                if (defined('PHP_VERSION_ID') && PHP_VERSION_ID >= 70300) {
                    @setcookie($name, session_id(), array(
                        'expires' => 0,
                        'path' => '/',
                        'domain' => $domain !== '' ? $domain : '',
                        'secure' => $isSecure,
                        'httponly' => true,
                        'samesite' => 'Lax',
                    ));
                } else {
                    @setcookie($name, session_id(), 0, '/', $domain !== '' ? $domain : '', $isSecure, true);
                }
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
