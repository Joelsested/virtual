<?php

require_once('../vendor/autoload.php');
require_once("../sistema/conexao.php");
require_once __DIR__ . '/../config/session.php';

function montarNotificationUrl(string $baseUrlSistema, string $relative, ?string $fallback = null): ?string
{
    // Dominio canônico do sistema virtual para callbacks EFY.
    $baseCanonica = rtrim((string) env('EFI_NOTIFICATION_BASE_URL', 'https://sestedcursosvirtual.com/'), '/') . '/';
    $baseAplicacao = rtrim($baseUrlSistema, '/') . '/';
    $rel = ltrim($relative, '/');

    $candidatas = [];

    if (!empty($fallback)) {
        $fallback = trim((string) $fallback);
        if (stripos($fallback, 'http://') === 0 || stripos($fallback, 'https://') === 0) {
            $candidatas[] = $fallback;
        } elseif (strpos($fallback, '/') === 0) {
            $candidatas[] = $baseAplicacao . ltrim($fallback, '/');
        }
    }

    $candidatas[] = $baseCanonica . $rel;
    $candidatas[] = $baseAplicacao . $rel;

    foreach ($candidatas as $url) {
        $url = trim((string) $url);
        if ($url !== '' && stripos($url, 'https://') === 0) {
            return $url;
        }
    }

    return null;
}

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

sested_session_start();

function renderBoletoEfHtml($valor, $codigoLinha, $linkDownload)
{
    $valorFormatado = number_format((float) $valor, 2, ',', '.');
    $codigoEscapado = htmlspecialchars($codigoLinha ?? '', ENT_QUOTES, 'UTF-8');
    $linkEscapado = $linkDownload ? htmlspecialchars($linkDownload, ENT_QUOTES, 'UTF-8') : '';
    $botaoAttr = $linkEscapado
        ? 'href="' . $linkEscapado . '" target="_blank"'
        : 'href="#" style="pointer-events:none;opacity:0.6"';

    echo '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pagamento por Boleto</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-gray-100 font-sans">
        <div class="container mx-auto px-4 py-10 max-w-3xl">
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h1 class="text-2xl font-bold text-center mb-4 text-blue-600">Pagamento por Boleto</h1>
                <div class="border-t border-b border-gray-200 py-4 mb-4">
                    <p class="text-center text-lg mb-2">Valor: <span class="font-bold">R$ ' . $valorFormatado . '</span></p>
                    <p class="text-center text-sm text-gray-600">Utilize o cÃ³digo abaixo para pagar o boleto ou faÃ§a download do PDF</p>
                </div>
                <div class="mb-6">
                    <div class="relative mb-4">
                        <input type="text" id="boleto-code" value="' . $codigoEscapado . '" readonly class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-sm" />
                        <button onclick="copiarCodigoBoleto()" class="absolute inset-y-0 right-0 px-4 bg-blue-500 text-white rounded-r-lg hover:bg-blue-600">
                            Copiar
                        </button>
                    </div>
                    <div class="text-center">
                        <a ' . $botaoAttr . ' class="inline-block bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                            </svg>
                            Download do Boleto
                        </a>
                    </div>
                </div>
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                O boleto tem vencimento em 7 dias. ApÃ³s o pagamento, a confirmaÃ§Ã£o pode levar atÃ© 3 dias Ãºteis.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <a href="../sistema/painel-aluno/index.php" class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                        Voltar ao Painel
                    </a>
                </div>
            </div>
        </div>
        <script>
            function copiarCodigoBoleto() {
                var codigoInput = document.getElementById("boleto-code");
                codigoInput.select();
                codigoInput.setSelectionRange(0, 99999);
                document.execCommand("copy");
                alert("CÃ³digo do boleto copiado para a Ã¡rea de transferÃªncia!");
            }
        </script>
    </body>
    </html>';
}

function buscarBoletoPendente($pdo, $idMatricula)
{
    $stmt = $pdo->prepare("SELECT * FROM pagamentos_boleto WHERE id_matricula = ? AND (status IS NULL OR status NOT IN ('paid','canceled')) ORDER BY id DESC LIMIT 1");
    $stmt->execute([$idMatricula]);
    $registro = $stmt->fetch(PDO::FETCH_ASSOC);
    return $registro ?: null;
}

function sincronizarBoletoPendenteComEfi(PDO $pdo, array $boletoExistente, array $config): array
{
    $idRegistro = (int) ($boletoExistente['id'] ?? 0);
    $chargeId = (string) ($boletoExistente['charge_id'] ?? '');
    if ($idRegistro <= 0 || $chargeId === '') {
        return $boletoExistente;
    }

    try {
        require_once __DIR__ . '/boleto.php';
        $boletoPayment = new EFIBoletoPayment(
            (string) ($config['client_id'] ?? ''),
            (string) ($config['client_secret'] ?? ''),
            (bool) ($config['sandbox'] ?? false)
        );

        $consulta = $boletoPayment->consultarCobranca($chargeId);
        $dadosCobranca = $consulta['data'] ?? [];
        $dadosBillet = $dadosCobranca['payment']['banking_billet'] ?? [];

        $urlAtualizada = (string) ($dadosBillet['billet_link'] ?? ($dadosBillet['link'] ?? ($boletoExistente['url_boleto'] ?? '')));
        $linhaAtualizada = (string) ($dadosBillet['line'] ?? ($dadosBillet['barcode'] ?? ($boletoExistente['linha_digitavel'] ?? '')));
        $statusAtualizado = (string) ($dadosCobranca['status'] ?? ($boletoExistente['status'] ?? 'pendente'));

        $stmt = $pdo->prepare("UPDATE pagamentos_boleto SET url_boleto = :url_boleto, linha_digitavel = :linha_digitavel, status = :status WHERE id = :id");
        $stmt->execute([
            ':url_boleto' => $urlAtualizada,
            ':linha_digitavel' => $linhaAtualizada,
            ':status' => $statusAtualizado,
            ':id' => $idRegistro
        ]);

        $boletoExistente['url_boleto'] = $urlAtualizada;
        $boletoExistente['linha_digitavel'] = $linhaAtualizada;
        $boletoExistente['status'] = $statusAtualizado;
    } catch (Throwable $e) {
        // Mantem dados locais para nao interromper o fluxo.
    }

    return $boletoExistente;
}

