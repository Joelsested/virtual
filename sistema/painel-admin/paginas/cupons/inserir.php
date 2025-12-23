<?php 
require_once("../../../conexao.php");
$tabela = 'cupons';

$codigo = $_POST['codigo'];
$valor = $_POST['valor'];
$id = $_POST['id'];
$valor = str_replace(',', '.', $valor);
$quantidade = $_POST['quantidade'];
$tipo = $_POST['tipo'];
$data = $_POST['data'];

//validar codigo duplicado
$query = $pdo->query("SELECT * FROM $tabela where codigo = '$codigo'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $res[0]['id'] != $id){
	echo 'Cupon jив Cadastrado com este cиоdigo, escolha Outro!';
	exit();
}

if($data != ""){
	$sql_data = ", data_validade = '$data'";
}else{
	$sql_data = " ";
}


if($id == ""){

	$query = $pdo->prepare("INSERT INTO $tabela SET codigo = :codigo, valor = :valor, quantidade = :quantidade, tipo = :tipo $sql_data");
}else{
	$query = $pdo->prepare("UPDATE $tabela SET codigo = :codigo, valor = :valor, quantidade = :quantidade, tipo = :tipo $sql_data WHERE id = '$id'");
}

$query->bindValue(":codigo", "$codigo");
$query->bindValue(":valor", "$valor");
$query->bindValue(":quantidade", "$quantidade");
$query->bindValue(":tipo", "$tipo");

$query->execute();

echo 'Salvo com Sucesso';

 ?>