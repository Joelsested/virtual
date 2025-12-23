<?php 
require_once("../../../conexao.php");
$tabela = 'sessao';

$id = $_POST['id'];

//excluir aulas relacionadas a sessão
$pdo->query("DELETE FROM aulas where sessao = '$id'");

$pdo->query("DELETE FROM $tabela where id = '$id'");

echo 'Excluído com Sucesso';

?>