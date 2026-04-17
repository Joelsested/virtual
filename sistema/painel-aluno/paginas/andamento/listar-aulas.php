<?php 
require_once("../../../conexao.php");
$tabela = 'aulas';
@session_start();
$id_aluno = $_POST['id_usu'];

$id_do_curso_pag = $_POST['id'];
$id_mat = $_POST['id_mat'];


//verificar se o aluno está matriculado no curso
$query_m = $pdo->query("SELECT * FROM matriculas where id_curso = '$id_do_curso_pag' and aluno = '$id_aluno' and status != 'Aguardando' ");
$res_m = $query_m->fetchAll(PDO::FETCH_ASSOC);

if(@count($res_m) == 0){
	echo 'Você não está matriculado neste curso!';
	exit();
}


$query_m = $pdo->query("SELECT * FROM matriculas where id = '$id_mat' ORDER BY id asc");
$res_m = $query_m->fetchAll(PDO::FETCH_ASSOC);
$total_aulas_conc = $res_m[0]['aulas_concluidas'];


$query_m = $pdo->query("SELECT * FROM cursos where id = '$id_do_curso_pag'");
$res_m = $query_m->fetchAll(PDO::FETCH_ASSOC);
$link_arquivo = $res_m[0]['arquivo'];


echo '<a href="'.$link_arquivo.'" target="_blank" class="cor-aula link-aula"><p class="titulo-curso"><small><img src="img/rar.png" width="20px" style="margin-right:3px"><span>Arquivos do Curso</span></small></p><hr style="margin:8px"></a>';


$query_m = $pdo->query("SELECT * FROM sessao where curso = '$id_do_curso_pag' ORDER BY id asc");
$res_m = $query_m->fetchAll(PDO::FETCH_ASSOC);
$total_reg_m = @count($res_m);			
if($total_reg_m > 0){
	$primeira_sessao = $res_m[0]['id'];
	for($i_m=0; $i_m < $total_reg_m; $i_m++){
		foreach ($res_m[$i_m] as $key => $value){}
			$sessao = $res_m[$i_m]['id'];
		$nome_sessao = $res_m[$i_m]['nome'];
		

		echo '<b><p class="titulo-curso"><small>'.$nome_sessao.'</small></p></b>';

		 

		$query = $pdo->query("SELECT * FROM aulas where curso = '$id_do_curso_pag' and sessao = '$sessao' ORDER BY num_aula asc");
		$res = $query->fetchAll(PDO::FETCH_ASSOC);
		$total_reg = @count($res);

		if($total_reg > 0){

			for($i=0; $i < $total_reg; $i++){
				foreach ($res[$i] as $key => $value){}
					$id_aula = $res[$i]['id'];
				$nome_aula = $res[$i]['nome'];	
				$num_aula = $res[$i]['num_aula'];
				$sessao_aula = $res[$i]['sessao'];			
				$link = $res[$i]['link'];
				$seq_aula = $res[$i]['sequencia_aula'];

				if($seq_aula <= $total_aulas_conc){
					$cor_aula = 'cor-aula';
					$ocultar_link = '';
					$ocultar_span = 'ocultar';
				}else{
					$cor_aula = 'text-muted';
					$ocultar_link = 'ocultar';
					$ocultar_span = '';
				}


				

echo <<<HTML
 				<p style="margin-bottom: 3px">
 				<a href="#" onclick="abrirAula('{$id_aula}', 'aula', '$nome_sessao')" title="Ver Aula" class="link-aula {$ocultar_link}">
				<small>
				<i class="fa fa-video-camera {$cor_aula}" style="margin-right: 2px"></i>
				<span class="{$cor_aula}">Aula {$num_aula} - {$nome_aula}</span>
				<br></small>
				</a>

				<span class="{$ocultar_span}">
				<small>
				<i class="fa fa-video-camera {$cor_aula}" style="margin-right: 2px"></i>
				<span class="{$cor_aula}">Aula {$num_aula} - {$nome_aula}</span>
				<br></small>
				</span>

				</p>
HTML;
				
	
	}
		
		
	}else{
		echo '<span class="neutra">Nenhuma aula Cadastrada</span>';
	}

	echo '<hr>';

}



}else{

	$query = $pdo->query("SELECT * FROM aulas where curso = '$id_do_curso_pag' ORDER BY num_aula asc");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);

	if($total_reg > 0){

		for($i=0; $i < $total_reg; $i++){
			foreach ($res[$i] as $key => $value){}
				$id_aula = $res[$i]['id'];
			$nome_aula = $res[$i]['nome'];	
			$num_aula = $res[$i]['num_aula'];
			$link = $res[$i]['link'];

			if($num_aula <= $total_aulas_conc){
					$cor_aula = 'cor-aula';
					$ocultar_link = '';
					$ocultar_span = 'ocultar';
				}else{
					$cor_aula = 'text-muted';
					$ocultar_link = 'ocultar';
					$ocultar_span = '';
				}

echo <<<HTML
				<p style="margin-bottom: 3px">
 				<a href="#" onclick="abrirAula('{$id_aula}', 'aula', '')" title="Ver Aula" class="link-aula {$ocultar_link}">
				<small>
				<i class="fa fa-video-camera {$cor_aula}" style="margin-right: 2px"></i>
				<span class="{$cor_aula}">Aula {$num_aula} - {$nome_aula}</span>
				<br></small>
				</a>

				<span class="{$ocultar_span}">
				<small>
				<i class="fa fa-video-camera {$cor_aula}" style="margin-right: 2px"></i>
				<span class="{$cor_aula}">Aula {$num_aula} - {$nome_aula}</span>
				<br></small>
				</span>

				</p>
HTML;				
			

		}


	}else{
		echo '<span class="neutra">Nenhuma aula Cadastrada</span>';
	}


}

?>		