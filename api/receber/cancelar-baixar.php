<?php 
$tabela = 'receber';
include_once('../conexao.php');

$id = @$_GET['id'];

$pdo->query("UPDATE $tabela set pago = 'Não', data_pgto = '' where id = '$id'");

?>