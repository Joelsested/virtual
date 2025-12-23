<?php 
$tabela = 'receber';
include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = @$_GET['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$foto = $res[0]['arquivo'];
if($foto != "sem-foto.png"){
	@unlink('../../sistema/painel-admin/img/contas/'.$foto);
}

$pdo->query("DELETE from $tabela where id = '$id'");


?>