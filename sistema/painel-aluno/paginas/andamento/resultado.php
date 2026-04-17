<?php 
require_once("../../../conexao.php");

$id_curso = $_POST['id_curso'];
$id_mat = $_POST['id_mat'];

$acertos = 0;

$query = $pdo->query("SELECT * FROM perguntas_quest where curso = '$id_curso' ORDER BY id asc");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	for($i=0; $i < $total_reg; $i++){
		$id = $res[$i]['id'];				
		$id_alt = @$_POST[$id];

		if($id_alt == ""){
			echo 'Preencha Todas as Questões!';
			exit();
		}

		$query2 = $pdo->query("SELECT * FROM alternativas where id = '$id_alt'");
		$res2 = $query2->fetchAll(PDO::FETCH_ASSOC);
		$correta = $res2[0]['correta'];

		if($correta == 'Sim'){
			$acertos += 1;
		}

	}

	$nota = ($acertos / $total_reg) * 100;
	$notaF = number_format($nota, 2, ',', '.');
	
	$pdo->query("UPDATE matriculas SET nota = '$nota' where id = '$id_mat'");

	if($nota >= $media_config){
		echo 'Aprovado***'.$notaF;
		$pdo->query("UPDATE matriculas SET status = 'Finalizado', data_conclusao = curDate() where id = '$id_mat'");
	}else{
		echo 'Reprovado***'.$notaF;
	}
	
}

 ?>