// ParÃ¢metros recebidos via GET
$forma_de_pagamento = $_GET['formaDePagamento'] ?? '';
$billingType = strtoupper($forma_de_pagamento);
// garante valor inteiro >= 1
$quantidadeParcelas = isset($_GET['quantidadeParcelas']) ? (int) $_GET['quantidadeParcelas'] : 1;
if ($quantidadeParcelas < 1) {
    $quantidadeParcelas = 1;
}
$maxParcelas = 24;
if ($quantidadeParcelas > $maxParcelas) {
    $quantidadeParcelas = $maxParcelas;
}
$tabelaMatricula = 'matriculas';

//Busca dados para atualizaÃ§Ã£o da situaÃ§Ã£o da matricula
$id_do_aluno = @$_SESSION['id'];
$id_do_curso_pag = $_GET['id_do_curso'];
$nome_curso_titulo = $_GET['nome_do_curso'];
$id_matricula_param = isset($_GET['id_matricula']) ? (int) $_GET['id_matricula'] : 0;

// fallback quando acessado sem parÃ¢metros (evita tela branca)
if (
    $forma_de_pagamento === '' ||
    empty($id_do_curso_pag) ||
    empty($nome_curso_titulo) ||
    empty($id_do_aluno)
) {
    echo '<!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pagamento</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-gray-100 font-sans">
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="bg-white shadow-lg rounded-lg p-6 max-w-lg w-full text-center">
                <h1 class="text-xl font-semibold text-gray-800 mb-2">Pagamento nÃ£o iniciado</h1>
                <p class="text-gray-600 mb-4">
                    Esta pÃ¡gina precisa ser acessada a partir do fluxo de compra. Volte ao seu painel e selecione a forma de pagamento.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="../sistema/painel-aluno/index.php?pagina=cursos" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        Ir para o Painel
                    </a>
                    <a href="../" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                        Voltar ao site
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>';
    exit;
}

$is_pacote = $_GET['pacote'] ?? null;

if ($is_pacote == 'Sim') {
    $curso_pacote = "Sim";
} else {
    $curso_pacote = "NÃ£o";
}

$form_post = [
    'forma_de_pagamento' => $billingType,
    'quantidadeParcelas' => $quantidadeParcelas,
    'id_do_aluno' => $id_do_aluno,
    'id_do_curso_pag' => $id_do_curso_pag,
    'nome_curso_titulo' => $nome_curso_titulo,
    'pacote' => $curso_pacote
];

$data = [];



$options = require_once 'options.php';

// ConfiguraÃ§Ãµes principais da EfÃ­ (PIX/boletos via SDK)
$config = [
    'client_id' => $options['clientId'],
    'client_secret' => $options['clientSecret'],
    'certificate_path' => $options['certificate'],
    'chave_pix' => $options['pixKey'] ?? ($chave_pix ?? ''),
    'sandbox' => $options['sandbox']
];


// Desconto PIX configurado no sistema
$queryPix = $pdo->query("SELECT desconto_pix FROM config");
$resPix = $queryPix->fetchAll(PDO::FETCH_ASSOC);
$descontoPix = isset($resPix[0]['desconto_pix']) ? (float) $resPix[0]['desconto_pix'] : 0.0;

