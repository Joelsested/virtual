<?php 
require_once("../../../conexao.php");
$tabela = 'aulas';

$id = $_POST['id'];



$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$foto = $res[0]['apostila'];

if($foto != "sem-arquivo.png"){
	@unlink('../../img/arquivos/'.$foto);
}


$pdo->query("DELETE FROM $tabela where id = '$id'");



echo 'Excluído com Sucesso';

?>