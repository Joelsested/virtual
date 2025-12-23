<?php 
require_once("../../../conexao.php");
$tabela = 'perguntas_quest';

$id = $_POST['id'];

//excluir aulas relacionadas a sessão
$pdo->query("DELETE FROM alternativas where pergunta = '$id'");

$pdo->query("DELETE FROM $tabela where id = '$id'");

echo 'Excluído com Sucesso';

?>