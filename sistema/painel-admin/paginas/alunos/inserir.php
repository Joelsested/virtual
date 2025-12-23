<?php 
require_once("../../../conexao.php");
@session_start();

$id_user = @$_SESSION['id'];
$tabela = 'alunos';

$nome = $_POST['nome'];
$cpf = $_POST['cpf'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$rg = $_POST['rg'];
$expedicao = $_POST['expedicao'];
$nascimento = $_POST['nascimento'];
$cep = $_POST['cep'];
$sexo = $_POST['sexo'];
$endereco = $_POST['endereco'];
$numero = $_POST['numero'];
$bairro = $_POST['bairro'];
$cidade = $_POST['cidade'];
$estado = $_POST['estado'];
$mae = $_POST['mae'];
$pai = $_POST['pai'];
$naturalidade = $_POST['naturalidade'];
$id = $_POST['id'];

$senha = '123456';
$senha_crip = md5($senha);

//validar email duplicado
$query = $pdo->query("SELECT * FROM $tabela where email = '$email'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $res[0]['id'] != $id){
	echo 'Email já Cadastrado, escolha Outro!';
	exit();
}


//validar cpf duplicado
$query = $pdo->query("SELECT * FROM $tabela where cpf = '$cpf'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0 and $res[0]['id'] != $id){
	echo 'CPF já Cadastrado, escolha Outro!';
	exit();
}


$query = $pdo->query("SELECT * FROM $tabela where id = '$id'");
$res = $query->fetchAll(PDO::FETCH_ASSOC);
$total_reg = @count($res);
if($total_reg > 0){
	$foto = $res[0]['foto'];
}else{
	$foto = 'sem-perfil.jpg';
}


//SCRIPT PARA SUBIR FOTO NO SERVIDOR
$nome_img = date('d-m-Y H:i:s') .'-'.@$_FILES['foto']['name'];
$nome_img = preg_replace('/[ :]+/' , '-' , $nome_img);

$caminho = '../../../painel-aluno/img/perfil/' .$nome_img;

$imagem_temp = @$_FILES['foto']['tmp_name']; 

if(@$_FILES['foto']['name'] != ""){
	$ext = pathinfo($nome_img, PATHINFO_EXTENSION);   
	if($ext == 'png' or $ext == 'jpg' or $ext == 'jpeg' or $ext == 'gif'){ 
	
			//EXCLUO A FOTO ANTERIOR
			if($foto != "sem-perfil.jpg"){
				@unlink('../../../painel-aluno/img/perfil/'.$foto);
			}

			$foto = $nome_img;
		
		move_uploaded_file($imagem_temp, $caminho);
	}else{
		echo 'Extensão de Imagem não permitida!';
		exit();
	}
}


if($id == ""){

	$query = $pdo->prepare("INSERT INTO $tabela SET nome = :nome, cpf = :cpf, email = :email, telefone = :telefone, rg = :rg, expedicao = :expedicao,  nascimento = :nascimento, cep = :cep, sexo = :sexo, endereco = :endereco, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado,  mae = :mae, pai = :pai, naturalidade = :naturalidade, foto = '$foto', ativo = 'Sim', usuario = '$id_user', data = curDate()");


$query->bindValue(":nome", "$nome");
$query->bindValue(":cpf", "$cpf");
$query->bindValue(":email", "$email");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":rg", "$rg");
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
$query->execute();
$ult_id = $pdo->lastInsertId();

$query = $pdo->prepare("INSERT INTO usuarios SET nome = :nome, usuario = :email, senha = '$senha', cpf = :cpf, senha_crip = '$senha_crip', nivel = 'Aluno',  foto = '$foto', id_pessoa = '$ult_id', ativo = 'Sim', data = curDate()");

$query->bindValue(":nome", "$nome");
$query->bindValue(":email", "$email");
$query->bindValue(":cpf", "$cpf");
$query->execute();

}else{
	 $query = $pdo->prepare("UPDATE $tabela SET nome = :nome, cpf = :cpf, email = :email, telefone = :telefone, rg = :rg, expedicao = :expedicao,  nascimento = :nascimento, cep = :cep, sexo = :sexo, endereco = :endereco, numero = :numero, bairro = :bairro, cidade = :cidade, estado = :estado,  mae = :mae, pai = :pai, naturalidade = :naturalidade, foto = '$foto' WHERE id = '$id'");

$query->bindValue(":nome", "$nome");
$query->bindValue(":cpf", "$cpf");
$query->bindValue(":email", "$email");
$query->bindValue(":telefone", "$telefone");
$query->bindValue(":rg", "$rg");
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
$query->execute();
$ult_id = $pdo->lastInsertId();

$query = $pdo->prepare("UPDATE usuarios SET nome = :nome, usuario = :email, cpf = :cpf, foto = '$foto' WHERE id_pessoa = '$id' and nivel = 'Aluno'");

$query->bindValue(":nome", "$nome");
$query->bindValue(":cpf", "$cpf");
$query->bindValue(":email", "$email");
$query->execute();
}




echo 'Salvo com Sucesso';

 ?>