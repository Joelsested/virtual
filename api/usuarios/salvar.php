<?php 
require_once("../conexao.php");
$tabela = 'usuarios';

$postjson = json_decode(file_get_contents('php://input'), true);

$id = @$postjson['id'];
$nome = @$postjson['nome'];
$senha = @$postjson['senha'];
$email = @$postjson['email'];
$nivel = @$postjson['nivel'];



//validar email
$query = $pdo->query("SELECT * FROM $tabela where email = '$email'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $res[0]['id'] != $id){
	$result = json_encode(array('mensagem'=>'Email já Cadastrado, escolha Outro!', 'sucesso'=>false));
	echo $result;	
	exit();
}



if($id == "" || $id == "0"){
	$res = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, email = :email, senha = :senha, nivel = :nivel");	

}else{
	$res = $pdo->prepare("UPDATE $tabela SET nome = :nome, email = :email, senha = :senha, nivel = :nivel where id = '$id'");
	
}


$res->bindValue(":nome", "$nome");
$res->bindValue(":senha", "$senha");
$res->bindValue(":nivel", "$nivel");
$res->bindValue(":email", "$email");

$res->execute();


$result = json_encode(array('mensagem'=>'Salvo com sucesso!', 'sucesso'=>true));

echo $result;

?>