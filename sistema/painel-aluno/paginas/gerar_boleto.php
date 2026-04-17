<?php
require_once('../../../vendor/autoload.php');
require_once('../../conexao.php');
require_once '../../../efi/boleto.php';

$options = require_once '../../../efi/options.php';

$config = [
    'client_id' => $options['clientId'],
    'client_secret' => $options['clientSecret'],
    'certificate_path' => $options['certificate'],
    'chave_pix' => env('EFI_PIX_KEY', $chave_pix ?? ''),
    'sandbox' => $options['sandbox']
];

$data = $_POST;
$pay = (string) ($data['payload'] ?? '');
$payload = json_decode($pay, true);
if (!is_array($payload)) {
    $payload = [];
}

function normalizarTelefoneEfi($telefone): string
{
    $digits = preg_replace('/\D/', '', (string) $telefone);
    if ($digits === '') {
        return '';
    }
    if (strlen($digits) >= 12 && strpos($digits, '55') === 0) {
        $digits = substr($digits, 2);
    }
    while (strlen($digits) > 10 && $digits[0] === '0') {
        $digits = substr($digits, 1);
    }
    if (!preg_match('/^[1-9]{2}9?[0-9]{8}$/', $digits)) {
        return '';
    }
    return $digits;
}

function renderBoletoHtml(float $valor, string $codigoLinha, string $linkDownload): void
{
    $valorFormatado = number_format($valor, 2, ',', '.');
    $codigoEsc = htmlspecialchars($codigoLinha, ENT_QUOTES, 'UTF-8');
    $linkEsc = htmlspecialchars($linkDownload, ENT_QUOTES, 'UTF-8');
    $linkAttr = $linkEsc !== '' ? 'href="' . $linkEsc . '" target="_blank"' : 'href="#" style="pointer-events:none;opacity:0.6"';

    echo '<!DOCTYPE html>
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
                <p class="text-center text-sm text-gray-600">Utilize o codigo abaixo para pagar o boleto ou faca download do PDF</p>
            </div>
            <div class="mb-6">
                <div class="relative mb-4">
                    <input type="text" id="boleto-code" value="' . $codigoEsc . '" readonly class="w-full p-3 border border-gray-300 rounded-lg bg-gray-50 text-sm" />
                    <button onclick="copiarCodigoBoleto()" class="absolute inset-y-0 right-0 px-4 bg-blue-500 text-white rounded-r-lg hover:bg-blue-600">Copiar</button>
                </div>
                <div class="text-center">
                    <a ' . $linkAttr . ' class="inline-block bg-green-500 hover:bg-green-600 text-white font-medium py-2 px-4 rounded">
                        Download do Boleto
                    </a>
                </div>
            </div>
            <div class="text-center">
                <a href="/sistema/painel-aluno/index.php?pagina=parcelas" class="inline-block bg-gray-500 hover:bg-gray-600 text-white font-medium py-2 px-4 rounded">Voltar ao Painel</a>
            </div>
        </div>
    </div>
    <script>
        function copiarCodigoBoleto() {
            var codigoInput = document.getElementById("boleto-code");
            codigoInput.select();
            codigoInput.setSelectionRange(0, 99999);
            document.execCommand("copy");
            alert("Codigo do boleto copiado para a area de transferencia!");
        }
    </script>
</body>
</html>';
}

function buscarParcelaDoAluno(PDO $pdo, int $idParcela, int $idMatricula, int $idAluno): ?array
{
    $sql = "SELECT pgb.*
              FROM parcelas_geradas_por_boleto pgb
         JOIN matriculas m ON m.id = pgb.id_matricula AND m.aluno = :aluno
             WHERE pgb.id = :id_parcela
               AND pgb.id_matricula = :id_matricula
             LIMIT 1";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':aluno' => $idAluno,
        ':id_parcela' => $idParcela,
        ':id_matricula' => $idMatricula,
    ]);

    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

function atualizarMatriculaFormaPgto(PDO $pdo, int $idMatricula, int $idAluno, string $formaPgto, string $linkBoleto): void
{
    $stmt = $pdo->prepare("UPDATE matriculas SET id_asaas = :id_asaas, forma_pgto = :forma_pgto WHERE id = :id AND aluno = :aluno");
    $stmt->execute([
        ':id_asaas' => $linkBoleto,
        ':forma_pgto' => $formaPgto,
        ':id' => $idMatricula,
        ':aluno' => $idAluno,
    ]);
}

