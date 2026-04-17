<?php

require_once('../vendor/autoload.php');
require_once('../sistema/conexao.php');
require_once('../config/env.php');

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

@session_start();
header('Content-Type: application/json; charset=utf-8');

$json = file_get_contents('php://input');
$data = json_decode($json, true);
if (!is_array($data)) {
    echo json_encode([
        'success' => false,
        'type' => 'CREDIT_CARD',
        'error' => 'Payload invalido.'
    ]);
    exit;
}

function montarNotificationUrlLocal(string $baseSistema, string $relativePath = 'efi_webhooks.php', ?string $fallback = null): ?string
{
    $candidatas = [];

    $baseSistema = trim($baseSistema);
    if ($baseSistema !== '') {
        $candidatas[] = rtrim($baseSistema, '/') . '/' . ltrim($relativePath, '/');
    }

    $fallback = trim((string) $fallback);
    if ($fallback !== '') {
        if (preg_match('#^https://[^/]+/?$#i', $fallback)) {
            $fallback = rtrim($fallback, '/') . '/' . ltrim($relativePath, '/');
        }
        $candidatas[] = $fallback;
    }

    foreach ($candidatas as $url) {
        if (stripos($url, 'https://') === 0) {
            return $url;
        }
    }

    return null;
}

function aprovarMatriculaCartaoLocal(int $idMatricula, float $valorTotal, string $formaPgto, string $tabelaOrigem = 'matriculas', string $obs = ''): bool
{
    global $pdo, $dia_pgto_comissao, $taxa_mp, $taxa_boleto, $taxa_paypal;

    if ($idMatricula <= 0) {
        return false;
    }

    if (!isset($dia_pgto_comissao) || $dia_pgto_comissao === '' || $dia_pgto_comissao === null) {
        $dia_pgto_comissao = date('d');
    }
    if (!isset($taxa_mp)) {
        $taxa_mp = 0;
    }
    if (!isset($taxa_boleto)) {
        $taxa_boleto = 0;
    }
    if (!isset($taxa_paypal)) {
        $taxa_paypal = 0;
    }

    $arquivoAprovar = __DIR__ . '/../sistema/painel-admin/paginas/matriculas/aprovar.php';
    if (!file_exists($arquivoAprovar)) {
        return false;
    }

    $postOriginal = $_POST ?? [];
    $_POST = [
        'forma_pgto' => $formaPgto,
        'valor' => number_format($valorTotal, 2, '.', ''),
        'obs' => $obs,
        'cartao' => 'Nao',
        'id_mat' => $idMatricula,
    ];

    if ($tabelaOrigem !== 'matriculas') {
        $_POST['tabela_origem'] = $tabelaOrigem;
    }

    $cwdOriginal = getcwd();
    $dirAprovar = dirname($arquivoAprovar);
    if ($dirAprovar !== '') {
        @chdir($dirAprovar);
    }

    ob_start();
    require basename($arquivoAprovar);
    ob_end_clean();

    if ($cwdOriginal !== false) {
        @chdir($cwdOriginal);
    }
    $_POST = $postOriginal;

    return true;
}

function registrarLogPagamentoLocal(PDO $pdo, int $idMatricula, string $descricao, string $formaPgto, float $valor, string $status, array $payload = []): void
{
    if ($idMatricula <= 0) {
        return;
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO logs_pagamentos (id_matricula, data, descricao, forma_pagamento, valor, status, json_response) VALUES (:id_matricula, NOW(), :descricao, :forma_pagamento, :valor, :status, :json_response)");
        $stmt->execute([
            ':id_matricula' => $idMatricula,
            ':descricao' => $descricao,
            ':forma_pagamento' => $formaPgto,
            ':valor' => $valor,
            ':status' => $status,
            ':json_response' => !empty($payload) ? json_encode($payload, JSON_UNESCAPED_UNICODE) : null,
        ]);
    } catch (Throwable $e) {
        // Nao interrompe o pagamento por falha de log.
    }
}

