<?php 
require_once("../../../conexao.php");
@session_start();
$id_usuario = $_SESSION['id'];

$tabela = 'arquivos_cursos';




$id_curso = @$_POST['id_do_arq'];
$arquivo = @$_POST['arquivo_4'];
$descricao = @$_POST['descricao'];



if ($id_curso == '') {
	echo 'Primeiro Insira o Curso';
	exit();
}

//SCRIPT PARA SUBIR FOTO NO BANCO
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['arquivo_4']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);
$caminho = '../../img/arquivos/' .$nome_img;
if (@$_FILES['arquivo_4']['name'] == ""){
  $imagem = "sem-arquivo.png";
}else{
    $imagem = $nome_img;
}

$imagem_temp = @$_FILES['arquivo_4']['tmp_name']; 
$ext = pathinfo($imagem, PATHINFO_EXTENSION);   
if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'zip' or $ext == 'pdf' or $ext == 'rar'){ 
move_uploaded_file($imagem_temp, $caminho);
}else{
	echo 'Extensão de Imagem não permitida!';
	exit();
}



$pdo->query("INSERT INTO arquivos_cursos SET curso = '$id_curso', arquivo = '$imagem', data = curDate(), descricao = '$descricao', usuario = '$id_usuario'");	


echo 'Salvo com Sucesso';

 ?>