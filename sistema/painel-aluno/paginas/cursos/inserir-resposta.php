<?php 
require_once("../../../conexao.php");
$tabela = 'respostas';
@session_start();
$id_aluno = $_SESSION['id'];
$resposta = $_POST['resposta'];
$pergunta = $_POST['id_pergunta'];
$id_curso = $_POST['id_curso'];

$resposta = str_replace("'", " ", $resposta);
$resposta = str_replace('"', ' ', $resposta);

$query = $pdo->prepare("INSERT INTO $tabela SET  resposta = :resposta, curso = '$id_curso', pessoa = '$id_aluno', data = curDate(), pergunta = '$pergunta', funcao = 'Aluno'");


$query->bindValue(":resposta", "$resposta");
$query->execute();


$pdo->query("UPDATE perguntas SET respondida = 'Não' where id = '$pergunta'");

echo 'Salvo com Sucesso';

?>