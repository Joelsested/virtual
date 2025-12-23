<?php 
require_once("../../../conexao.php");
$tabela = 'banner_login';

$id = $_POST['id'];
$acao = $_POST['acao'];

if($acao == 'Sim'){
	$query = $pdo->query("SELECT * from $tabela where ativo = 'Sim'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res) > 0){
		echo 'Você já possui um banner ativo para o login, não pode ativar outro!';
		exit();
	}
}


$pdo->query("UPDATE $tabela SET ativo = '$acao' where id = '$id'");


echo 'Alterado com Sucesso';
?>