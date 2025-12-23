<?php 
require_once("../../../conexao.php");
$tabela = 'cursos';

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$foto = $res[0]['imagem'];
$status = $res[0]['status'];
if($status == 'Aprovado'){
	echo 'Cuidado, O Curso não pode ser excluído com status de aprovado!';
	exit();
}
if($foto != "sem-foto.png"){
	@unlink('../../img/cursos/'.$foto);
}

$pdo->query("DELETE FROM aulas where curso = '$id'");
$pdo->query("DELETE FROM $tabela where id = '$id'");

echo 'Excluído com Sucesso';

?>