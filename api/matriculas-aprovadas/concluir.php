<?php 
include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id = @$_GET['id'];

//trazer o curso da matricula
$query = $pdo->query("SELECT * FROM matriculas where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$curso = $res[0]['id_curso'];

$query2 = $pdo->query("SELECT * FROM aulas where curso = '$curso'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$aulas = @count($res2);

$pdo->query("UPDATE matriculas SET status = 'Finalizado', aulas_concluidas = '$aulas' where id = '$id'");


?>