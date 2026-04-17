<?php
require_once(__DIR__ . "/../../../conexao.php");
require_once(__DIR__ . "/../../../../helpers.php");
$tabela = 'secretarios';

$nome = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$telefone = $_POST['telefone'];
$cpf = trim($_POST['cpf'] ?? '');
$endereco = $_POST['endereco'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$sexo = $_POST['sexo'];
$nascimento = trim($_POST['nascimento'] ?? '');
$id = $_POST['id'];

$wallet_id = trim($_POST['wallet_id'] ?? '');

if ($nome === '') {
    echo 'Informe o nome.';
    exit();
}
if ($cpf === '') {
    echo 'Informe o CPF.';
    exit();
}
if ($email === '') {
    echo 'Informe o email.';
    exit();
}
if ($nascimento === '') {
    echo 'Informe a data de nascimento.';
    exit();
}
if ($wallet_id === '') {
    echo 'Informe o wallet_id.';
    exit();
}

$hasWalletColumn = false;
try {
    $stmtCol = $pdo->prepare("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'usuarios' AND COLUMN_NAME = 'wallet_id' LIMIT 1");
    $stmtCol->execute();
    $hasWalletColumn = (bool) $stmtCol->fetchColumn();
} catch (Exception $e) {
    $hasWalletColumn = false;
}

$hasNascimentoColumn = false;
try {
    $stmtCol = $pdo->prepare("SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'secretarios' AND COLUMN_NAME = 'nascimento' LIMIT 1");
    $stmtCol->execute();
    $hasNascimentoColumn = (bool) $stmtCol->fetchColumn();
} catch (Exception $e) {
    $hasNascimentoColumn = false;
}

$senha = birthDigits($nascimento);
if ($senha === '') {
    echo 'Informe uma data de nascimento valida!';
    exit();
}
$senha_crip = md5($senha);
$nascimentoBr = formatDateBr($nascimento);

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

$caminho = '../../../painel-aluno/img/perfil/' . $nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name'];

if (@$_FILES['foto']['name'] != "") {
    $ext = pathinfo($nome_img, PATHINFO_EXTENSION);
    if ($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif') {

        //EXCLUO A FOTO ANTERIOR
        if ($foto != "sem-perfil.jpg") {
            @unlink('../../../painel-secretario/img/perfil/' . $foto);
        }

        $foto = $nome_img;

        move_uploaded_file($imagem_temp, $caminho);
    } else {
        echo 'Extensão de Imagem não permitida!';
        exit();
    }
}


if ($id == "") {

    if ($hasNascimentoColumn) {
        $query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, nascimento = :nascimento, endereco = :endereco,  cidade = :cidade, estado = :estado, sexo = :sexo, foto = '$foto', ativo = 'Sim', data = curDate()");
        $query->bindValue(":nascimento", "$nascimentoBr");
    } else {
        $query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, endereco = :endereco,  cidade = :cidade, estado = :estado, sexo = :sexo, foto = '$foto', ativo = 'Sim', data = curDate()");
    }
    $query->bindValue(":nome", "$nome");
    $query->bindValue(":email", "$email");
    $query->bindValue(":telefone", "$telefone");
    $query->bindValue(":cpf", "$cpf");
    $query->bindValue(":endereco", "$endereco");
    $query->bindValue(":cidade", "$cidade");
    $query->bindValue(":estado", "$estado");
    $query->bindValue(":sexo", "$sexo");
    $query->execute();
    $ult_id = $pdo->lastInsertId();

    if ($hasWalletColumn) {
        $query = $pdo->prepare("INSERT INTO usuarios SET wallet_id = :wallet_id, nome = :nome, usuario = :email, senha = '', cpf = :cpf, senha_crip = :senha_crip, nivel = 'Secretario',  foto = '$foto', id_pessoa = '$ult_id', ativo = 'Sim', data = curDate()");
        $query->bindValue(":wallet_id", "$wallet_id");
    } else {
        $query = $pdo->prepare("INSERT INTO usuarios SET nome = :nome, usuario = :email, senha = '', cpf = :cpf, senha_crip = :senha_crip, nivel = 'Secretario',  foto = '$foto', id_pessoa = '$ult_id', ativo = 'Sim', data = curDate()");
    }

    $query->bindValue(":nome", "$nome");
    $query->bindValue(":email", "$email");
    $query->bindValue(":cpf", "$cpf");
    $query->bindValue(":senha_crip", "$senha_crip");
    $query->execute();

} else {
    if ($hasNascimentoColumn) {
        $query = $pdo->prepare("UPDATE $tabela SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, nascimento = :nascimento, endereco = :endereco,  cidade = :cidade, estado = :estado, sexo = :sexo, foto = '$foto' WHERE id = '$id'");
        $query->bindValue(":nascimento", "$nascimentoBr");
    } else {
        $query = $pdo->prepare("UPDATE $tabela SET nome = :nome, email = :email, cpf = :cpf, telefone = :telefone, endereco = :endereco,  cidade = :cidade, estado = :estado, sexo = :sexo, foto = '$foto' WHERE id = '$id'");
    }
    $query->bindValue(":nome", "$nome");
    $query->bindValue(":email", "$email");
    $query->bindValue(":telefone", "$telefone");
    $query->bindValue(":cpf", "$cpf");
    $query->bindValue(":endereco", "$endereco");
    $query->bindValue(":cidade", "$cidade");
    $query->bindValue(":estado", "$estado");
    $query->bindValue(":sexo", "$sexo");
    $query->execute();
    $ult_id = $pdo->lastInsertId();

    if ($hasWalletColumn) {
        $query = $pdo->prepare("UPDATE usuarios SET wallet_id = :wallet_id, nome = :nome, usuario = :email, cpf = :cpf, senha = '', senha_crip = :senha_crip, foto = '$foto' WHERE id_pessoa = '$id' and nivel = 'Secretario'");
        $query->bindValue(":wallet_id", "$wallet_id");
    } else {
        $query = $pdo->prepare("UPDATE usuarios SET nome = :nome, usuario = :email, cpf = :cpf, senha = '', senha_crip = :senha_crip, foto = '$foto' WHERE id_pessoa = '$id' and nivel = 'Secretario'");
    }

    $query->bindValue(":nome", "$nome");
    $query->bindValue(":email", "$email");
    $query->bindValue(":cpf", "$cpf");
    $query->bindValue(":senha_crip", "$senha_crip");
    $query->execute();
}


echo 'Salvo com Sucesso';

?>
