<?php 
include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = @$_GET['id'];

$pdo->query("UPDATE alunos SET ativo = 'Não' where id = '$id'");
$pdo->query("UPDATE usuarios SET ativo = 'Não' where id_pessoa = '$id' and nivel = 'Aluno'");

?>