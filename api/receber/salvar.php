<?php 

require_once("../conexao.php");
$tabela = 'receber';

$postjson = json_decode(file_get_contents('php://input'), true);

$id = @$postjson['id'];
$valor = @$postjson['valor'];
$valor = str_replace(',', '.', $valor);
$descricao = @$postjson['descricao'];
$data_venc = @$postjson['data_venc'];
$foto = @$postjson['foto'];


if($id != "" and $foto == ""){
	$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
	$res = $query->fetchAll(PDO::FETCH_ASSOC);
	$total_reg = @count($res);
	if($total_reg > 0){
		$foto = $res[0]['arquivo'];
	}else{
		$foto = 'sem-foto.png';
	}

}



if($id == "" || $id == "0"){
	$res = $pdo->prepare("INSERT INTO $tabela SET descricao = :descricao, valor = :valor, vencimento = '$data_venc', data = curDate(), arquivo = '$foto', pago = 'Não'");
	

}else{
	$res = $pdo->prepare("UPDATE $tabela SET descricao = :descricao, valor = :valor, vencimento = '$data_venc', arquivo = '$foto' where id = '$id'");
	
}


$res->bindValue(":descricao", "$descricao");
$res->bindValue(":valor", "$valor");
$res->execute();


$result = json_encode(array('mensagem'=>'Salvo com sucesso!', 'sucesso'=>true));

echo $result;

?>