$formaDePagamento = strtoupper((string) ($data['payment_method'] ?? 'CREDIT_CARD'));
$isRecorrente = ($formaDePagamento === 'DEBIT_CARD');
$installments = (int) ($data['installments'] ?? 1);
if ($installments < 1) {
    $installments = 1;
}
if (!$isRecorrente && $installments > 12) {
    $installments = 12;
}
if ($isRecorrente && $installments > 24) {
    $installments = 24;
}
if ($isRecorrente && $installments < 2) {
    $installments = 2;
}

$idAlunoUsuario = (int) (@$_SESSION['id'] ?? 0);
$idCurso = (int) ($data['id_do_curso'] ?? 0);
$idMatricula = (int) ($data['id_matricula'] ?? 0);
$nomeCurso = (string) ($data['nome_do_curso'] ?? 'Produto/Servico');
$tipo = (string) ($data['tipo'] ?? 'cursos');

if ($idAlunoUsuario <= 0 || $idCurso <= 0) {
    echo json_encode([
        'success' => false,
        'type' => $isRecorrente ? 'RECURRING_CARD' : 'CREDIT_CARD',
        'error' => 'Dados obrigatorios ausentes.'
    ]);
    exit;
}

$tabelaMatricula = 'matriculas';
if ($tipo === 'tecnicos') {
    $tabelaMatricula = 'matriculas_tecnicos';
} elseif ($tipo === 'profissionalizantes') {
    $tabelaMatricula = 'matriculas_profissionalizantes';
}

$options = require_once 'options.php';

$stmtUsuario = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
$stmtUsuario->execute([':id' => $idAlunoUsuario]);
$usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC) ?: null;

if (!$usuario) {
    echo json_encode([
        'success' => false,
        'type' => $isRecorrente ? 'RECURRING_CARD' : 'CREDIT_CARD',
        'error' => 'Usuario do aluno nao encontrado.'
    ]);
    exit;
}

$idPessoa = (int) ($usuario['id_pessoa'] ?? 0);
$stmtAluno = $pdo->prepare('SELECT * FROM alunos WHERE id = :id LIMIT 1');
$stmtAluno->execute([':id' => $idPessoa]);
$aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC) ?: null;

if (!$aluno) {
    echo json_encode([
        'success' => false,
        'type' => $isRecorrente ? 'RECURRING_CARD' : 'CREDIT_CARD',
        'error' => 'Cadastro do aluno nao encontrado.'
    ]);
    exit;
}

$sqlMat = "SELECT * FROM {$tabelaMatricula} WHERE aluno = :aluno";
$paramsMat = [':aluno' => $idAlunoUsuario];
if ($idMatricula > 0) {
    $sqlMat .= " AND id = :id";
    $paramsMat[':id'] = $idMatricula;
} else {
    $sqlMat .= " AND id_curso = :id_curso";
    $paramsMat[':id_curso'] = $idCurso;
}
$sqlMat .= " LIMIT 1";
$stmtMat = $pdo->prepare($sqlMat);
$stmtMat->execute($paramsMat);
$matricula = $stmtMat->fetch(PDO::FETCH_ASSOC) ?: null;

if (!$matricula) {
    echo json_encode([
        'success' => false,
        'type' => $isRecorrente ? 'RECURRING_CARD' : 'CREDIT_CARD',
        'error' => 'Matricula nao encontrada.'
    ]);
    exit;
}

$valorCurso = (float) ($matricula['subtotal'] ?? 0);
if ($valorCurso <= 0) {
    $valorCurso = (float) ($matricula['valor'] ?? 0);
}

$descontoPix = 0.0;
$resPix = $pdo->query('SELECT desconto_pix FROM config LIMIT 1')->fetch(PDO::FETCH_ASSOC);
if ($resPix && isset($resPix['desconto_pix'])) {
    $descontoPix = (float) $resPix['desconto_pix'];
}

