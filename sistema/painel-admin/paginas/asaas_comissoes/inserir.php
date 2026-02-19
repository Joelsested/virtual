<?php
require_once("../../../conexao.php");

$tabela = 'comissoes';

$nivel = $_POST['nivel'] ?? '';
$porcentagem = $_POST['porcentagem'] ?? '';
$recebeSempre = $_POST['recebeSempre'] ?? '0';

if ($nivel === '' || $porcentagem === '') {
    header('Location: ../../index.php?pagina=asaas_comissoes');
    exit();
}

$consulta = $pdo->prepare("SELECT COUNT(*) FROM $tabela WHERE nivel = :nivel");
$consulta->bindValue(':nivel', $nivel);
$consulta->execute();
$total_registros = (int)$consulta->fetchColumn();

if ($total_registros > 0) {
    header('Location: ../../index.php?pagina=asaas_comissoes');
    exit();
}

$query = $pdo->prepare("INSERT INTO $tabela (nivel, porcentagem, recebeSempre, created_at) VALUES (:nivel, :porcentagem, :recebeSempre, NOW())");
$query->bindValue(':nivel', $nivel);
$query->bindValue(':porcentagem', str_replace(',', '.', $porcentagem));
$query->bindValue(':recebeSempre', (int)$recebeSempre);
$query->execute();

header('Location: ../../index.php?pagina=asaas_comissoes');
exit();
?>
