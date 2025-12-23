<?php
require_once('../../sistema/conexao.php');

$email = $_POST['email'];

if($email == ""){
    echo 'Preencha o Campo Email!';
    exit();
}

$res = $pdo->query("SELECT * FROM emails where email = '$email'"); 
$dados = $res->fetchAll(PDO::FETCH_ASSOC);

if(@count($dados) > 0){
    
    $pdo->query("UPDATE emails SET enviar = 'não' where email = '$email'"); 
    echo 'Descadastrado da Lista com Sucesso!';
}else{
   echo 'Este email não está cadastrado!';

}

?>