if ($formaDePagamento === 'PIX') {
    $valorAPagar = $valorCurso - ($valorCurso * ($descontoPix / 100));
} elseif (!$isRecorrente) {
    // Repassa as taxas de cartao de credito para o cliente e preserva o valor liquido da venda.
    $taxaFixaCartaoCredito = (float) env('EFI_CARTAO_CREDITO_TAXA_FIXA', env('EFI_CARD_CREDIT_FIXED_FEE', '0.29'));
    $taxaPercentualCartaoCredito = (float) env('EFI_CARTAO_CREDITO_TAXA_PERCENTUAL', env('EFI_CARD_CREDIT_PERCENT_FEE', '4.99'));
    $percentualFracaoCartaoCredito = max(0.0, min(0.99, $taxaPercentualCartaoCredito / 100));

    if ($valorCurso > 0) {
        $valorAPagar = ($valorCurso + $taxaFixaCartaoCredito) / (1 - $percentualFracaoCartaoCredito);
        $valorAPagar = round(max($valorCurso, $valorAPagar), 2);
    } else {
        $valorAPagar = $valorCurso;
    }
} else {
    $valorAPagar = $valorCurso;
}

// Comissoes: fixos + vendedor vinculado ao aluno (alunos.usuario/responsavel_id).
$fixosWalletIds = [];

$adicionarRepasse = function (&$lista, string $walletId, float $percentualEmBase100) {
    $walletId = trim($walletId);
    if ($walletId === '' || $percentualEmBase100 <= 0) {
        return;
    }

    foreach ($lista as &$item) {
        if (($item['payee_code'] ?? '') === $walletId) {
            $item['percentage'] += $percentualEmBase100;
            return;
        }
    }

    $lista[] = [
        'payee_code' => $walletId,
        'percentage' => $percentualEmBase100
    ];
};

// 1) Fixos (recebeSempre = 1)
$listaCargosRecebemFixo = [];
$resComissoesFixas = $pdo->query("SELECT nivel FROM comissoes WHERE recebeSempre = 1")->fetchAll(PDO::FETCH_ASSOC);
foreach ($resComissoesFixas as $registro) {
    if (!empty($registro['nivel'])) {
        $listaCargosRecebemFixo[] = $registro['nivel'];
    }
}

if (!empty($listaCargosRecebemFixo)) {
    $placeholders = implode(',', array_fill(0, count($listaCargosRecebemFixo), '?'));
    $sql = "SELECT usuarios.wallet_id, comissoes.porcentagem
            FROM usuarios
            INNER JOIN comissoes ON comissoes.nivel = usuarios.nivel
            WHERE usuarios.nivel IN ($placeholders)
              AND usuarios.wallet_id IS NOT NULL
              AND usuarios.wallet_id <> ''";
    $stmtFixos = $pdo->prepare($sql);
    $stmtFixos->execute($listaCargosRecebemFixo);
    $usuariosFixos = $stmtFixos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($usuariosFixos as $item) {
        $adicionarRepasse(
            $fixosWalletIds,
            (string) ($item['wallet_id'] ?? ''),
            (float) ($item['porcentagem'] ?? 0) * 100
        );
    }
}

// 2) Vendedor da venda: prioriza vinculo do aluno (usuario/responsavel_id)
$idUsuarioVendedor = (int) ($aluno['usuario'] ?? 0);
if ($idUsuarioVendedor <= 0) {
    $idUsuarioVendedor = (int) ($aluno['responsavel_id'] ?? 0);
}
if ($idUsuarioVendedor <= 0) {
    // Fallback para o antigo comportamento (usuario professor da matricula)
    $idUsuarioVendedor = (int) ($matricula['professor'] ?? 0);
}

