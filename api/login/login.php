<?php 
include_once('../conexao.php');

$postjson = json_decode(file_get_contents("php://input"), true);
$senha = $postjson['senha'];
$senha_crip = md5($postjson['senha']);
$query_buscar = $pdo->prepare("SELECT * from usuarios where (usuario = :usuario or cpf = :usuario) and senha_crip = :senha ");
$query_buscar->bindValue(":usuario", "$postjson[email]");
$query_buscar->bindValue(":senha", "$senha_crip");
$query_buscar->execute();

$nivel = '';

$dados_buscar = $query_buscar->fetchAll(PDO::FETCH_ASSOC);

for ($i=0; $i < count($dados_buscar); $i++) { 
    foreach ($dados_buscar[$i] as $key => $value) {
    }
    $id_user = $dados_buscar[$i]['id'];
    $nivel = $dados_buscar[$i]['nivel'];

    $dados[] = array(
        'id' => intVal($dados_buscar[$i]['id']),
        'nome' => $dados_buscar[$i]['nome'],  
        'email' => $dados_buscar[$i]['usuario'],  
        'nivel' => $dados_buscar[$i]['nivel'],  
    );
}

if(@$nivel == 'Administrador'){
    $result = json_encode(array('result'=>$dados));
    echo $result;  

}else{
    $result = json_encode(array('result'=>'Dados Incorretos!'));
 	echo $result;
}

?>