// Dados do aluno
$query2 = $pdo->query("SELECT * FROM usuarios WHERE id = '$id_do_aluno'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$res3 = [];

if (@count($res2) > 0) {
    $id_pessoa = $res2[0]['id_pessoa'] ?? null;
    if ($id_pessoa) {
        $query3 = $pdo->query("SELECT * FROM alunos WHERE id = '$id_pessoa'");
        $res3 = $query3->fetchAll(PDO::FETCH_ASSOC);
    }
}

if (@count($res2) > 0 && @count($res3) > 0) {
    if (empty($res2[0]['cpf']) && !empty($res3[0]['cpf'])) {
        $res2[0]['cpf'] = $res3[0]['cpf'];
    }
    if (empty($res2[0]['telefone']) && !empty($res3[0]['telefone'])) {
        $res2[0]['telefone'] = $res3[0]['telefone'];
    }
    if (empty($res2[0]['usuario']) && !empty($res3[0]['email'])) {
        $res2[0]['usuario'] = $res3[0]['email'];
    }
    if (empty($res2[0]['nome']) && !empty($res3[0]['nome'])) {
        $res2[0]['nome'] = $res3[0]['nome'];
    }
}

$nome_aluno = $res3[0]['nome'] ?? ($res2[0]['nome'] ?? '');
$email_aluno = $res3[0]['email'] ?? ($res2[0]['usuario'] ?? '');
$cpf_aluno = isset($res3[0]['cpf']) ? preg_replace('/\D+/', '', $res3[0]['cpf']) : preg_replace('/\D+/', '', ($res2[0]['cpf'] ?? ''));
$nivel_responsavel_pelo_cadastro_do_aluno = $res3[0]['usuario'] ?? null;

// Buscar dados da matrÃ­cula
if ($id_matricula_param > 0) {
    $stmtMatricula = $pdo->prepare("SELECT * FROM {$tabelaMatricula} WHERE id = :id AND aluno = :aluno LIMIT 1");
    $stmtMatricula->execute([':id' => $id_matricula_param, ':aluno' => $id_do_aluno]);
    $res = $stmtMatricula->fetchAll(PDO::FETCH_ASSOC);
} else {
    $stmtMatricula = $pdo->prepare("SELECT * FROM {$tabelaMatricula} WHERE id_curso = :curso AND aluno = :aluno");
    $stmtMatricula->execute([':curso' => $id_do_curso_pag, ':aluno' => $id_do_aluno]);
    $res = $stmtMatricula->fetchAll(PDO::FETCH_ASSOC);
}

$matricula_encontrada = @count($res) > 0;

$valor_curso = 0.0;
$valor_a_pagar = 0.0;
$status_mat = '';
$id_venda = null;
$id_usuario_professor = null;
$valorF = '0,00';

if (!$matricula_encontrada) {
    echo '<!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pagamento</title>
        <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    </head>
    <body class="bg-gray-100 font-sans">
        <div class="min-h-screen flex items-center justify-center px-4">
            <div class="bg-white shadow-lg rounded-lg p-6 max-w-lg w-full text-center">
                <h1 class="text-xl font-semibold text-gray-800 mb-2">MatrÃ­cula nÃ£o encontrada</h1>
                <p class="text-gray-600 mb-4">
                    NÃ£o foi possÃ­vel localizar a matrÃ­cula deste curso. Volte ao painel e tente novamente.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="../sistema/painel-aluno/index.php?pagina=cursos" class="bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded">
                        Ir para o Painel
                    </a>
                    <a href="../" class="bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                        Voltar ao site
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>';
    exit;
}

if ($matricula_encontrada) {
    $valor_curso_raw = $res[0]['subtotal'] ?? $res[0]['valor'] ?? '0';
    $valor_curso = (float) str_replace(',', '.', preg_replace('/[^0-9,\.]/', '', (string) $valor_curso_raw));
    $status_mat = $res[0]['status'] ?? '';
    $id_venda = $res[0]['id'] ?? null;
    $id_usuario_professor = $res[0]['professor'] ?? null;
    $valorF = number_format($valor_curso, 2, ',', '.');

    // Valor a pagar conforme forma (PIX aplica desconto)
    $valor_a_pagar = $valor_curso;
    if ($billingType === 'PIX' && $descontoPix > 0) {
        $valor_a_pagar = $valor_curso - ($valor_curso * ($descontoPix / 100));
    }
}

$split_type = $res[0]['split'] ?? 1;

// Split: comissÃµes fixas + vendedor/responsÃ¡vel vinculado ao aluno.
$fixos_wallet_ids = [];
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
$consulta_comissoes_que_recebem_fixo = $pdo->query("SELECT nivel, porcentagem from comissoes where recebeSempre = 1 ");
$resposta_comissoes_que_recebem_fixo = $consulta_comissoes_que_recebem_fixo->fetchAll(PDO::FETCH_ASSOC);

$lista_cargos_recebem_fixo = [];
foreach ($resposta_comissoes_que_recebem_fixo as $registro) {
    if (!empty($registro['nivel'])) {
        $lista_cargos_recebem_fixo[] = $registro['nivel'];
    }
}

if (!empty($lista_cargos_recebem_fixo)) {
    $placeholders = implode(',', array_fill(0, count($lista_cargos_recebem_fixo), '?'));
    $sqlFixos = "SELECT usuarios.wallet_id, comissoes.porcentagem
                 FROM usuarios
                 INNER JOIN comissoes ON comissoes.nivel = usuarios.nivel
                 WHERE usuarios.nivel IN ($placeholders)
                   AND usuarios.wallet_id IS NOT NULL
                   AND usuarios.wallet_id <> ''";
    $stmtFixos = $pdo->prepare($sqlFixos);
    $stmtFixos->execute($lista_cargos_recebem_fixo);
    $lista_de_usuarios_que_recebem_fixo = $stmtFixos->fetchAll(PDO::FETCH_ASSOC);

    foreach ($lista_de_usuarios_que_recebem_fixo as $item) {
        $adicionarRepasse(
            $fixos_wallet_ids,
            (string) ($item['wallet_id'] ?? ''),
            (float) ($item['porcentagem'] ?? 0) * 100
        );
    }
}

// 2) Vendedor/responsÃ¡vel da venda: prioriza vÃ­nculo do aluno.
$id_usuario_vendedor = (int) ($res3[0]['usuario'] ?? 0);
if ($id_usuario_vendedor <= 0) {
    $id_usuario_vendedor = (int) ($res3[0]['responsavel_id'] ?? 0);
}
if ($id_usuario_vendedor <= 0) {
    // Fallback para o comportamento antigo.
    $id_usuario_vendedor = (int) ($id_usuario_professor ?? 0);
}

if ($id_usuario_vendedor > 0) {
    $stmtUsuarioVendedor = $pdo->prepare("SELECT id, nivel, id_pessoa, wallet_id FROM usuarios WHERE id = :id LIMIT 1");
    $stmtUsuarioVendedor->execute([':id' => $id_usuario_vendedor]);
    $usuarioVendedor = $stmtUsuarioVendedor->fetch(PDO::FETCH_ASSOC) ?: [];

    $nivelVendedor = (string) ($usuarioVendedor['nivel'] ?? '');
    $idPessoaVendedor = (int) ($usuarioVendedor['id_pessoa'] ?? 0);
    $walletIdVendedor = trim((string) ($usuarioVendedor['wallet_id'] ?? ''));
    $porcentagemVendedor = 0.0;

    if ($nivelVendedor === 'Vendedor' && $idPessoaVendedor > 0) {
        $stmtPct = $pdo->prepare("SELECT comissao FROM vendedores WHERE id = :id LIMIT 1");
        $stmtPct->execute([':id' => $idPessoaVendedor]);
        $porcentagemVendedor = (float) ($stmtPct->fetchColumn() ?: 0);
    } elseif ($nivelVendedor === 'Parceiro' && $idPessoaVendedor > 0) {
        $stmtPct = $pdo->prepare("SELECT comissao FROM parceiros WHERE id = :id LIMIT 1");
        $stmtPct->execute([':id' => $idPessoaVendedor]);
        $porcentagemVendedor = (float) ($stmtPct->fetchColumn() ?: 0);
    } elseif ($nivelVendedor !== '') {
        $stmtPct = $pdo->prepare("SELECT porcentagem FROM comissoes WHERE nivel = :nivel LIMIT 1");
        $stmtPct->execute([':nivel' => $nivelVendedor]);
        $porcentagemVendedor = (float) ($stmtPct->fetchColumn() ?: 0);
    }

    $adicionarRepasse($fixos_wallet_ids, $walletIdVendedor, $porcentagemVendedor * 100);
}

foreach ($fixos_wallet_ids as &$repasse) {
    $repasse['percentage'] = (int) round((float) ($repasse['percentage'] ?? 0));
}
unset($repasse);


// Configuracoes da API da Efi (antiga GerenciaNet)
$clientId = $options['clientId'];
$clientSecret = $options['clientSecret'];
$sandbox = $options['sandbox']; // true para ambiente de testes, false para produÃ§Ã£o
$baseUrl = $options['baseUrlPix'] ?? ($sandbox ? 'https://api-pix-h.gerencianet.com.br' : 'https://api-pix.gerencianet.com.br');
$baseUrlBoleto = $options['baseUrlBoleto'] ?? ($sandbox ? 'https://cobrancas-h.api.efipay.com.br' : 'https://cobrancas.api.efipay.com.br');
$certificadoPath = (string) ($options['certificate'] ?? '');

// AutenticaÃ§Ã£o - obtenÃ§Ã£o do token de acesso
function obterTokenEfi($clientId, $clientSecret, $baseUrl, $certificadoPath)
{
    $url = $baseUrl . '/oauth/token';

    $headers = [
        'Content-Type: application/json'
    ];

    $data = [
        'grant_type' => 'client_credentials'
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSLCERT => $certificadoPath,
        CURLOPT_SSLCERTPASSWD => '',
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERPWD => $clientId . ':' . $clientSecret
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        throw new Exception("Erro na autenticaÃ§Ã£o com a EfÃ­: " . $err);
    }

    $respostaDecodificada = json_decode($response, true);
    if (!isset($respostaDecodificada['access_token'])) {
        throw new Exception("Erro ao obter token da EfÃ­: " . json_encode($respostaDecodificada));
    }

    return $respostaDecodificada['access_token'];
}


// FunÃ§Ã£o para registrar cliente na EfÃ­
function registrarClienteEfi($token, $baseUrl, $certificadoPath, $nome, $cpf, $email)
{
    $url = $baseUrl . '/v2/customers';

    $dados = [
        'nome' => $nome,
        'cpf' => $cpf,
        'email' => $email
    ];

    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSLCERT => $certificadoPath,
        CURLOPT_SSLCERTPASSWD => '',
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        throw new Exception("Erro ao registrar cliente na EfÃ­: " . $err);
    }

    return json_decode($response, true);
}

