<?php
session_cache_limiter('private');
$cache_limiter = session_cache_limiter();

/* define o prazo do cache em 120 minutos */
session_cache_expire(120);
$cache_expire = session_cache_expire();
/* inicia a sessão */
@session_start();

require_once('conexao.php');

$usuario = $_POST['usuario'];
$senha = $_POST['senha'];
$senha_crip = md5($senha);

$query = $pdo->prepare("SELECT * FROM usuarios where (cpf = :usuario or usuario = :usuario) and senha_crip = :senha");
$query->bindValue(":usuario", "$usuario");
$query->bindValue(":senha", "$senha_crip");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if(@count($res) > 0){

	if($res[0]['ativo'] == 'Não'){
		echo "<script>window.alert('Seu Acesso foi desativado pelo Administrador!')</script>";
	echo "<script>window.location='index.php'</script>";
	exit();
	}

	//recuperar o nível do usuário
	$_SESSION['nivel'] = $res[0]['nivel'];
	$_SESSION['cpf'] = $res[0]['cpf'];
	$_SESSION['id'] = $res[0]['id'];
	$_SESSION['nome'] = $res[0]['nome'];


	
	$id = $res[0]['id'];
	echo "<script>localStorage.setItem('id_usu', '$id')</script>";

	if($_SESSION['nivel'] == 'Administrador'){
		echo "<script>window.location='painel-admin'</script>";
	}

	if($_SESSION['nivel'] == 'Tesoureiro'){
		echo "<script>window.location='painel-admin'</script>";
	}


	if($_SESSION['nivel'] == 'Secretario'){
		echo "<script>window.location='painel-admin'</script>";
	}

	if($_SESSION['nivel'] == 'Professor'){
		echo "<script>window.location='painel-admin'</script>";
	}

	if($_SESSION['nivel'] == 'Aluno'){
		echo "<script>window.location='painel-aluno'</script>";
	}

	if($_SESSION['nivel'] == 'Tutor'){
		echo "<script>window.location='painel-admin'</script>";
	}

	if($_SESSION['nivel'] == 'Parceiro'){
		echo "<script>window.location='painel-admin'</script>";
	}
	
	if($_SESSION['nivel'] == 'Assessor'){
		echo "<script>window.location='painel-admin'</script>";
	}
	
	if($_SESSION['nivel'] == 'Vendedor'){
		echo "<script>window.location='painel-admin'</script>";
	}
		 
}else{
	echo "<script>window.alert('Dados Incorretos!')</script>";
	echo "<script>window.location='index.php'</script>";
}

 ?>