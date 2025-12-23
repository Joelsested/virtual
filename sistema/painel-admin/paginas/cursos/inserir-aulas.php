<?php 
require_once("../../../conexao.php");
$tabela = 'aulas';

$num_aula = $_POST['num_aula'];
$nome_aula = $_POST['nome_aula'];
$link_aula = $_POST['link_aula'];

$sessao_aula = $_POST['sessao_aula'];
$id_curso = $_POST['id'];
$id_aula = $_POST['id_aula'];


//buscar quantidade de aulas do curso
$query = $pdo->query("SELECT * FROM $tabela where curso = '$id_curso'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_aulas = @count($res);
if($total_aulas == 0){
	$seq_aula = 1;
}else{
	$seq_aula = $total_aulas + 1;
}


//validar num aula duplicado
$query = $pdo->query("SELECT * FROM $tabela where num_aula = '$num_aula' and sessao = '$sessao_aula' and curso = '$id_curso'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $res[0]['id'] != $id_aula){
	echo 'Aula já Cadastrada, escolha outro numero para aula!';
	exit();
}



$query = $pdo->query("SELECT * FROM $tabela where id = '$id_aula'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$foto = $res[0]['apostila'];
}else{
	$foto = '';
}


//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['arquivo_2']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/arquivos/' .$nome_img;

$imagem_temp = @$_FILES['arquivo_2']['tmp_name']; 

if(@$_FILES['arquivo_2']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'pdf' or $ext == 'PDF' or $ext == 'gif'){ 
						
			//EXCLUO A FOTO ANTERIOR
			if($foto != "sem-arquivo"){
				@unlink('../../img/arquivos/'.$foto);
			}

			$foto = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}


if($id_aula == ""){

	$query = $pdo->prepare("INSERT INTO $tabela SET num_aula = :num_aula, nome = :nome, link = :link, curso = '$id_curso', apostila = '$foto', sessao = '$sessao_aula', sequencia_aula = '$seq_aula'");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET num_aula = :num_aula, nome = :nome, link = :link, sessao = '$sessao_aula', apostila = '$foto' where id = '$id_aula'");
}

$query->bindValue(":nome", "$nome_aula");
$query->bindValue(":num_aula", "$num_aula");
$query->bindValue(":link", "$link_aula");
$query->execute();

echo 'Salvo com Sucesso';

 ?>