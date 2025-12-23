<?php 
include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = @$_GET['id'];

$query2 = $pdo->query("SELECT * FROM alunos where id = '$id'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$cartoes = $res2[0]['cartao'] - 1;


$pdo->query("UPDATE alunos SET cartao = '$cartoes' where id = '$id'");


?>