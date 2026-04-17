<?php 
require_once("../../../conexao.php");
$tabela = 'banner_index';

$titulo = $_POST['titulo'];
$link = $_POST['link'];
$descricao = $_POST['descricao'];
$id = $_POST['id'];


//validar email duplicado
$query = $pdo->query("SELECT * FROM $tabela where titulo = '$titulo'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $res[0]['id'] != $id){
	echo 'Título banner já Cadastrado, escolha Outro!';
	exit();
}



$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$foto = $res[0]['foto'];
}else{
	$foto = 'sem-foto.png';
}


//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../img/banners/' .$nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name']; 

if(@$_FILES['foto']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif'){ 
	
			//EXCLUO A FOTO ANTERIOR
			if($foto != "sem-foto.png"){
				@unlink('../../img/banners/'.$foto);
			}

			$foto = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}


if($id == ""){

	$query = $pdo->prepare("INSERT INTO $tabela SET titulo = :titulo, descricao = :descricao, link = :link, foto = '$foto', ativo = 'Sim' ");

}else{
	$query = $pdo->prepare("UPDATE $tabela SET titulo = :titulo, descricao = :descricao, link = :link, foto = '$foto' WHERE id = '$id'");
}

$query->bindValue(":titulo", "$titulo");
$query->bindValue(":descricao", "$descricao");
$query->bindValue(":link", "$link");
$query->execute();



echo 'Salvo com Sucesso';

 ?>