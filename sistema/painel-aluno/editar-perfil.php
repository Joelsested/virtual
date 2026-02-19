<?php 
require_once("../conexao.php");
require_once(__DIR__ . "/../../config/upload.php");
require_once(__DIR__ . "/../../helpers.php");

$nome = trim($_POST['nome_usu'] ?? '');
$cpf = trim($_POST['cpf_usu'] ?? '');
$email = trim($_POST['email_usu'] ?? '');
$id = (int) ($_POST['id_usu'] ?? 0);
$foto = $_POST['foto_usu'] ?? '';

$rg = trim($_POST['rg_usu'] ?? '');
$orgao_expedidor = trim($_POST['expedidor_usu'] ?? '');
$expedicao = trim($_POST['expedicao_usu'] ?? '');
$nascimento = trim($_POST['nascimento_usu'] ?? '');
$telefone = trim($_POST['telefone_usu'] ?? '');
$cep = trim($_POST['cep_usu'] ?? '');
$sexo = trim($_POST['sexo_usu'] ?? '');
$endereco = trim($_POST['endereco_usu'] ?? '');
$numero = trim($_POST['numero_usu'] ?? '');
$bairro = trim($_POST['bairro_usu'] ?? '');
$cidade = trim($_POST['cidade_usu'] ?? '');
$estado = trim($_POST['estado_usu'] ?? '');
$mae = trim($_POST['mae_usu'] ?? '');
$pai = trim($_POST['pai_usu'] ?? '');
$naturalidade = trim($_POST['naturalidade_usu'] ?? '');

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
if ($telefone === '') {
	echo 'Informe o telefone.';
	exit();
}
if ($nascimento === '') {
	echo 'Informe a data de nascimento.';
	exit();
}
$cpfDigits = digitsOnly($cpf);
if ($cpfDigits === '') {
	echo 'CPF invalido!';
	exit();
}

$senha = birthDigits($nascimento);
if ($senha === '') {
	echo 'Data de nascimento invalida!';
	exit();
}
$senha_crip = md5($senha);



$query = $pdo->prepare("SELECT * FROM usuarios where id = :id");
$query->execute([':id' => $id]);
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$id_pessoa = $res[0]['id_pessoa'];

//validar email duplicado
$stmtEmail = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :usuario AND id <> :id LIMIT 1");
$stmtEmail->execute([':usuario' => $email, ':id' => $id]);
if ($stmtEmail->fetchColumn()) {
	echo 'Email ja Cadastrado, escolha Outro!';
	exit();
}

//validar cpf duplicado
if ($cpfDigits !== '') {
	$cpfColumn = cleanCpfColumn('cpf');
	$stmtCpf = $pdo->prepare("SELECT id FROM usuarios WHERE $cpfColumn = :cpf_digits AND id <> :id LIMIT 1");
	$stmtCpf->execute([':cpf_digits' => $cpfDigits, ':id' => $id]);
	if ($stmtCpf->fetchColumn()) {
		echo 'CPF ja Cadastrado, escolha Outro!';
		exit();
	}
}





//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$destDir = __DIR__ . '/img/perfil';
$allowedExt = ['png', 'jpg', 'jpeg', 'gif', 'webp'];
$allowedMime = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$upload = upload_handle($_FILES['foto'] ?? [], $destDir, $allowedExt, $allowedMime, 5 * 1024 * 1024, date('Y-m-d-H-i-s') . '-', true);
if (!$upload['ok']) {
	echo $upload['error'];
	exit();
}
if (empty($upload['skipped'])) {
	if ($foto != 'sem-perfil.jpg') {
		@unlink($destDir . '/' . $foto);
	}
	$foto = $upload['filename'];
}


//atualizar os dados do usuário
$query = $pdo->prepare("UPDATE usuarios SET nome = :nome, cpf = :cpf, usuario = :usuario, senha = :senha, senha_crip = :senha_crip, foto = :foto where id = :id");

$query->bindValue(":nome", "$nome");
$query->bindValue(":usuario", "$email");
$query->bindValue(":cpf", "$cpf");
$query->bindValue(":senha", "");
$query->bindValue(":senha_crip", "$senha_crip");
$query->bindValue(":foto", "$foto");
$query->bindValue(":id", $id, PDO::PARAM_INT);
$query->execute();


$query = $pdo->prepare("UPDATE alunos SET nome = :nome, cpf = :cpf, email = :email, telefone = :telefone, rg = :rg, orgao_expedidor = :orgao_expedidor, expedicao = :expedicao,  nascimento = :nascimento, cep = :cep, sexo = :sexo, endereco = :endereco, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado, mae = :mae, pai = :pai, naturalidade = :naturalidade where id = :id");

$query->bindValue(":nome", "$nome");
$query->bindValue(":cpf", "$cpf");
$query->bindValue(":email", "$email");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":rg", "$rg");
$query->bindValue(":orgao_expedidor", "$orgao_expedidor");
$query->bindValue(":expedicao", "$expedicao");
$query->bindValue(":nascimento", "$nascimento");
$query->bindValue(":cep", "$cep");
$query->bindValue(":sexo", "$sexo");
$query->bindValue(":endereco", "$endereco");
$query->bindValue(":numero", "$numero");
$query->bindValue(":bairro", "$bairro");
$query->bindValue(":cidade", "$cidade");
$query->bindValue(":estado", "$estado");
$query->bindValue(":mae", "$mae");
$query->bindValue(":pai", "$pai");
$query->bindValue(":naturalidade", "$naturalidade");
$query->bindValue(":id", $id_pessoa, PDO::PARAM_INT);
$query->execute();

echo 'Editado com Sucesso';

?>