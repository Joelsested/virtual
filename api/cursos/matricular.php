<?php 
include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$curso = @$postjson['id'];
$email = @$postjson['email'];

//verficiar email se existe
$query = $pdo->prepare("SELECT * FROM usuarios where usuario = :usuario ");
$query->bindValue(":usuario", "$email");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) == 0){
	$result = json_encode(array('mensagem'=>'Email não Cadastrado!', 'sucesso'=>false));
	echo $result;
	exit();
}else{
	$id_aluno = $res[0]['id'];
	$nome_aluno = $res[0]['nome'];	
	$email_aluno = $res[0]['usuario'];
}


$query = $pdo->query("SELECT * FROM cursos where id = '$curso'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$valor = $res[0]['valor'];
$promocao = $res[0]['promocao'];
$nome_curso = $res[0]['nome'];
$professor = $res[0]['professor'];

if($promocao > 0){
	$valor = $promocao;
}

//verficiar se o aluno já está matriculado no curso
$query = $pdo->query("SELECT * FROM matriculas where aluno = '$id_aluno' and id_curso = '$curso'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	$result = json_encode(array('mensagem'=>'Aluno já Matriculado no Curso!', 'sucesso'=>false));
	echo $result;
	exit();
}else{

	if($valor == '0'){
		$pdo->query("INSERT INTO matriculas SET id_curso = '$curso', aluno = '$id_aluno', professor = '$professor', valor = '$valor', data = curDate(), status = 'Matriculado', pacote = 'Não', subtotal = '$valor', aulas_concluidas = '1'");
	}else{
		$pdo->query("INSERT INTO matriculas SET id_curso = '$curso', aluno = '$id_aluno', professor = '$professor', valor = '$valor', data = curDate(), status = 'Aguardando', pacote = 'Não', subtotal = '$valor', aulas_concluidas = '1'");
	}
	
	
}


require_once('../../ajax/cursos/email-matricula.php');

$result = json_encode(array('mensagem'=>'Matriculado com sucesso!', 'sucesso'=>true));
echo $result;


?>