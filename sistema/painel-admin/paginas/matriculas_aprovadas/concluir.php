<?php 
require_once("../../../conexao.php");
$tabela = 'matriculas';

$id = $_POST['id'];

//trazer o curso da matricula
$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$curso = $res[0]['id_curso'];

$query2 = $pdo->query("SELECT * FROM aulas where curso = '$curso'");
$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
$aulas = @count($res2);

$pdo->query("UPDATE $tabela SET status = 'Finalizado', aulas_concluidas = '$aulas' where id = '$id'");

echo 'Concluído com Sucesso';

?>