@session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_parcela'], $_POST['id_matricula'])) {
    $id_parcela = (int) $_POST['id_parcela'];
    $id_matricula = (int) $_POST['id_matricula'];
    $id_do_aluno = (int) (@$_SESSION['id'] ?? 0);

    if ($id_do_aluno <= 0) {
        echo 'Erro: Sessao expirada.';
        exit;
    }

    $parcela = buscarParcelaDoAluno($pdo, $id_parcela, $id_matricula, $id_do_aluno);
    if (!$parcela) {
        echo 'Erro: Parcela nao encontrada para este aluno.';
        exit;
    }

    $valor_parcela = (float) ($parcela['valor_parcela'] ?? ($_POST['valor_parcela'] ?? 0));
    $payloadBanco = json_decode((string) ($parcela['payload'] ?? ''), true);
    if (is_array($payloadBanco) && !empty($payloadBanco)) {
        $payload = $payloadBanco;
    }

    $stmtUsuario = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
    $stmtUsuario->execute([':id' => $id_do_aluno]);
    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC) ?: [];

    $id_pessoa = (int) ($usuario['id_pessoa'] ?? 0);
    $stmtAluno = $pdo->prepare('SELECT * FROM alunos WHERE id = :id LIMIT 1');
    $stmtAluno->execute([':id' => $id_pessoa]);
    $aluno = $stmtAluno->fetch(PDO::FETCH_ASSOC) ?: [];

    $telefone_normalizado = normalizarTelefoneEfi($aluno['telefone'] ?? ($payload['telefone'] ?? ''));
    if ($telefone_normalizado === '') {
        echo 'Erro: Telefone do aluno invalido. Cadastre com DDD (ex: 69999998888).';
        exit;
    }

    $payload['nome'] = $aluno['nome'] ?? ($payload['nome'] ?? '');
    $payload['email'] = $aluno['email'] ?? ($payload['email'] ?? ($usuario['usuario'] ?? ''));
    $payload['cpf'] = str_replace(['.', '-'], '', (string) ($aluno['cpf'] ?? ($payload['cpf'] ?? '')));
    $payload['telefone'] = $telefone_normalizado;

    try {
        $boletoPayment = new EFIBoletoPayment(
            $config['client_id'],
            $config['client_secret'],
            $config['sandbox']
        );

        $chargeExistente = trim((string) ($parcela['charge_id'] ?? ''));
        if ($chargeExistente !== '') {
            try {
                $consulta = $boletoPayment->consultarCobranca($chargeExistente);
                $dadosCobranca = $consulta['data'] ?? [];
                $dadosBillet = $dadosCobranca['payment']['banking_billet'] ?? [];

                $statusAtual = strtolower((string) ($dadosCobranca['status'] ?? ''));
                $linkAtual = (string) ($dadosBillet['billet_link'] ?? ($dadosBillet['link'] ?? ($parcela['id_asaas'] ?? '')));
                $codigoAtual = (string) ($dadosBillet['line'] ?? ($dadosBillet['barcode'] ?? ($parcela['transaction_receipt_url'] ?? '')));
                $situacaoAtual = ($statusAtual === 'paid') ? 1 : (int) ($parcela['situacao'] ?? 0);

                $stmtUpParcela = $pdo->prepare('UPDATE parcelas_geradas_por_boleto SET id_asaas = :id_asaas, transaction_receipt_url = :transaction_receipt_url, situacao = :situacao WHERE id = :id');
                $stmtUpParcela->execute([
                    ':id_asaas' => $linkAtual,
                    ':transaction_receipt_url' => $codigoAtual,
                    ':situacao' => $situacaoAtual,
                    ':id' => $id_parcela,
                ]);

                renderBoletoHtml($valor_parcela, $codigoAtual, $linkAtual);
                exit;
            } catch (Throwable $e) {
            }
        }

        $resultado = $boletoPayment->createBoletoCharge($payload);
        $billet = $resultado['payment_data']['data']['payment']['banking_billet'] ?? [];

        $linkBoleto = (string) ($billet['billet_link'] ?? ($billet['link'] ?? ($billet['pdf']['charge'] ?? ($resultado['link_boleto'] ?? ''))));
        $codigoBoleto = (string) ($billet['line'] ?? ($billet['barcode'] ?? ($resultado['linha_digitavel'] ?? '')));
        $situacao = (strtolower((string) ($resultado['status'] ?? '')) === 'paid') ? 1 : 0;

        $stmt = $pdo->prepare('UPDATE parcelas_geradas_por_boleto SET id_asaas = :id_asaas, charge_id = :charge_id, transaction_receipt_url = :transaction_receipt_url, id_matricula = :id_matricula, situacao = :situacao WHERE id = :id');
        $stmt->execute([
            ':id_asaas' => $linkBoleto,
            ':charge_id' => $resultado['charge_id'],
            ':transaction_receipt_url' => $codigoBoleto,
            ':id_matricula' => $id_matricula,
            ':situacao' => $situacao,
            ':id' => $id_parcela,
        ]);

        atualizarMatriculaFormaPgto($pdo, $id_matricula, $id_do_aluno, 'BOLETO', $linkBoleto);
        renderBoletoHtml($valor_parcela > 0 ? $valor_parcela : (float) ($resultado['total'] ?? 0), $codigoBoleto, $linkBoleto);
        exit;
    } catch (RequestException $e) {
        echo 'Erro na requisicao: ' . $e->getMessage();
        exit;
    } catch (Exception $e) {
        echo 'Erro: ' . $e->getMessage();
        exit;
    }
}

echo 'Requisicao invalida.';
?>
