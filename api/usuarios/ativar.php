<?php 
include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = @$_GET['id'];

$pdo->query("UPDATE alunos SET ativo = 'Sim' where id = '$id'");
$pdo->query("UPDATE usuarios SET ativo = 'Sim' where id_pessoa = '$id' and nivel = 'Aluno'");

?>