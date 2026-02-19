<?php
require_once("../conexao.php");

$nome = $_POST['nome_usu'] ?? '';
$email = $_POST['email_usu'] ?? '';
$cpf = $_POST['cpf_usu'] ?? '';
$id = $_POST['id_usu'] ?? '';
$foto = $_POST['foto_usu'] ?? 'sem-perfil.jpg';

$query = $pdo->query("SELECT * FROM usuarios where usuario = '$email'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0 and $res[0]['id'] != $id) {
    echo 'Email jÃ¡ cadastrado, escolha outro!';
    exit();
}

$query = $pdo->query("SELECT * FROM usuarios where cpf = '$cpf'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if ($total_reg > 0 and $res[0]['id'] != $id) {
    echo 'CPF jÃ¡ cadastrado, escolha outro!';
    exit();
}

$nome_img = date('d-m-Y H:i:s') . '-' . (@$_FILES['foto']['name'] ?? '');
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);
$caminho = 'img/perfil/' . $nome_img;
$imagem_temp = @$_FILES['foto']['tmp_name'];

if (@$_FILES['foto']['name'] != "") {
    $ext = pathinfo($nome_img, PATHINFO_EXTENSION);
    if ($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif' or $ext == 'webp') {
        if ($foto != "sem-perfil.jpg") {
            @unlink('img/perfil/' . $foto);
        }
        $foto = $nome_img;
        move_uploaded_file($imagem_temp, $caminho);
    } else {
        echo 'ExtensÃ£o de imagem nÃ£o permitida!';
        exit();
    }
}

$query = $pdo->prepare("UPDATE usuarios SET nome = :nome, cpf = :cpf, usuario = :usuario, foto = '$foto' where id = '$id'");
$query->bindValue(":nome", "$nome");
$query->bindValue(":usuario", "$email");
$query->bindValue(":cpf", "$cpf");
$query->execute();

function tabela_pessoa_por_nivel(string $nivel) : string {
    $map = ['Administrador' => 'administradores', 'Professor' => 'professores', 'Tutor' => 'tutores', 'Vendedor' => 'vendedores', 'Secretario' => 'secretarios', 'Tesoureiro' => 'tesoureiros', 'Parceiro' => 'parceiros', 'Assessor' => 'assessores', 'Aluno' => 'alunos',
    ];
    return $map[$nivel] ?? '';
}

function atualizar_pessoa(PDO $pdo, string $tabela, int $idPessoa, array $dados) : void {
    if ($tabela === '' || $idPessoa <= 0) {
        return;
    }

    try {
        $cols = $pdo->query("SHOW COLUMNS FROM `$tabela`")->fetchAll(PDO::FETCH_COLUMN, 0);
        if (!$cols) {
            return;
        }

        $colsSet = [];
        $params = [':id' => $idPessoa];
        foreach ($dados as $campo => $valor) {
            if (in_array($campo, $cols, true)) {
                $colsSet[] = "`$campo` = :$campo";
                $params[":$campo"] = $valor;
            }
        }

        if (!$colsSet) {
            return;
        }

        $sql = "UPDATE `$tabela` SET " . implode(', ', $colsSet) . " WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
    } catch (Exception $e) {
        // Nao interrompe edicao do usuario principal.
    }
}

$query = $pdo->query("SELECT * FROM usuarios where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$nivel = $res[0]['nivel'] ?? '';
$id_pessoa = (int)($res[0]['id_pessoa'] ?? 0);
$tabela = tabela_pessoa_por_nivel((string)$nivel);

atualizar_pessoa($pdo, $tabela, $id_pessoa, ['nome' => $nome, 'cpf' => $cpf, 'email' => $email, 'foto' => $foto,
]);

echo 'Editado com Sucesso';

?>