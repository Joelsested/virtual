<?php 
$tabela = 'receber';
include_once('../conexao.php');

$id = @$_GET['id'];

$pdo->query("UPDATE $tabela set pago = 'Sim', data_pgto = 'curDate()' where id = '$id'");

?>