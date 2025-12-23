<?php
session_cache_limiter('private');
$cache_limiter = session_cache_limiter();

/* define o prazo do cache em 120 minutos */
session_cache_expire(120);
$cache_expire = session_cache_expire();
/* inicia a sessão */
@session_start();

require_once('../../sistema/conexao.php');

$usuario = $_POST['usuario'];
$senha = $_POST['senha'];
$senha_crip = md5($senha);

//verficiar email se existe
$query = $pdo->prepare("SELECT * FROM usuarios where (cpf = :usuario or usuario = :usuario)");
$query->bindValue(":usuario", "$usuario");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) == 0){
	echo 'Usuário não Cadastrado com este email ou CPF inserido!';
	exit();
}


$query = $pdo->prepare("SELECT * FROM usuarios where (cpf = :usuario or usuario = :usuario) and senha_crip = :senha and nivel = 'Aluno'");
$query->bindValue(":usuario", "$usuario");
$query->bindValue(":senha", "$senha_crip");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){

	if($res[0]['ativo'] == 'Não'){
		echo "Seu Acesso foi desativado pelo Administrador!";
		exit();	
	}

	//recuperar o nível do usuário
	$_SESSION['nivel'] = $res[0]['nivel'];
	$_SESSION['cpf'] = $res[0]['cpf'];
	$_SESSION['id'] = $res[0]['id'];
	$_SESSION['nome'] = $res[0]['nome'];

	
	if($_SESSION['nivel'] == 'Aluno'){
		echo "Logado com Sucesso-".$_SESSION['id'];
	}
		 
}else{
	echo "Senha Incorreta!!";
}

 ?>


