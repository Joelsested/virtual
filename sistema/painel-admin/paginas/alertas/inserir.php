<?php 
require_once("../../../conexao.php");
$tabela = 'alertas';

$ano_atual = date('Y');

@session_start();
$id_usuario = $_SESSION['id'];

$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$data = $_POST['data'];
$video = $_POST['video'];
$link = $_POST['link'];

$descricao = str_replace("'", " ", $descricao);
$descricao = str_replace('"', ' ', $descricao);

$titulo = str_replace("'", " ", $titulo);
$titulo = str_replace('"', ' ', $titulo);


$id = $_POST['id'];


$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$foto = $res[0]['imagem'];
}else{
	$foto = 'sem-foto.png';
}



//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/alertas/' .$nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name']; 

if(@$_FILES['foto']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif'){ 
						
			//EXCLUO A FOTO ANTERIOR
			if($foto != "sem-foto.png"){
				@unlink('../../img/alertas/'.$foto);
			}

			$foto = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}


if($id == ""){

	$query = $pdo->prepare("INSERT INTO $tabela SET titulo = :titulo, descricao = :descricao, link = :link, video = :video, data = '$data', imagem = '$foto'");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET titulo = :titulo, descricao = :descricao, link = :link, video = :video, data = '$data', imagem = '$foto'  WHERE id = '$id'");
}

$query->bindValue(":titulo", "$titulo");
$query->bindValue(":descricao", "$descricao");
$query->bindValue(":link", "$link");
$query->bindValue(":video", "$video");


$query->execute();

echo 'Salvo com Sucesso';

 ?>