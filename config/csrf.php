<?php
function csrf_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $customSavePath = env('SESSION_SAVE_PATH', '');
    if ($customSavePath !== '') {
        if (!is_dir($customSavePath)) {
            @mkdir($customSavePath, 0700, true);
        }
        if (is_dir($customSavePath) && is_writable($customSavePath)) {
            session_save_path($customSavePath);
        }
    }

    $params = session_get_cookie_params();
    $host = $_SERVER['HTTP_HOST'] ?? '';
    $host = preg_replace('/:\d+$/', '', $host);
    $domain = '';

    if ($host !== '' && $host !== 'localhost' && !filter_var($host, FILTER_VALIDATE_IP)) {
        $host = preg_replace('/^www\./i', '', $host);
        $domain = '.' . $host;
    }

    $cookieName = session_name();
    if ($cookieName !== '' && !empty($_COOKIE[$cookieName]) && session_id() === '') {
        session_id($_COOKIE[$cookieName]);
    }

    $forwardedProto = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? ''));
    $forwardedSsl = strtolower((string) ($_SERVER['HTTP_X_FORWARDED_SSL'] ?? ''));
    $cfVisitor = (string) ($_SERVER['HTTP_CF_VISITOR'] ?? '');
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['SERVER_PORT'] ?? 0) == 443
        || $forwardedProto === 'https'
        || $forwardedSsl === 'on'
        || stripos($cfVisitor, '"scheme":"https"') !== false;

    session_set_cookie_params([
        'lifetime' => $params['lifetime'],
        'path' => $params['path'] ?: '/',
        // Em localhost/IP o dominio precisa ser vazio para o cookie de sessao persistir corretamente.
        'domain' => $domain,
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_cookies', '1');

    @session_start();
}

function csrf_token(): string
{
    csrf_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_request_token(): string
{
    if (!empty($_POST['csrf_token'])) {
        return (string) $_POST['csrf_token'];
    }
    if (!empty($_SERVER['HTTP_X_CSRF_TOKEN'])) {
        return (string) $_SERVER['HTTP_X_CSRF_TOKEN'];
    }
    if (!empty($_GET['csrf_token'])) {
        return (string) $_GET['csrf_token'];
    }
    return '';
}

function csrf_validate(string $token): bool
{
    csrf_start();
    if ($token === '' || empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

function csrf_require(bool $requireLogin = true): void
{
    if ($requireLogin && empty($_SESSION['id'])) {
        return;
    }
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    if ($method === 'GET' || $method === 'HEAD' || $method === 'OPTIONS') {
        return;
    }
    $token = csrf_request_token();
    if (!csrf_validate($token)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo 'CSRF invalido.';
        exit();
    }
}
