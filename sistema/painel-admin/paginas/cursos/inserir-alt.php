<?php 
require_once("../../../conexao.php");
$tabela = 'alternativas';


$alternativa = $_POST['alternativa'];
$id_pergunta = $_POST['id'];
$id_curso = $_POST['id_curso'];
$correta = @$_POST['correta'];

if($correta == 'Sim'){
$query = $pdo->query("SELECT * FROM $tabela where pergunta = '$id_pergunta' and correta = 'Sim'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	echo 'Você só pode ter uma alternativa correta para cada Pergunta';
	exit();
}
}


$query = $pdo->prepare("INSERT INTO $tabela SET curso = '$id_curso', pergunta = '$id_pergunta', correta = '$correta', resposta = :resposta");

$query->bindValue(":resposta", "$alternativa");
$query->execute();

echo 'Salvo com Sucesso';

 ?>