// FunÃ§Ã£o para criar cobranÃ§a PIX
function criarCobrancaPix($token, $baseUrl, $certificadoPath, $cliente_id, $valor, $descricao, $id_curso, $repasses = [])
{
    // Valor em centavos
    $valorCentavos = intval($valor * 100);

    $url = $baseUrl . '/v2/cob';

    $dados = [
        'calendario' => [
            'expiracao' => 3600 // ExpiraÃ§Ã£o em segundos (1 hora)
        ],
        'devedor' => [
            'cpf' => $cliente_id,
            'nome' => 'Gabriel Ramos Luciano da Silva'
        ],
        'valor' => [
            'original' => number_format($valor, 2, '.', '')
        ],
        'chave' => '21e09baa-ccd9-447a-bc31-fcd760cef68c', // Sua chave PIX registrada na EfÃ­
        'solicitacaoPagador' => $descricao
    ];

    //     Adiciona splits se existirem
    if (!empty($repasses)) {
        $dados['repasses'] = $repasses;
    }

    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSLCERT => $certificadoPath,
        CURLOPT_SSLCERTPASSWD => '',
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        throw new Exception("Erro ao criar cobranÃ§a PIX: " . $err);
    }

    return json_decode($response, true);
}

// FunÃ§Ã£o para criar boleto
function criarBoleto($token, $baseUrl, $certificadoPath, $cliente_id, $valor, $descricao, $id_curso, $repasses = [])
{

    $data = [
        'token' => $token,
        'baseUrl' => $baseUrl,
        'cert' => $certificadoPath,
        'client_id' => $cliente_id,
        'valor' => $valor,
        'description' => $descricao,
        'id_curso' => $id_curso,
        'repasses' => $repasses = []
    ];

    //    return $data;
    // Valor em centavos
    $valorCentavos = intval($valor * 100);
    $dataVencimento = date('Y-m-d', strtotime('+7 days'));

    $url = $baseUrl . '/v1/charge/one-step';


    $dados = [
        'items' => [
            [
                'name' => $descricao,
                'value' => $valorCentavos,
                'amount' => 1
            ]
        ],
        'payment' => [
            'banking_billet' => [
                'customer' => [
                    'name' => 'Gabriel Ralusi',
                    'cpf' => '13294939663',
                    'email' => 'gabrielralusi@gmail.com'
                ],
                'expire_at' => $dataVencimento,
                'message' => $descricao,
                'custom_id' => $id_curso
            ]
        ]
    ];


    // Adiciona splits se existirem
//    if (!empty($repasses)) {
//        $dados['repasses'] = $repasses;
//    }

    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSLCERT => $certificadoPath,
        CURLOPT_SSLCERTPASSWD => '',
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    // echo '<pre>';
    // echo 'RESPOSTA - ';
    // echo json_encode($response, JSON_PRETTY_PRINT);
    // echo '</pre>';

    // echo '<pre>';
    // echo 'ERROS - ';
    // echo json_encode($err, JSON_PRETTY_PRINT);
    // echo '</pre>';

    // return;

    if ($err) {
        throw new Exception("Erro ao criar boleto: " . $err);
    }

    return json_decode($response, true);
}

