<?php
require_once("../../../conexao.php");
require_once __DIR__ . '/../../../../config/session.php';
sested_session_start();

$id_usuario = $_SESSION['id'] ?? null;
$id_curso = $_POST['id_curso'] ?? null;
$forma_pgto = $_POST['forma_pgto'] ?? null;
$id_matricula = $_POST['id'] ?? null;
$tabela = 'matriculas';

$nome_do_curso = $_POST['nome_do_curso'] ?? 'Pagamento Curso';
$pacote = $_POST['pacote'] ?? 'Nao';
$quantidadeParcelas = $_POST['quantidadeParcelas'] ?? 1;

header('Content-Type: application/json; charset=utf-8');

try {
    $forma_pgto = strtoupper(trim((string) $forma_pgto));
    $quantidadeParcelas = (int) $quantidadeParcelas;
    if ($quantidadeParcelas < 1) {
        $quantidadeParcelas = 1;
    }

    $maxParcelas = 24;
    if ($forma_pgto === 'BOLETO_PARCELADO') {
        if ($quantidadeParcelas > $maxParcelas) {
            $quantidadeParcelas = $maxParcelas;
        }
    } else {
        $quantidadeParcelas = 1;
    }

    if (!$id_usuario || !$id_curso || !$forma_pgto || !$id_matricula) {
        throw new Exception('Dados incompletos.');
    }

    $formasPermitidas = ['BOLETO', 'BOLETO_PARCELADO', 'PIX', 'CARTAO_DE_CREDITO', 'CARTAO_RECORRENTE'];
    if (!in_array($forma_pgto, $formasPermitidas, true)) {
        throw new Exception('Forma de pagamento invalida.');
    }

    $stmtMatricula = $pdo->prepare("SELECT * FROM {$tabela} WHERE id = :id AND aluno = :aluno LIMIT 1");
    $stmtMatricula->execute([':id' => $id_matricula, ':aluno' => $id_usuario]);
    $matricula = $stmtMatricula->fetch(PDO::FETCH_ASSOC);
    if (!$matricula) {
        throw new Exception('Matricula nao encontrada.');
    }

    $statusMatricula = $matricula['status'] ?? '';
    if ($statusMatricula !== '' && strcasecmp($statusMatricula, 'Aguardando') !== 0) {
        throw new Exception('Pagamento ja confirmado. Nao e possivel alterar a forma de pagamento.');
    }

    if (!empty($matricula['pacote'])) {
        $pacote = $matricula['pacote'];
    }

    $stmtPixPago = $pdo->prepare("SELECT COUNT(*) FROM pagamentos_pix WHERE id_matricula = :id AND status = 'CONCLUIDA'");
    $stmtPixPago->execute([':id' => $id_matricula]);
    if ($stmtPixPago->fetchColumn() > 0) {
        throw new Exception('Pagamento PIX confirmado. Nao e possivel alterar a forma de pagamento.');
    }

    $stmtBoletoPago = $pdo->prepare("SELECT COUNT(*) FROM pagamentos_boleto WHERE id_matricula = :id AND status = 'paid'");
    $stmtBoletoPago->execute([':id' => $id_matricula]);
    if ($stmtBoletoPago->fetchColumn() > 0) {
        throw new Exception('Pagamento por boleto confirmado. Nao e possivel alterar a forma de pagamento.');
    }

    $stmtParcelaPaga = $pdo->prepare("SELECT COUNT(*) FROM parcelas_geradas_por_boleto WHERE id_matricula = :id AND situacao = 1");
    $stmtParcelaPaga->execute([':id' => $id_matricula]);
    if ($stmtParcelaPaga->fetchColumn() > 0) {
        throw new Exception('Ja existe parcela paga para esta matricula. Nao e possivel alterar a forma de pagamento.');
    }

    // Mantem historico de cobrancas EFY para nao perder parcelas e boletos ja gerados.
    $query = $pdo->prepare("UPDATE $tabela SET forma_pgto = :forma_pgto WHERE aluno = :aluno AND id = :id");
    $query->bindValue(':forma_pgto', $forma_pgto);
    $query->bindValue(':aluno', $id_usuario);
    $query->bindValue(':id', $id_matricula);
    $query->execute();

    $redirectUrl = null;
    switch ($forma_pgto) {
        case 'BOLETO':
        case 'BOLETO_PARCELADO':
        case 'PIX':
            $redirectUrl = $url_sistema . 'efi/index.php?' . http_build_query([
                'formaDePagamento' => $forma_pgto,
                'billingType' => strtoupper($forma_pgto),
                'quantidadeParcelas' => $quantidadeParcelas,
                'id_do_curso' => $id_curso,
                'id_matricula' => $id_matricula,
                'nome_do_curso' => $nome_do_curso,
                'pacote' => $pacote
            ]);
            break;

        case 'CARTAO_DE_CREDITO':
        case 'CARTAO_RECORRENTE':
            $redirectUrl = $url_sistema . 'sistema/painel-aluno/index.php?pagina=parcelas_cartao';
            break;
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Forma de pagamento salva com sucesso.',
        'redirect' => $redirectUrl
    ]);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
