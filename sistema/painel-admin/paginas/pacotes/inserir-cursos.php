<?php 
require_once("../../../conexao.php");
$tabela = 'cursos_pacotes';


$id_pacote = $_POST['id_pacote'];
$id_curso = $_POST['id_curso'];

//validar num aula duplicado
$query = $pdo->query("SELECT * FROM $tabela where id_curso = '$id_curso' and id_pacote = '$id_pacote' ");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	echo 'Curso Já Adicionado ao Pacote!';
	exit();
}


$query = $pdo->query("INSERT INTO $tabela SET  id_pacote = '$id_pacote', id_curso = '$id_curso'");


echo 'Salvo com Sucesso';

 ?>