<?php
require_once("../../../conexao.php");

if (!isset($_POST['acao'])) {
    header('Location: ../../index.php?pagina=asaas_comissoes');
    exit();
}

$acao = $_POST['acao'];
$id_exclusao = $_POST['id_exclusao'] ?? null;
$registros = $_POST['registros'] ?? [];

if ($acao == 'excluir' && !empty($id_exclusao)) {
    $stmt = $pdo->prepare("DELETE FROM comissoes WHERE id = ?");
    $stmt->execute([$id_exclusao]);

    header('Location: ../../index.php?pagina=asaas_comissoes');
    exit();
}

if ($acao == 'editar' && is_array($registros)) {
    foreach ($registros as $registro) {
        $id = $registro['id'] ?? null;
        if (empty($id)) {
            continue;
        }

        $porcentagem = isset($registro['porcentagem']) ? str_replace(',', '.', $registro['porcentagem']) : 0;
        $recebeSempre = isset($registro['recebeSempre']) ? (int)$registro['recebeSempre'] : 0;

        $stmt = $pdo->prepare("UPDATE comissoes SET porcentagem = ?, recebeSempre = ? WHERE id = ?");
        $stmt->execute([$porcentagem, $recebeSempre, $id]);
    }

    header('Location: ../../index.php?pagina=asaas_comissoes');
    exit();
}

header('Location: ../../index.php?pagina=asaas_comissoes');
exit();
?>
