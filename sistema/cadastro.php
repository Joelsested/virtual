<?php 
require_once("conexao.php");
$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];
$conf_senha = $_POST['conf_senha'];
$senha_crip = md5($senha);
$tipo = @$_POST['tipo'];

if($senha != $conf_senha){
	echo 'As senhas não se coincidem!!';
	exit();
}

$query = $pdo->query("SELECT * FROM usuarios where nome = 'Professor_padrao'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$prof_padrao = $res[0]['id'];

if($tipo == 'Professor'){

	//CADASTRO DO PROFESSOR
	$query = $pdo->prepare("SELECT * FROM professores where email = :email");
$query->bindValue(":email", "$email");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	echo 'Este email já está cadastrado, escolha outro ou recupere sua senha!';
	exit();
}

$query = $pdo->prepare("INSERT INTO professores SET nome = :nome, email = :email, foto = 'sem-perfil.jpg', data = curDate(), ativo = 'Sim'");
$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->execute();
$ult_id = $pdo->lastInsertId();

$query = $pdo->prepare("INSERT INTO usuarios SET nome = :nome, usuario = :email, senha = :senha, senha_crip = :senha_crip, nivel = 'Professor',  foto = 'sem-perfil.jpg', id_pessoa = '$ult_id', ativo = 'Sim', data = curDate()");
$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->bindValue(":senha", "$senha");
$query->bindValue(":senha_crip", "$senha_crip");
$query->execute();

}else{

	//capturar o email do aluno para o email marketing
$query = $pdo->query("SELECT * from emails where email = '$email'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
	if(@count($res) == 0){
		$query = $pdo->prepare("INSERT INTO emails SET email = :email, nome = :nome, enviar = 'sim'");

		$query->bindValue(":email", "$email");
		$query->bindValue(":nome", "$nome");		
		$query->execute();
	}	


$query = $pdo->prepare("SELECT * FROM alunos where email = :email");
$query->bindValue(":email", "$email");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){
	echo 'Este email já está cadastrado, escolha outro ou recupere sua senha!';
	exit();
}

$query = $pdo->prepare("INSERT INTO alunos SET nome = :nome, email = :email, foto = 'sem-perfil.jpg', data = curDate(), usuario = '$prof_padrao', ativo = 'Sim'");
$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->execute();
$ult_id = $pdo->lastInsertId();

$query = $pdo->prepare("INSERT INTO usuarios SET nome = :nome, usuario = :email, senha = :senha, senha_crip = :senha_crip, nivel = 'Aluno',  foto = 'sem-perfil.jpg', id_pessoa = '$ult_id', ativo = 'Sim', data = curDate()");
$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->bindValue(":senha", "$senha");
$query->bindValue(":senha_crip", "$senha_crip");
$query->execute();

}

echo 'Cadastrado com Sucesso';

 ?>