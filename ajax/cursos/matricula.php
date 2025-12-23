<?php 
require_once('../../sistema/conexao.php');
@session_start();
$id_aluno = $_SESSION['id'];


$usuario = @$_POST['email'];
$curso = $_POST['curso'];
$pacote = $_POST['pacote'];


if($pacote == 'Sim'){
	$tabela = 'pacotes';
}else{
	$tabela = 'cursos';
}

//verficiar email se existe
if($_SESSION['nivel'] == 'Aluno'){
	$query = $pdo->prepare("SELECT * FROM usuarios where id = '$id_aluno'");
}else{
	$query = $pdo->prepare("SELECT * FROM usuarios where usuario = :usuario ");
	$query->bindValue(":usuario", "$usuario");
}

$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) == 0){
	echo 'Aluno não Cadastrado com este email!';
	exit();
}else{
	$id_aluno = $res[0]['id'];
	$nome_aluno = $res[0]['nome'];	
	$email_aluno = $res[0]['usuario'];
}


$query = $pdo->query("SELECT * FROM $tabela where id = '$curso'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$valor = $res[0]['valor'];
$promocao = $res[0]['promocao'];
$nome_curso = $res[0]['nome'];
$professor = $res[0]['professor'];
$split = $res[0]['split'] ?? 1;

if($promocao > 0){
	$valor = $promocao;
}


//verficiar se o aluno já está matriculado no curso
$query = $pdo->query("SELECT * FROM matriculas where aluno = '$id_aluno' and id_curso = '$curso' and pacote = '$pacote' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	echo 'Aluno já matriculado neste curso!';
	exit();
}else{

	if($valor == '0'){
		$pdo->query("INSERT INTO matriculas SET id_curso = '$curso', aluno = '$id_aluno', professor = '$professor', valor = '$valor', data = curDate(), status = 'Matriculado', pacote = '$pacote', subtotal = '$valor', aulas_concluidas = '1', split = '$split'");
	}else{
		$pdo->query("INSERT INTO matriculas SET id_curso = '$curso', aluno = '$id_aluno', professor = '$professor', valor = '$valor', data = curDate(), status = 'Aguardando', pacote = '$pacote', subtotal = '$valor', aulas_concluidas = '1', split = '$split'");
	}
	
	
}

echo 'Matriculado com Sucesso';


require_once('email-matricula.php');

 ?>


