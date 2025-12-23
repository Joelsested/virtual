<?php 
require_once('../../sistema/conexao.php');
$tabela = 'avaliacoes';

$id = $_POST['id'];

$pdo->query("DELETE FROM $tabela where id = '$id'");

echo 'Excluído com Sucesso';

?>