if ($idUsuarioVendedor > 0) {
    $stmtUsuarioVendedor = $pdo->prepare('SELECT id, nivel, id_pessoa, wallet_id FROM usuarios WHERE id = :id LIMIT 1');
    $stmtUsuarioVendedor->execute([':id' => $idUsuarioVendedor]);
    $usuarioVendedor = $stmtUsuarioVendedor->fetch(PDO::FETCH_ASSOC) ?: [];

    $nivelVendedor = (string) ($usuarioVendedor['nivel'] ?? '');
    $idPessoaVendedor = (int) ($usuarioVendedor['id_pessoa'] ?? 0);
    $walletIdVendedor = trim((string) ($usuarioVendedor['wallet_id'] ?? ''));
    $porcentagemVendedor = 0.0;

    if ($nivelVendedor === 'Vendedor' && $idPessoaVendedor > 0) {
        $stmtPct = $pdo->prepare('SELECT comissao FROM vendedores WHERE id = :id LIMIT 1');
        $stmtPct->execute([':id' => $idPessoaVendedor]);
        $porcentagemVendedor = (float) ($stmtPct->fetchColumn() ?: 0);
    } elseif ($nivelVendedor === 'Parceiro' && $idPessoaVendedor > 0) {
        $stmtPct = $pdo->prepare('SELECT comissao FROM parceiros WHERE id = :id LIMIT 1');
        $stmtPct->execute([':id' => $idPessoaVendedor]);
        $porcentagemVendedor = (float) ($stmtPct->fetchColumn() ?: 0);
    } elseif ($nivelVendedor !== '') {
        // Fallback por nivel na tabela comissoes
        $stmtPct = $pdo->prepare('SELECT porcentagem FROM comissoes WHERE nivel = :nivel LIMIT 1');
        $stmtPct->execute([':nivel' => $nivelVendedor]);
        $porcentagemVendedor = (float) ($stmtPct->fetchColumn() ?: 0);
    }

    $adicionarRepasse($fixosWalletIds, $walletIdVendedor, $porcentagemVendedor * 100);
}

// Arredonda para inteiro (formato esperado pela Efí, ex.: 1500 = 15%)
foreach ($fixosWalletIds as &$rep) {
    $rep['percentage'] = (int) round((float) ($rep['percentage'] ?? 0));
}
unset($rep);

$nomeAluno = trim((string) ($aluno['nome'] ?? ''));
$emailAluno = trim((string) ($aluno['email'] ?? ($usuario['email'] ?? ($usuario['usuario'] ?? ''))));
$cpfAluno = preg_replace('/\D/', '', (string) ($aluno['cpf'] ?? ($usuario['cpf'] ?? '')));
$telefoneAluno = preg_replace('/\D/', '', (string) ($aluno['telefone'] ?? ($usuario['telefone'] ?? '')));

$notificationUrl = montarNotificationUrlLocal((string) ($url_sistema ?? ''), 'efi_webhooks.php', (string) ($options['notificationUrl'] ?? ''));

$clientId = trim((string) ($options['clientId'] ?? ''));
$clientSecret = trim((string) ($options['clientSecret'] ?? ''));
$sandbox = (bool) ($options['sandbox'] ?? false);

if ($clientId === '' || $clientSecret === '') {
    echo json_encode([
        'success' => false,
        'type' => $isRecorrente ? 'RECURRING_CARD' : 'CREDIT_CARD',
        'error' => 'Credenciais EFI nao configuradas.'
    ]);
    exit;
}

require_once 'card.php';

