<?php
http_response_code(410);

$mensagem = 'Gateway desativado. Este sistema utiliza exclusivamente pagamentos via EFY.';

if (!headers_sent()) {
    $aceitaJson = strpos(strtolower((string)($_SERVER['HTTP_ACCEPT'] ?? '')), 'application/json') !== false;
    if ($aceitaJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'status' => 'error',
            'message' => $mensagem,
            'gateway' => 'EFY_ONLY'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    header('Content-Type: text/plain; charset=utf-8');
}

echo $mensagem;
exit;