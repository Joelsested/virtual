<?php 
require_once("../../../conexao.php");
$tabela = 'secretarios';

$id = $_POST['id'];

$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
$foto = $res[0]['foto'];
if($foto != "sem-perfil.jpg"){
	@unlink('../../../painel-secretario/img/perfil/'.$foto);
}

$pdo->query("DELETE FROM $tabela where id = '$id'");
$pdo->query("DELETE FROM usuarios where id_pessoa = '$id' and nivel = 'secretario'");

echo 'Excluído com Sucesso';

?>