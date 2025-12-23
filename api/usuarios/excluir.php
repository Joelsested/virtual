<?php 
include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = @$_GET['id'];

$query = $pdo->query("SELECT * FROM alunos where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$foto = $res[0]['foto'];
if($foto != "sem-perfil.jpg"){
	@unlink('../../sistema/painel-aluno/img/perfil/'.$foto);
}

$pdo->query("DELETE from alunos where id = '$id'");
$pdo->query("DELETE FROM usuarios where id_pessoa = '$id' and nivel = 'Aluno'");

?>