try {
    $cardPayment = new EFICreditCardPayment($clientId, $clientSecret, $sandbox);

    $streetPayload = trim((string) ($data['street'] ?? ($data['address'] ?? '')));
    $zipcodePayload = preg_replace('/\D/', '', (string) ($data['zipcode'] ?? ($data['cep'] ?? '')));
    $numberPayload = trim((string) ($data['number'] ?? ''));
    $neighborhoodPayload = trim((string) ($data['neighborhood'] ?? ''));
    $cityPayload = trim((string) ($data['city'] ?? ''));
    $statePayload = strtoupper(trim((string) ($data['state'] ?? '')));

    $dadosCartao = [
        'valor' => $valorAPagar,
        'item_nome' => $nomeCurso,
        'quantidade' => 1,
        'nome' => $nomeAluno,
        'email' => $emailAluno,
        'cpf' => $cpfAluno,
        'telefone' => $telefoneAluno,
        'credit_card_token' => (string) ($data['payment_token'] ?? ''),
        'installments' => $installments,
        'street' => $streetPayload,
        'number' => $numberPayload,
        'neighborhood' => $neighborhoodPayload,
        'zipcode' => $zipcodePayload,
        'city' => $cityPayload,
        'state' => $statePayload,
        'notification_url' => $notificationUrl,
    ];

    if (!empty($fixosWalletIds)) {
        $dadosCartao['repasses'] = $fixosWalletIds;
    }

    if (empty($dadosCartao['nome']) || empty($dadosCartao['email']) || empty($dadosCartao['cpf']) || empty($dadosCartao['credit_card_token'])) {
        throw new Exception('Dados do cliente/cartao incompletos.');
    }
    if (
        empty($dadosCartao['street']) ||
        empty($dadosCartao['number']) ||
        empty($dadosCartao['neighborhood']) ||
        empty($dadosCartao['city']) ||
        empty($dadosCartao['state']) ||
        strlen((string) $dadosCartao['zipcode']) !== 8
    ) {
        throw new Exception('Endereco incompleto: informe rua, numero, bairro, cidade, UF e CEP com 8 digitos.');
    }

    if (empty($dadosCartao['notification_url'])) {
        unset($dadosCartao['notification_url']);
    }

    if ($isRecorrente) {
        $resultado = $cardPayment->createRecurringSubscription($dadosCartao);
    } else {
        $resultado = $cardPayment->createCreditCardCharge($dadosCartao);
    }

    $formaPgtoMatricula = $isRecorrente ? 'CARTAO_RECORRENTE' : 'CARTAO_DE_CREDITO';
    $statusGateway = strtoupper((string) ($resultado['status'] ?? ''));
    $descricaoLog = $isRecorrente ? 'Pagamento cartao recorrente aprovado' : 'Pagamento cartao de credito aprovado';
    $idMatriculaAtual = (int) ($matricula['id'] ?? 0);

    $stmtForma = $pdo->prepare("UPDATE {$tabelaMatricula} SET forma_pgto = :forma_pgto WHERE id = :id");
    $stmtForma->execute([
        ':forma_pgto' => $formaPgtoMatricula,
        ':id' => $idMatriculaAtual,
    ]);

    registrarLogPagamentoLocal(
        $pdo,
        $idMatriculaAtual,
        $descricaoLog,
        $formaPgtoMatricula,
        (float) ($resultado['total'] ?? $valorAPagar ?? 0),
        $statusGateway !== '' ? $statusGateway : 'APROVADO',
        (array) ($resultado['payment_data'] ?? [])
    );

    $deveAprovarMatricula = ((string) ($matricula['status'] ?? '') !== 'Matriculado');
    $matriculaAprovada = false;
    if ($deveAprovarMatricula) {
        $matriculaAprovada = aprovarMatriculaCartaoLocal(
            $idMatriculaAtual,
            (float) ($valorCurso ?? $valorAPagar ?? 0),
            $formaPgtoMatricula,
            $tabelaMatricula,
            $descricaoLog
        );
    }

    echo json_encode([
        'success' => true,
        'type' => $isRecorrente ? 'RECURRING_CARD' : 'CREDIT_CARD',
        'data' => [
            'charge_id' => $resultado['charge_id'] ?? null,
            'subscription_id' => $resultado['subscription_id'] ?? null,
            'status' => $resultado['status'] ?? null,
            'total' => $resultado['total'] ?? null,
            'payment_data' => $resultado['payment_data'] ?? null,
        ],
        'matricula_processada' => $deveAprovarMatricula ? $matriculaAprovada : true
    ]);
    exit;
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'type' => $isRecorrente ? 'RECURRING_CARD' : 'CREDIT_CARD',
        'error' => 'Nao foi possivel processar o pagamento.',
        'detail' => $e->getMessage(),
        'data' => [
            'charge_id' => 'CHARGE_ID',
            'status' => 'STATUS',
            'total' => 'TOTAL',
            'payment_data' => 'PAYMENT_DATA',
        ]
    ]);
    exit;
}
