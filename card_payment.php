<?php

require_once('../vendor/autoload.php');
require_once("../sistema/conexao.php");

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

@session_start();

header('Content-Type: application/json; charset=utf-8');
$json = file_get_contents('php://input');
$data = json_decode($json, true);


// Parâmetros recebidos via GET
$forma_de_pagamento = $data['payment_method'];
$billingType = strtoupper($forma_de_pagamento);
$quantidadeParcelas = $data['installments'] ?? 1;

//Busca dados para atualização da situação da matricula
$id_do_aluno = @$_SESSION['id'];
$id_do_curso_pag = $data['id_do_curso'];
$nome_curso_titulo = $data['nome_do_curso'];

$is_pacote = $_GET['pacote'] ?? null;

if ($is_pacote == 'Sim') {
    $curso_pacote = "Sim";
} else {
    $curso_pacote = "Não";
}



$options = require_once 'options.php';


// Configura����es da API da Ef�� (antiga GerenciaNet)
$clientId = env('EFI_CARD_CLIENT_ID', $options['clientId'] ?? '');
$clientSecret = env('EFI_CARD_CLIENT_SECRET', $options['clientSecret'] ?? '');

$sandbox = filter_var(env('EFI_CARD_SANDBOX', !empty($options['sandbox']) ? 'true' : 'false'), FILTER_VALIDATE_BOOLEAN);
$baseUrl = $sandbox ? 'https://api-pix-h.gerencianet.com.br' : 'https://api-pix.gerencianet.com.br';
$baseUrlBoleto = $sandbox ? 'https://cobrancas-h.api.efipay.com.br' : 'https://cobrancas.api.efipay.com.br';
$certificadoPath = env('EFI_CARD_CERT_PATH', $options['certificate'] ?? '');

$notificationBase = env('EFI_WEBHOOK_BASE_URL', '');
if ($notificationBase === '') {
    $notificationBase = $url_sistema;
}
$notificationBase = rtrim($notificationBase, '/') . '/';
if (stripos($notificationBase, 'https://') !== 0) {
    $notificationBase = 'https://sestedcursosvirtual.com/';
}



try {

  

    require_once 'card.php'; // arquivo que contém EFICreditCardPayment

    $cardPayment = new EFICreditCardPayment(
        $clientId,
        $clientSecret,
        $sandbox
    );

    // Preparar dados do pagamento com cartão
    $dadosCartao = [
        'valor' => floatval($valor_a_pagar ?? 0),
        'item_nome' => $nome_curso_titulo ?? 'Produto/Serviço',
        'quantidade' => 1,
        'nome' => $res2[0]['nome'] ?? '',
        'email' => $res2[0]['usuario'] ?? '',
        'cpf' => $res2[0]['cpf'] ?? '',
        'telefone' => $res2[0]['telefone'] ?? '',
        'credit_card_token' => $data['payment_token'], // token gerado pelo SDK JS da Gerencianet
        'installments' => $data['installments'] ?? 1,
        'street' => $data['street'] ?? null,
        'number' => $data['number'] ?? null,
        'neighborhood' => $data['neighborhood'] ?? null,
        'zipcode' => $data['zipcode'] ?? null,
        'city' => $data['city'] ?? null,
        'state' => $data['state'] ?? null,
        'notification_url' => $notificationBase . 'efi_webhook_boleto.php'
    ];

    // Validações básicas
    if (empty($dadosCartao['nome']) || empty($dadosCartao['email']) || empty($dadosCartao['cpf']) || empty($dadosCartao['credit_card_token'])) {
        throw new Exception('Dados do cartão incompletos.');
    }

    // Criar cobrança com cartão
    $resultado = $cardPayment->createCreditCardCharge($dadosCartao);

    
    // Formatar resposta
    $response = [
        'success' => true,
        'type' => 'CREDIT_CARD',
        'data' => [
            'charge_id' => $resultado['charge_id'],
            'status' => $resultado['status'],
            'total' => $resultado['total'],
            'payment_data' => $resultado['payment_data']
        ]
    ];

    echo json_encode($response);
    return;



    // // Armazenar informações do pagamento no banco de dados
    // $query = $pdo->prepare("INSERT INTO pagamentos_cartao (id_matricula, charge_id, valor, status) 
    //                         VALUES (?, ?, ?, ?)");
    // $query->execute([$id_venda, $resultado['charge_id'], $resultado['total'], $resultado['status']]);
    // $id_pagamento_cartao = $pdo->lastInsertId();

    // $update = $pdo->prepare("UPDATE matriculas SET forma_pgto = 'cartao_de_credito' WHERE id = ?");
    // $update->execute([$id_venda]);


} catch (Exception $e) {
    $response = [
        'success' => false,
        'type' => 'CREDIT_CARD',
        'error' => "Não foi possível processar o pagamento.",
        'data' => [
            'charge_id' => "CHARGE_ID",
            'status' => "STATUS",
            'total' => "TOTAL",
            'payment_data' => "PAYMENT_DATA"
        ]
    ];
    echo json_encode($response);
    return;
}

?>