// FunÃ§Ã£o para criar cobranÃ§a de cartÃ£o de crÃ©dito com parcelamento
function criarCobrancaCartao($token, $baseUrl, $certificadoPath, $cliente_id, $valor, $descricao, $id_curso, $parcelas, $repasses = [])
{
    // Valor em centavos
    $valorCentavos = intval($valor * 100);

    $url = $baseUrl . '/v2/charge';

    $dados = [
        'items' => [
            [
                'name' => 'Pagamento do curso: ' . $nome_curso_titulo,
                'value' => $valorCentavos,
                'amount' => 1
            ]
        ],
        'payment' => [
            'credit_card' => [
                'installments' => $parcelas,
                'payment_token' => '{{payment_token}}', // SerÃ¡ preenchido pelo checkout da EfÃ­
                'billing_address' => [] // SerÃ¡ preenchido pelo checkout da EfÃ­
            ]
        ],
        'customer' => [
            'name' => $nome_aluno,
            'cpf' => $cpf_aluno,
            'email' => $email_aluno
        ],
        'message' => $descricao,
        'custom_id' => $id_curso
    ];

    // Adiciona splits se existirem
    if (!empty($repasses)) {
        $dados['repasses'] = $repasses;
    }

    $headers = [
        'Authorization: Bearer ' . $token,
        'Content-Type: application/json'
    ];

    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($dados),
        CURLOPT_HTTPHEADER => $headers,
        CURLOPT_SSLCERT => $certificadoPath,
        CURLOPT_SSLCERTPASSWD => '',
        CURLOPT_SSL_VERIFYPEER => true
    ]);

    $response = curl_exec($curl);
    $err = curl_error($curl);
    curl_close($curl);

    if ($err) {
        throw new Exception("Erro ao criar cobranÃ§a de cartÃ£o: " . $err);
    }

    return json_decode($response, true);
}



