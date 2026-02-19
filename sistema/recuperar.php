<?php 
require_once("conexao.php");

$email = $_POST['recuperar'];

$query = $pdo->prepare("SELECT * FROM usuarios where usuario = :email or cpf = :email");
$query->bindValue(":email", "$email");
$query->execute();
$res = $query->fetchAll(PDO::FETCH_ASSOC);
if (@count($res) == 0) {
	echo 'Não possui cadastro com este email ou CPF digitado!';
	exit();
} else {
	$email = $res[0]['usuario'];
}

// ENVIAR O EMAIL COM AS INSTRUÇÕES DE ACESSO
$destinatario = $email;
$assunto = $nome_sistema . ' - Recuperação de Acesso';
$mensagem = 'Seu acesso é pela data de nascimento no formato DDMMAAAA (somente números).';
$cabecalhos = "From: " . $email_sistema;

mail($destinatario, $assunto, $mensagem, $cabecalhos);

?>