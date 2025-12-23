<?php
require_once("../../../conexao.php");
$tabela = 'administradores';

$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$comissao = $_POST['comissao'] ?? null;
$cpf = $_POST['cpf'];
$id = $_POST['id'];

$wallet_id = $_POST['wallet_id'];

$senha = '123456';
$senha_crip = md5($senha);

//validar email duplicado
$query = $pdo->query("SELECT * FROM $tabela where email = '$email'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0 and $res[0]['id'] != $id) {
    echo 'Email já Cadastrado, escolha Outro!';
    exit();
}


//validar cpf duplicado
$query = $pdo->query("SELECT * FROM $tabela where cpf = '$cpf'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0 and $res[0]['id'] != $id) {
    echo 'CPF já Cadastrado, escolha Outro!';
    exit();
}


$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0) {
    $foto = $res[0]['foto'];
} else {
    $foto = 'sem-perfil.jpg';
}


//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') . '-' . @$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/', '-', $nome_img);

$caminho = '../../img/perfil/' . $nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name'];

if (@$_FILES['foto']['name'] != "") {
    $ext = pathinfo($nome_img, PATHINFO_EXTENSION);
    if ($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif') {

        //EXCLUO A FOTO ANTERIOR
        if ($foto != "sem-perfil.jpg") {
            @unlink('../../img/perfil/' . $foto);
        }

        $foto = $nome_img;

        move_uploaded_file($imagem_temp, $caminho);
    } else {
        echo 'Extensão de Imagem não permitida!';
        exit();
    }
}


if ($id == "") {

    $comissao = isset($_POST['comissao']) && $_POST['comissao'] !== '' ? $_POST['comissao'] : null;

    // Se comissão for null, buscar na tabela comissao
    if ($comissao === null) {
        $query = $pdo->prepare("SELECT porcentagem FROM comissoes WHERE nivel = 'Administrador' LIMIT 1");
        $query->execute();
        $resultado = $query->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $comissao = $resultado['porcentagem'];
        } else {
            $comissao = 0; // Definir um valor padrão caso nada seja encontrado
        }
    }

    $query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, comissao = :comissao, foto = '$foto', ativo = 'Sim', data = curDate()");
    $query->bindValue(":nome", "$nome");
    $query->bindValue(":email", "$email");
    $query->bindValue(":telefone", "$telefone");
    $query->bindValue(":comissao", $comissao);
    $query->bindValue(":cpf", "$cpf");
    $query->execute();
    $ult_id = $pdo->lastInsertId();

    $query = $pdo->prepare("INSERT INTO usuarios SET wallet_id = :wallet_id, nome = :nome, usuario = :email, senha = '$senha', cpf = :cpf, senha_crip = '$senha_crip', nivel = 'Administrador',  foto = '$foto', id_pessoa = '$ult_id', ativo = 'Sim', data = curDate()");

    $query->bindValue(":nome", "$nome");
    $query->bindValue(":email", "$email");
    $query->bindValue(":cpf", "$cpf");
    $query->bindValue(":wallet_id", "$wallet_id");
    $query->execute();
} else {

    // Se comissão for null, buscar na tabela comissao
    if ($comissao === null) {
        $query = $pdo->prepare("SELECT porcentagem FROM comissoes WHERE nivel = 'Administrador' LIMIT 1");
        $query->execute();
        $resultado = $query->fetch(PDO::FETCH_ASSOC);

        if ($resultado) {
            $comissao = $resultado['porcentagem'];
        } else {
            $comissao = 0; // Definir um valor padrão caso nada seja encontrado
        }
    }

    $query = $pdo->prepare("UPDATE $tabela SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, comissao = :comissao, foto = '$foto' WHERE id = '$id'");
    $query->bindValue(":nome", "$nome");
    $query->bindValue(":email", "$email");
    $query->bindValue(":telefone", "$telefone");
    $query->bindValue(":comissao", "$comissao");
    $query->bindValue(":cpf", "$cpf");
    $query->execute();
    $ult_id = $pdo->lastInsertId();

    $query = $pdo->prepare("UPDATE usuarios SET wallet_id = :wallet_id, nome = :nome, usuario = :email, cpf = :cpf, foto = '$foto' WHERE id_pessoa = '$id' and nivel = 'Administrador'");

    $query->bindValue(":nome", "$nome");
    $query->bindValue(":email", "$email");
    $query->bindValue(":cpf", "$cpf");
    $query->bindValue(":wallet_id", "$wallet_id");
    $query->execute();
}


echo 'Salvo com Sucesso';