// Tratamento para boletos (Ã  vista ou parcelado)
// Mesmo boleto Ã  vista gera 1 parcela na tabela para exibir em "Parcelas Boleto"
if (
    ($forma_de_pagamento == 'BOLETO_PARCELADO' && $quantidadeParcelas >= 1) ||
    ($forma_de_pagamento == 'BOLETO')
) {





    if (@count($res) > 0) {
        $id_matricula = $res[0]['id'];

        $consulta_dados_da_parcela = $pdo->query("SELECT * FROM boletos_parcelados WHERE id_matricula = '$id_matricula'");
        $resposta_consulta_dados_da_parcela = $consulta_dados_da_parcela->fetchAll(PDO::FETCH_ASSOC);

        if (@count($resposta_consulta_dados_da_parcela) > 0) {
            $stmtParcelasExistentes = $pdo->prepare("SELECT COUNT(*) FROM parcelas_geradas_por_boleto WHERE id_matricula = :id_matricula");
            $stmtParcelasExistentes->execute([':id_matricula' => $id_matricula]);
            $qtdParcelasExistentes = (int) $stmtParcelasExistentes->fetchColumn();

            if ($qtdParcelasExistentes > 0) {
                header("Location: ../sistema/painel-aluno/index.php?pagina=parcelas");
                exit();
            }
        }

        if ($forma_de_pagamento === 'BOLETO') {
            $boletoExistente = buscarBoletoPendente($pdo, $id_matricula);
            if ($boletoExistente) {
                $boletoExistente = sincronizarBoletoPendenteComEfi($pdo, $boletoExistente, $config);
                renderBoletoEfHtml(
                    $boletoExistente['valor'] ?? $valor_curso,
                    $boletoExistente['linha_digitavel'] ?? '',
                    $boletoExistente['url_boleto'] ?? ''
                );
                exit;
            }
        }

        // sempre registra a quantidade solicitada (para Ã  vista, quantidadeParcelas normalmente vem 1)
        $parcelasSolicitadas = (int) $quantidadeParcelas;
        if ($parcelasSolicitadas < 1) {
            $parcelasSolicitadas = 1;
        }

        $registro_dados_da_parcela = $pdo->query("INSERT INTO boletos_parcelados (qtd_parcelas, id_matricula) VALUES ('$parcelasSolicitadas', '$id_matricula')");
        $id_registro_dados_da_parcela = $pdo->lastInsertId();


        $valor_unitario_parcelas = round($valor_curso / $parcelasSolicitadas, 2);
        $valor_unitario_centavos = (int) round($valor_unitario_parcelas * 100);

        $notificationParcelado = montarNotificationUrl($url_sistema, "efi_webhook_boleto_parcelado.php", $options['notificationUrl'] ?? null);

        $dadosBoleto = [
            'valor' => $valor_unitario_centavos,
            'item_nome' => $nome_curso_titulo ?? 'Produto/ServiÃ§o',
            'quantidade' => 1,
            'nome' => $res2[0]['nome'] ?? '',
            'email' => $res2[0]['usuario'] ?? '',
            'cpf' => $res2[0]['cpf'] ?? '',
            'telefone' => $res2[0]['telefone'] ?? '69999694538',
            'vencimento' => $res2[0]['vencimento'] ?? '+7 days',
            // 'repasses' => $fixos_wallet_ids,
            // notification_url sÃ³ se for https vÃ¡lido
            'notification_url' => $notificationParcelado
        ];
        if (empty($dadosBoleto['notification_url'])) {
            unset($dadosBoleto['notification_url']);
        }
        
        if (!empty($fixos_wallet_ids)) {
            $dadosBoleto['repasses'] = $fixos_wallet_ids;
        }

        $payload = json_encode($dadosBoleto);

        for ($i = 0; $i < $parcelasSolicitadas; $i++) {
            $ordemParcela = $i + 1;
            $pdo->query("INSERT INTO parcelas_geradas_por_boleto (ordem_parcela, id_boleto_parcelado, valor_parcela, situacao, payload, id_matricula) VALUES ('$ordemParcela', '$id_registro_dados_da_parcela', '$valor_unitario_parcelas', '0', '$payload', '$id_matricula')");
        }

        header("Location: ../sistema/painel-aluno/index.php?pagina=parcelas");
        exit();
    }






    // echo '<pre>';
    // echo json_encode($dadosBoleto, JSON_PRETTY_PRINT);
    // return;
}



