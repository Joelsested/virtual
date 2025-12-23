<?php 
include_once('../conexao.php');

$postjson = json_decode(file_get_contents('php://input'), true);

$id_mat = @$postjson['id'];
$subtotal = @$postjson['valor'];
$forma_pgto = @$postjson['pgto'];
$obs = @$postjson['obs'];
$subtotal = str_replace(',', '.', $subtotal);

$query = $pdo->prepare("UPDATE matriculas SET total_recebido = :total_recebido, forma_pgto = :forma_pgto, obs = :obs WHERE id = '$id_mat'");

$query->bindValue(":total_recebido", "$subtotal");
$query->bindValue(":forma_pgto", "$forma_pgto");
$query->bindValue(":obs", "$obs");
$query->execute();


$result = json_encode(array('mensagem'=>'Editada com sucesso!', 'sucesso'=>true));

echo $result;

?>