try {


    if ($billingType == 'PIX') {
        require_once 'pix.php';

        $pixPayment = new EFIPixPayment(
            $config['client_id'],
            $config['client_secret'],
            $config['certificate_path'],
            $config['sandbox']
        );

        // Preparar dados para PIX
        $dadosPix = [
            'cpf' => $data['cpf'] ?? '13294939663',
            'nome' => $data['nome'] ?? 'Gabriel Ramos',
            'valor' => floatval($valor_a_pagar ?? 0),
            'chave_pix' => $config['chave_pix'],
            'descricao' => $data['descricao'] ?? 'Pagamento PIX',
            'expiracao' => $data['expiracao'] ?? 3600
        ];



        // ValidaÃ§Ãµes especÃ­ficas do PIX
        if (empty($dadosPix['cpf'])) {
            throw new Exception('CPF Ã© obrigatÃ³rio para PIX');
        }
        if (empty($dadosPix['nome'])) {
            throw new Exception('Nome Ã© obrigatÃ³rio para PIX');
        }
        if ($dadosPix['valor'] <= 0) {
            throw new Exception('Valor deve ser maior que zero');
        }

        // Adicionar informaÃ§Ãµes adicionais se fornecidas
        if (isset($data['infoAdicionais'])) {
            $dadosPix['infoAdicionais'] = $data['infoAdicionais'];
        }

        // Criar cobranÃ§a PIX
        $resultado = $pixPayment->createPixCharge($dadosPix);

        // Formatar resposta PIX
        $response = [
            'success' => true,
            'type' => 'PIX',
            'data' => [
                'txid' => $resultado['txid'],
                'status' => $resultado['status'],
                'valor' => $resultado['valor'],
                'qr_code' => $resultado['qr_code'],
                'qr_code_image' => $resultado['qr_code_image'],
                'link_pagamento' => $resultado['link_pagamento'],
                'vencimento' => $resultado['vencimento']
            ]
        ];

        $qrCodeData = json_decode($qrResponse, true);

        $urlPagamento = $resultado['link_pagamento']; // Aqui continua o cÃ³digo
        $imagemQrCode = $resultado['qr_code_image'];
        $textoCopiaCola = $resultado['qr_code'];
        $txid = $resultado['txid'];

        // Armazenar informaÃ§Ãµes do pagamento PIX no banco de dados
        $query = $pdo->prepare("INSERT INTO pagamentos_pix (id_matricula, txid, qrcode_url, texto_copia_cola, valor, status) 
                               VALUES (?, ?, ?, ?, ?, 'pendente')");
        $query->execute([$id_venda, $txid, $imagemQrCode, $textoCopiaCola, $valor_a_pagar]);
        $id_pagamento_pix = $pdo->lastInsertId();

        // Atualizar forma_pgto na tabela matricula
        $update = $pdo->prepare("UPDATE {$tabelaMatricula} SET forma_pgto = 'PIX' WHERE id = ?");
        $update->execute([$id_venda]);


        echo '
        <!DOCTYPE html>
        <html lang="pt-br">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Pagamento PIX</title>
            <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
        </head>
        <body class="bg-gray-100 font-sans">
            <div class="container mx-auto px-4 py-10 max-w-3xl">
                <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                    <h1 class="text-2xl font-bold text-center mb-4 text-blue-600">Pagamento via PIX</h1>
                    <div class="border-t border-b border-gray-200 py-4 mb-4">
                        <p class="text-center text-lg mb-2">Valor com desconto: <span class="font-bold text-green-600">R$ ' . number_format($resultado['valor'], 2, ',', '.') . '</span></p>
                        <p class="text-center text-sm text-gray-600">Escaneie o QR Code abaixo ou copie o cÃ³digo PIX</p>
                    </div>
                    <div class="flex flex-col items-center justify-center mb-6">
                        <img src="' . $resultado['qr_code_image'] . '" alt="QR Code PIX" class="w-64 h-64 mb-4">
                        <div class="w-full">
                            <div class="relative">
                                <input type="text" id="pix-code" value="' . $resultado['link_pagamento'] . '" readonly class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-sm" />
                                <button onclick="copiarCodigo()" class="absolute inset-y-0 right-0 px-4 bg-blue-500 text-white rounded-r-lg hover:bg-blue-600">
                                    Copiar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    O pagamento Ã© processado automaticamente. ApÃ³s o pagamento, aguarde alguns instantes para a matrÃ­cula ser liberada.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <a href="../sistema/painel-aluno/index.php" class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">
                            Voltar ao Painel
                        </a>
                        <button id="verificar-pagamento" class="inline-block bg-blue-500 hover:bg-blue-600 text-white font-medium py-2 px-4 rounded ml-2">
                            Verificar Pagamento
                        </button>
                    </div>
                </div>
            </div>
            <script>
                function copiarCodigo() {
                    var codigoInput = document.getElementById("pix-code");
                    codigoInput.select();
                    codigoInput.setSelectionRange(0, 99999);
                    document.execCommand("copy");
                    alert("CÃ³digo PIX copiado para a Ã¡rea de transferÃªncia!");
                }
                
                document.getElementById("verificar-pagamento").addEventListener("click", function() {
                    // Fazer uma requisiÃ§Ã£o AJAX para verificar o status do pagamento
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "verificar_pagamento_pix.php?id_pagamento=' . $resultado['txid'] . '", true);
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState === 4 && xhr.status === 200) {
                            var response = JSON.parse(xhr.responseText);
                            if (response.status === "aprovado") {
                                alert("Pagamento confirmado! Redirecionando para o painel...");
                                window.location.href = "../sistema/painel-aluno/index.php";
                            } else {
                                alert("Pagamento ainda nÃ£o foi confirmado. Por favor, tente novamente em alguns instantes.");
                            }
                        }
                    };
                    xhr.send();
                });
            </script>
        </body>
        </html>';


        // return $response;

    } elseif ($billingType == 'BOLETO') {
        

        require_once 'boleto.php';

        $boletoPayment = new EFIBoletoPayment(
            $config['client_id'],
            $config['client_secret'],
            $config['sandbox']
        );

        $valorEmCentavos = (int) round(floatval($valor_curso ?? 0) * 100);
        if ($valorEmCentavos < 100) {
            $valorEmCentavos = 100;
        }

        // Preparar dados para Boleto
        $notificationBoleto = montarNotificationUrl($url_sistema, "efi_webhook_boleto.php", $options['notificationUrl'] ?? null);

        $dadosBoleto = [
            'valor' => $valorEmCentavos,
            'item_nome' => $nome_curso_titulo ?? 'Produto/ServiÃ§o',
            'quantidade' => 1,
            'nome' => $res2[0]['nome'] ?? '',
            'email' => $res2[0]['usuario'] ?? '',
            'cpf' => $res2[0]['cpf'] ?? '',
            'telefone' => $res2[0]['telefone'] ?? '69999694538',
            // 'nascimento' => $res2[0]['nascimento'] ?? '27/10/1995',
            'vencimento' => $res2[0]['vencimento'] ?? '+7 days',
            // 'repasses' => $fixos_wallet_ids,
            'notification_url' => $notificationBoleto
        ];
        if (empty($dadosBoleto['notification_url'])) {
            unset($dadosBoleto['notification_url']);
        }
        
        if (!empty($fixos_wallet_ids)) {
            $dadosBoleto['repasses'] = $fixos_wallet_ids;
        }


        // echo '<pre>';
        // echo json_encode($dadosBoleto, JSON_PRETTY_PRINT);
        // echo '</pre>';
        // return;

        // ValidaÃ§Ãµes especÃ­ficas do Boleto
        if (empty($dadosBoleto['nome'])) {
            throw new Exception('Nome Ã© obrigatÃ³rio para Boleto');
        }
        if (empty($dadosBoleto['email'])) {
            throw new Exception('Email Ã© obrigatÃ³rio para Boleto');
        }
        if (empty($dadosBoleto['cpf'])) {
            throw new Exception('CPF Ã© obrigatÃ³rio para Boleto');
        }
        if ($dadosBoleto['valor'] <= 0) {
            throw new Exception('Valor deve ser maior que zero');
        }

        // Adicionar endereÃ§o se fornecido
        if (isset($data['endereco'])) {
            $dadosBoleto['endereco'] = $data['endereco'];
        }

        // ConfiguraÃ§Ãµes opcionais do boleto
        if (isset($data['instrucoes'])) {
            $dadosBoleto['instrucoes'] = $data['instrucoes'];
        }
        if (isset($data['multa'])) {
            $dadosBoleto['multa'] = floatval($data['multa']);
        }
        if (isset($data['juros'])) {
            $dadosBoleto['juros'] = floatval($data['juros']);
        }
        if (isset($data['desconto'])) {
            $dadosBoleto['desconto'] = $data['desconto'];
        }
        if (isset($data['metadata'])) {
            $dadosBoleto['metadata'] = $data['metadata'];
        }



        // Criar cobranÃ§a Boleto
        try {
            $resultado = $boletoPayment->createBoletoCharge($dadosBoleto);
        } catch (Exception $e) {
            if (strpos($e->getMessage(), '4600210') !== false) {
                $boletoExistente = buscarBoletoPendente($pdo, $id_venda);
                if ($boletoExistente) {
                    $boletoExistente = sincronizarBoletoPendenteComEfi($pdo, $boletoExistente, $config);
                    renderBoletoEfHtml(
                        $boletoExistente['valor'] ?? $valor_curso,
                        $boletoExistente['linha_digitavel'] ?? '',
                        $boletoExistente['url_boleto'] ?? ''
                    );
                    exit;
                }
            }
            throw $e;
        }


        // Formatar resposta Boleto
        $response = [
            'success' => true,
            'type' => 'BOLETO',
            'data' => [
                'charge_id' => $resultado['charge_id'],
                'status' => $resultado['status'],
                'total' => $resultado['total'],
                'vencimento' => $resultado['vencimento'],
                'linha_digitavel' => $resultado['linha_digitavel'],
                'codigo_barras' => $resultado['codigo_barras'],
                'link_boleto' => $resultado['link_boleto'],
                'pdf_boleto' => $resultado['pdf_boleto']
            ],
            'payment_data' => $resultado['payment_data']
        ];

        $payment_data = $resultado['payment_data']['data']['payment']['banking_billet'];

        // Recuperar URL do boleto e linha digitÃ¡vel
        $urlPagamento = $payment_data['billet_link'] ?? ($payment_data['link'] ?? '');
        $urlBoleto = $payment_data['pdf']['charge'] ?? '';
        $linhaDigitavel = $payment_data['line'] ?? ($payment_data['barcode'] ?? ($resultado['linha_digitavel'] ?? ''));
        $urlPersistencia = $urlPagamento !== ''
            ? $urlPagamento
            : ($urlBoleto !== '' ? $urlBoleto : ($resultado['link_boleto'] ?? ''));
        // $nossoNumero = '11961722303';
        $nossoNumero = $resultado['charge_id'];

        // $das = [
        //     'url' => $urlPagamento,
        //     'linha' => $linhaDigitavel,
        //     'numero' => $nossoNumero
        // ];

        // echo '<pre>';
        // echo json_encode($das, JSON_PRETTY_PRINT);
        // echo '</pre>';
        // return;

        // // Armazenar informaÃ§Ãµes do boleto no banco de dados
        $query = $pdo->prepare("INSERT INTO pagamentos_boleto (id_matricula, charge_id, nosso_numero, url_boleto, linha_digitavel, valor, status) 
                               VALUES (?, ?, ?, ?, ?, ?, 'pendente')");
        $query->execute([$id_venda, $nossoNumero, $nossoNumero, $urlPersistencia, $linhaDigitavel, $resultado['total']]);
        $id_pagamento_boleto = $pdo->lastInsertId();

        // Atualizar forma_pgto na tabela matricula
        $update = $pdo->prepare("UPDATE {$tabelaMatricula} SET forma_pgto = 'BOLETO' WHERE id = ?");
        $update->execute([$id_venda]);

        renderBoletoEfHtml(
            $valor_curso,
            $linhaDigitavel,
            $urlPersistencia
        );

    } elseif ($billingType == 'CONSULTAR_BOLETO') {




    }

} catch (Exception $e) {

}

// Arquivo de callback para webhook (a ser implementado em arquivo separado)
// Este arquivo receberÃ¡ notificaÃ§Ãµes da EfÃ­ quando o status do pagamento for alterado

/*
 * Arquivo webhook_efi.php (implementar separadamente)
 * Este arquivo receberÃ¡ as notificaÃ§Ãµes da EfÃ­ sobre mudanÃ§as no status dos pagamentos
 * e deverÃ¡ atualizar os status no banco de dados, liberar acesso ao curso quando confirmado, etc.
 */
?>
